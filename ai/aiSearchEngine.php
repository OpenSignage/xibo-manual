<?php
/*
 * Copyright (C) 2025 Open Source Digital Signage Initiative.
 */

// Enable error display
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Extend PHP execution time limit (300 seconds)
set_time_limit(300);

// Increase memory limit (512MB)
ini_set('memory_limit', '512M');

// Timeout and buffering settings
ini_set('default_socket_timeout', 300);
ini_set('max_execution_time', 300);
ignore_user_abort(true);

// Debug log file - UTF-8 encoding
$logFile = __DIR__ . '/debug.log';
$logDir = dirname($logFile);

// Check log directory
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// Check write permissions for log directory
if (!is_writable($logDir)) {
    error_log('Warning: Log directory is not writable: ' . $logDir);
}

/**
 * Simple logging function
 * @param mixed $message Message to log
 */
function debug_log($message) {
    global $logFile;
    $log_entry = date('[Y-m-d H:i:s] ') . print_r($message, true) . "\n";
    
    try {
        file_put_contents($logFile, $log_entry, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        error_log('Debug log write error: ' . $e->getMessage());
    }
}

// Check for Composer autoloader
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    $errorMessage = 'Composer autoload file not found. Please run `composer install`.';
    die($errorMessage);
}
require 'vendor/autoload.php';

// Import required classes
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Core\Exception\GoogleException;

// Gemini API設定
$GEMINI_API_KEY = '';
$GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
$MANUAL_DATA_PATH = __DIR__ . '/learning_data.json';
$SYSTEM_PROMPT_PATH = __DIR__ . '/system_prompt.txt';

// システムプロンプトの読み込み
function loadSystemPrompt() {
    global $SYSTEM_PROMPT_PATH;
    
    try {
        if (!file_exists($SYSTEM_PROMPT_PATH)) {
            throw new Exception('システムプロンプトファイルが見つかりません: ' . $SYSTEM_PROMPT_PATH);
        }
        
        $prompt = file_get_contents($SYSTEM_PROMPT_PATH);
        if ($prompt === false) {
            throw new Exception('システムプロンプトの読み込みに失敗しました');
        }
        
        return trim($prompt);
    } catch (Exception $e) {
        debug_log('システムプロンプト読み込みエラー: ' . $e->getMessage());
        // デフォルトのプロンプトを返す
        return <<<EOT
あなたはXiboデジタルサイネージシステムのマニュアルアシスタントです。
以下の指針に従って応答してください：

1. マニュアルの内容に基づいて正確な情報を提供してください。
2. 技術的な説明は簡潔かつ分かりやすく行ってください。
3. 不明な点がある場合は、その旨を明確に伝えてください。
4. 必要に応じて、関連する設定手順やトラブルシューティングの方法を提案してください。
5. 回答は日本語で提供してください。

ユーザーからの質問に対して、上記の指針に基づいて回答を生成してください。
EOT;
    }
}

$SYSTEM_PROMPT = loadSystemPrompt();

/**
 * マニュアルデータを読み込む
 * @return array|null マニュアルデータ
 */
function loadManualData() {
    global $MANUAL_DATA_PATH;
    
    try {
        if (!file_exists($MANUAL_DATA_PATH)) {
            throw new Exception('マニュアルデータファイルが見つかりません: ' . $MANUAL_DATA_PATH);
        }
        
        $jsonData = file_get_contents($MANUAL_DATA_PATH);
        if ($jsonData === false) {
            throw new Exception('マニュアルデータの読み込みに失敗しました');
        }
        
        $manualData = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('マニュアルデータのJSONパースに失敗: ' . json_last_error_msg());
        }
        
        return $manualData;
    } catch (Exception $e) {
        debug_log('マニュアルデータ読み込みエラー: ' . $e->getMessage());
        return null;
    }
}

/**
 * 質問に関連するマニュアルコンテンツを検索
 * @param string $query ユーザーの質問
 * @param array $manualData マニュアルデータ
 * @return array 関連するコンテキストとURL
 */
function findRelevantContext($query, $manualData) {
    if (empty($manualData)) {
        return ['context' => '', 'urls' => []];
    }
    
    $relevantSections = [];
    $queryKeywords = extractKeywords($query);
    
    foreach ($manualData as $section) {
        // セクションの関連性をスコアリング
        $score = 0;
        $content = isset($section['content']) ? $section['content'] : '';
        $title = isset($section['title']) ? $section['title'] : '';
        $url = isset($section['url']) ? $section['url'] : '';
        
        foreach ($queryKeywords as $keyword) {
            // タイトルに含まれる場合は高いスコア
            if (mb_stripos($title, $keyword) !== false) {
                $score += 2;
            }
            // コンテンツに含まれる場合はスコア加算
            if (mb_stripos($content, $keyword) !== false) {
                $score += 1;
            }
        }
        
        if ($score > 0) {
            $relevantSections[] = [
                'content' => $title . "\n" . $content,
                'url' => $url,
                'score' => $score
            ];
        }
    }
    
    // スコアで降順ソート
    usort($relevantSections, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    // 上位3つのセクションを結合
    $context = array_slice($relevantSections, 0, 3);
    $contextText = '';
    $urls = [];
    
    foreach ($context as $section) {
        $contextText .= $section['content'] . "\n\n";
        if (!empty($section['url'])) {
            $urls[] = $section['url'];
        }
    }
    
    return [
        'context' => $contextText,
        'urls' => array_unique($urls)
    ];
}

/**
 * Main function to query Gemini AI and stream results
 * @param string $userPrompt User query
 * @param array $config Configuration parameters
 * @param string $credentialsPath Path to credentials file
 */
function queryGeminiAIStreaming($userPrompt, $config, $credentialsPath) {
    global $GEMINI_API_KEY, $GEMINI_API_URL, $SYSTEM_PROMPT;
    
    try {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header('Content-Type: text/event-stream; charset=UTF-8');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');
        }

        // 接続確認メッセージを送信
        debug_log("Connected. Waiting for Gemini response...");
        echo "event: status\n";
        echo "data: " . json_encode(['status' => 'connected', 'message' => '接続完了。Gemini AI準備中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();

        // APIキーの取得
        if (empty($GEMINI_API_KEY)) {
            $apiKeyFile = __DIR__ . '/gemini_api_key.txt';
            if (!file_exists($apiKeyFile)) {
                throw new Exception('Gemini API key file not found');
            }
            $GEMINI_API_KEY = trim(file_get_contents($apiKeyFile));
            if (empty($GEMINI_API_KEY)) {
                throw new Exception('Gemini API key is empty');
            }
        }

        // マニュアルデータの読み込みと関連コンテキストの検索
        $manualData = loadManualData();
        $relevantData = findRelevantContext($userPrompt, $manualData);
        $relevantContext = $relevantData['context'];
        $relevantUrls = $relevantData['urls'];
        
        // コンテキスト付きのプロンプト作成
        $contextPrompt = empty($relevantContext) ? $userPrompt : 
            "以下のマニュアル情報を参考に質問に答えてください：\n\n" .
            "=== マニュアル情報 ===\n" .
            $relevantContext .
            "\n=== ユーザーの質問 ===\n" .
            $userPrompt;

        // Gemini APIリクエストの準備
        $requestData = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $SYSTEM_PROMPT
                        ]
                    ],
                    'role' => 'system'
                ],
                [
                    'parts' => [
                        [
                            'text' => $contextPrompt
                        ]
                    ],
                    'role' => 'user'
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        // APIリクエストの実行
        $ch = curl_init($GEMINI_API_URL . '?key=' . $GEMINI_API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // 処理状態の通知
        echo "event: status\n";
        echo "data: " . json_encode(['status' => 'processing', 'message' => 'Gemini AIに問い合わせ中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();

        // レスポンスの取得と処理
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Gemini API error: HTTP ' . $httpCode);
        }

        $responseData = json_decode($response, true);
        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Invalid response format from Gemini API');
        }

        $answer = $responseData['candidates'][0]['content']['parts'][0]['text'];
        
        // 応答の整形と送信
        $formattedAnswer = improveTextReadability($answer);
        
        echo "event: answer\n";
        echo "data: " . json_encode([
            'answer' => $formattedAnswer,
            'relevance_score' => 1.0,
            'page_urls' => $relevantUrls
        ], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();

        // 完了通知
        echo "event: status\n";
        echo "data: " . json_encode(['status' => 'completed'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();

    } catch (Exception $e) {
        $errorMsg = 'Error: ' . $e->getMessage();
        debug_log($errorMsg);
        
        echo "event: error\n";
        echo "data: " . json_encode(['error' => 'Gemini AIでエラーが発生しました: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();
    }
}

// Process request
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $json_data = file_get_contents('php://input');
    
    $post_data = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        debug_log('JSONデータの解析に失敗: ' . json_last_error_msg());
        echo "event: error\n";
        echo "data: " . json_encode(['error' => 'リクエストデータの形式が不正です。'], JSON_UNESCAPED_UNICODE) . "\n\n";
        die();
    }
    
    $userPrompt = $post_data['question'] ?? '';
    
    if (empty($userPrompt)) {
        debug_log('空の質問が送信されました');
        echo "event: error\n";
        echo "data: " . json_encode(['error' => '質問が入力されていません。'], JSON_UNESCAPED_UNICODE) . "\n\n";
        die();
    }

    // Load configuration file
    $configPath = __DIR__ . '/config.php';
    if (!file_exists($configPath)) {
        debug_log('設定ファイルが見つかりません: ' . $configPath);
        die('Configuration file not found');
    }
    $config = require $configPath;

    // Call main function with Gemini
    queryGeminiAIStreaming($userPrompt, $config, $credentialsPath);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    echo "This endpoint only accepts POST requests.";
}

/**
 * Preprocess query for optimization
 * @param string $userPrompt Raw user query
 * @return string Optimized query
 */
function preprocessQuery($userPrompt) {
    // Summarize long queries
    if (mb_strlen($userPrompt) > 200) {
        // Extract important parts for long queries
        $keywords = extractKeywords($userPrompt);
        return $keywords;
    }
    
    // Process special characters
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\.\,\?\!]/u', ' ', $userPrompt);
    
    return trim($cleaned);
}

/**
 * Extract keywords from text
 * @param string $text Text to extract keywords from
 * @return string Space-separated keywords
 */
function extractKeywords($text) {
    // Basic keyword extraction
    // List of stopwords (common Japanese and English words)
    $stopWords = ['の', 'に', 'は', 'を', 'た', 'が', 'で', 'て', 'と', 'し', 'れ', 'さ', 'ある', 'いる', 'する', 'から', 'など', 'the', 'is', 'at', 'which', 'on', 'a', 'an', 'and', 'or', 'for', 'in', 'to', 'that', 'this', 'with'];
    
    // Split text into words (ideally morphological analysis for Japanese, but simple space splitting here)
    $words = preg_split('/[\s\.\,\?\!]+/u', $text);
    
    // Filter out stopwords and short words
    $keywords = [];
    foreach ($words as $word) {
        $word = trim($word);
        if (!empty($word) && mb_strlen($word) > 1 && !in_array(mb_strtolower($word), $stopWords)) {
            $keywords[] = $word;
        }
    }
    
    // Remove duplicates
    $keywords = array_unique($keywords);
    
    // Limit to maximum 10 keywords
    $keywords = array_slice($keywords, 0, 10);
    
    return implode(' ', $keywords);
}

/**
 * Calculate relevance score between query and document
 * @param string $query Search query
 * @param object $document Document object
 * @param object $searchResult Search result object
 * @return float Relevance score (0.0-1.0)
 */
function calculateRelevanceScore($query, $document, $searchResult) {
    // Use API score if available
    if (method_exists($searchResult, 'getRelevanceScore')) {
        $apiScore = $searchResult->getRelevanceScore();
        if ($apiScore > 0) {
            return $apiScore;
        }
    }
    
    // Calculate our own score if API score not available
    $docText = '';
    $structData = null;
    
    // Try to extract text from structData
    $_structDataProto = $document->getStructData();
    if ($_structDataProto !== null) {
        try {
            if (method_exists($_structDataProto, 'serializeToJsonString')) {
                $structDataJson = $_structDataProto->serializeToJsonString();
                $structData = json_decode($structDataJson, true);
                
                // Extract text from main fields
                foreach (['content', 'text', 'description', 'title', 'snippet'] as $field) {
                    if (isset($structData[$field]) && !empty(trim($structData[$field]))) {
                        $docText .= ' ' . $structData[$field];
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore error and try other methods
        }
    }
    
    // Try to extract text from Content object
    if (empty($docText)) {
        $contentObj = $document->getContent();
        if ($contentObj && method_exists($contentObj, 'getRawText')) {
            $docText = $contentObj->getRawText();
        }
    }
    
    // Use document ID if no text is found
    if (empty($docText)) {
        $docText = $document->getId();
    }
    
    // Calculate similarity between query and document
    $score = calculateTextSimilarity($query, $docText);
    
    return $score;
}

/**
 * Calculate text similarity score
 * @param string $query Query text
 * @param string $text Document text
 * @return float Similarity score (0.1-1.0)
 */
function calculateTextSimilarity($query, $text) {
    // Normalize query and text
    $normalizedQuery = mb_strtolower(trim($query));
    $normalizedText = mb_strtolower(trim($text));
    
    // Calculate word overlap
    $queryWords = preg_split('/\s+/', $normalizedQuery);
    $textWords = preg_split('/\s+/', $normalizedText);
    
    $matchCount = 0;
    foreach ($queryWords as $queryWord) {
        if (empty($queryWord)) continue;
        
        // Count exact matches or substring matches
        if (in_array($queryWord, $textWords) || mb_strpos($normalizedText, $queryWord) !== false) {
            $matchCount++;
        }
    }
    
    // Base score - word match ratio (0-1)
    $baseScore = count($queryWords) > 0 ? $matchCount / count($queryWords) : 0;
    
    // Consider text length (very short texts are less reliable)
    $lengthFactor = min(mb_strlen($normalizedText) / 100, 1.0);
    
    // Final score = base score × length factor
    $finalScore = $baseScore * $lengthFactor;
    
    // Adjust score to 0.1-1.0 range
    return max(0.1, min(1.0, $finalScore));
}

/**
 * Summarize text to specified length
 * @param string $text Text to summarize
 * @param int $maxLength Maximum length of summary
 * @return string Summarized text
 */
function summarizeText($text, $maxLength = 200) {
    // Return as is if already short enough
    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }
    
    // Split into paragraphs and extract important ones
    $paragraphs = preg_split('/\n\s*\n|\r\n\s*\r\n/', $text);
    
    // First paragraph is usually important
    $summary = isset($paragraphs[0]) ? trim($paragraphs[0]) : '';
    
    // If first paragraph is long enough
    if (mb_strlen($summary) >= $maxLength * 0.8) {
        return mb_substr($summary, 0, $maxLength) . '…';
    }
    
    // Split into sentences
    $sentences = preg_split('/(?<=[。．!\?！？])\s*/', $text);
    
    // Add sentences until max length is reached
    $summary = '';
    foreach ($sentences as $sentence) {
        $sentence = trim($sentence);
        if (empty($sentence)) continue;
        
        // Stop if adding would exceed max length
        if (mb_strlen($summary . $sentence) > $maxLength) {
            // If already long enough, stop
            if (mb_strlen($summary) > $maxLength * 0.5) {
                break;
            }
            
            // Otherwise, add truncated sentence
            $remainingLength = $maxLength - mb_strlen($summary);
            if ($remainingLength > 15) { // At least 15 chars
                $summary .= mb_substr($sentence, 0, $remainingLength) . '…';
            }
            break;
        }
        
        $summary .= $sentence . ' ';
    }
    
    // Add ellipsis if not already there
    if (mb_substr($summary, -3) !== '...' && mb_substr($summary, -1) !== '…') {
        $summary .= '…';
    }

    // Format result (normalize ellipsis)
    $summary = str_replace('...', '…', trim($summary));
    $summary = str_replace('  ', ' ', $summary);
    
    return $summary;
}

/**
 * Improve text readability by adding line breaks
 * @param string $text Text to improve
 * @return string Formatted text with line breaks
 */
function improveTextReadability($text) {
    // Replace "..." with "…"
    $text = str_replace('...', '…', $text);
    
    // Add line break after Japanese periods - include HTML tags
    $text = preg_replace('/。(?!\n)/u', "。<br>\n", $text);
    
    // Remove excess line break tags
    $text = preg_replace('/<br>\s*<br>\s*<br>/u', "<br><br>", $text);
    
    // Add line break after English periods - include HTML tags
    $text = preg_replace('/\.(?!\n)(?!\d)(?!<br>)/u', ".<br>\n", $text);
    
    // Add line break after "?" and "!" - include HTML tags
    $text = preg_replace('/([！？!?])(?!\n)(?!<br>)/u', "$1<br>\n", $text);
    
    // Clean up consecutive line breaks
    $text = preg_replace('/\n{3,}/u', "\n\n", $text);
    
    return $text;
}
?>
