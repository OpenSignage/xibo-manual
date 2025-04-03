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
use Google\Cloud\DiscoveryEngine\V1\Client\SearchServiceClient;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest;
use Google\ApiCore\ApiException;

// Authentication setup
$serviceAccountPathFile = __DIR__ . '/serviceAccountPath.txt';
if (!file_exists($serviceAccountPathFile)) {
    die('Service account path file not found');
}
$credentialsPath = trim(file_get_contents($serviceAccountPathFile));
if (empty($credentialsPath)) {
    die('Service account path file is empty');
}
if (!file_exists($credentialsPath)) {
    die('Authentication file not found at specified path');
}

/**
 * Main function to query AI search and stream results
 * @param string $userPrompt User query
 * @param array $config Configuration parameters
 * @param string $credentialsPath Path to credentials file
 */
function queryVertexAIStreaming($userPrompt, $config, $credentialsPath) {
    $searchServiceClient = null;

    try {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header('Content-Type: text/event-stream; charset=UTF-8');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Disable Nginx buffering
        }

        // Send connection confirmation message
        debug_log("Connected. Waiting for AI response...");
        echo "event: status\n";
        echo "data: " . json_encode(['status' => 'connected', 'message' => '接続完了。検索準備中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();

        // Heartbeat function to keep connection alive
        $lastHeartbeat = time();
        function sendHeartbeat(&$lastHeartbeat) {
            $now = time();
            if ($now - $lastHeartbeat >= 10) { // Send heartbeat every 10 seconds
                echo "event: heartbeat\n";
                echo "data: " . json_encode(['timestamp' => $now], JSON_UNESCAPED_UNICODE) . "\n\n";
                ob_flush(); flush();
                $lastHeartbeat = $now;
                return true;
            }
            return false;
        }

        try {
            $searchServiceClient = new SearchServiceClient(['credentials' => $credentialsPath]);
            
            // Send heartbeat
            sendHeartbeat($lastHeartbeat);
        } catch (Exception $e) { 
            $errorMsg = 'SearchServiceClient initialization error: ' . $e->getMessage();
            debug_log($errorMsg);
            echo "event: error\n";
            echo "data: " . json_encode(['error' => 'APIサービスとの接続に失敗しました。'], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();
            return; 
        }

        try {
            // Preprocess query - extract keywords and optimize
            $searchQuery = preprocessQuery($userPrompt);
            debug_log("検索クエリ: " . $searchQuery);
            
            // Notify client of processing status
            echo "event: status\n";
            echo "data: " . json_encode(['status' => 'processing', 'message' => '検索中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();

            $formattedName = SearchServiceClient::servingConfigName(
                $config['projectId'], $config['location'],
                $config['dataStoreId'], $config['searchEngineId']
            );
            
            // Send heartbeat
            sendHeartbeat($lastHeartbeat);

            $searchRequest = (new SearchRequest())
                ->setServingConfig($formattedName)
                ->setQuery($searchQuery)
                ->setPageSize(15); // Get more results for filtering later
            
            // Notify client of processing status
            echo "event: status\n";
            echo "data: " . json_encode(['status' => 'processing', 'message' => '検索中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();
            
            // Send heartbeat
            sendHeartbeat($lastHeartbeat);

            // Execute search
            try {
                // Notify client of search execution
                debug_log("Google API検索実行: " . $searchQuery);
                
                $response = $searchServiceClient->search($searchRequest);
                
                debug_log("Google API検索完了");
            } catch (ApiException $e) {
                // Handle error and retry with basic search
                debug_log("検索エラー、基本検索に切り替え: " . $e->getMessage());
                
                // Create more basic search request
                $searchRequest = (new SearchRequest())
                    ->setServingConfig($formattedName)
                    ->setQuery($searchQuery)
                    ->setPageSize(15);
                
                $response = $searchServiceClient->search($searchRequest);
                debug_log("基本検索完了");
            }
            
            // Send heartbeat
            sendHeartbeat($lastHeartbeat);

            // Function to maintain connection
            $lastHeartbeat = time();
            function maintainConnection(&$lastHeartbeat) {
                if (time() - $lastHeartbeat > 10) {
                    echo ":\n\n"; // Send comment line to maintain connection
                    ob_flush(); flush();
                    $lastHeartbeat = time();
                }
            }

            // Process response
            $resultCount = 0;
            $relevantResults = [];  // Array to hold relevant results

            foreach ($response->getIterator() as $searchResult) {
                $resultCount++;
                if (!method_exists($searchResult, 'getDocument')) {
                    continue;
                }
                $document = $searchResult->getDocument();

                // Calculate relevance score
                $relevanceScore = calculateRelevanceScore($searchQuery, $document, $searchResult);
                
                // Skip results with low relevance score (below 0.3)
                if ($relevanceScore < 0.3) {
                    continue;
                }
                
                // Convert Protobuf\Struct to PHP associative array
                $structData = null;
                $_structDataProto = $document->getStructData();
                if ($_structDataProto !== null) {
                    try {
                        if (method_exists($_structDataProto, 'serializeToJsonString')) {
                            $structDataJson = $_structDataProto->serializeToJsonString();
                            $structData = json_decode($structDataJson, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                $structData = null;
                            }
                        }
                    } catch (Exception $e) {
                        $structData = null;
                    }
                }

                $derivedData = null;
                $_derivedDataProto = $document->getDerivedStructData();
                if ($_derivedDataProto !== null) {
                    try {
                        if (method_exists($_derivedDataProto, 'serializeToJsonString')) {
                            $derivedDataJson = $_derivedDataProto->serializeToJsonString();
                            $derivedData = json_decode($derivedDataJson, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                $derivedData = null;
                            }
                        }
                    } catch (Exception $e) {
                        $derivedData = null;
                    }
                }

                // Extract content and URL from converted PHP arrays
                $answerText = '関連情報が見つかりませんでした。';
                if ($structData !== null) {
                    if (isset($structData['snippet']) && !empty(trim($structData['snippet']))) {
                        $answerText = $structData['snippet'];
                    } elseif (isset($structData['content']) && !empty(trim($structData['content']))) {
                        $answerText = $structData['content'];
                    } elseif (isset($structData['text']) && !empty(trim($structData['text']))) {
                        $answerText = $structData['text'];
                    } elseif (isset($structData['description']) && !empty(trim($structData['description']))) {
                        $answerText = $structData['description'];
                    } elseif (isset($structData['title']) && !empty(trim($structData['title']))) {
                        $answerText = $structData['title'];
                    }
                } else {
                    $contentObj = $document->getContent();
                    if ($contentObj && method_exists($contentObj, 'getRawText') && !empty(trim($contentObj->getRawText()))) {
                        $answerText = $contentObj->getRawText();
                    } elseif ($contentObj && method_exists($contentObj, 'getUri')) {
                        $answerText = 'Content location: ' . $contentObj->getUri();
                    }
                }

                $pageUrl = '';
                if ($derivedData !== null) {
                    if (isset($derivedData['link']) && !empty($derivedData['link'])) {
                        $pageUrl = $derivedData['link'];
                    } elseif (isset($derivedData['url']) && !empty($derivedData['url'])) {
                        $pageUrl = $derivedData['url'];
                    } elseif (isset($derivedData['uri']) && !empty($derivedData['uri'])) {
                        $pageUrl = $derivedData['uri'];
                    } elseif (isset($derivedData['page_url']) && !empty($derivedData['page_url'])) {
                        $pageUrl = $derivedData['page_url'];
                    }
                }
                
                // Check $structData for URL if not found in $derivedData
                if (empty($pageUrl) && $structData !== null) {
                    if (isset($structData['link']) && !empty($structData['link'])) {
                        $pageUrl = $structData['link'];
                    } elseif (isset($structData['url']) && !empty($structData['url'])) {
                        $pageUrl = $structData['url'];
                    } elseif (isset($structData['uri']) && !empty($structData['uri'])) {
                        $pageUrl = $structData['uri'];
                    } elseif (isset($structData['page_url']) && !empty($structData['page_url'])) {
                        $pageUrl = $structData['page_url'];
                    }
                }
                
                // Last resort: check document object properties for URL
                if (empty($pageUrl)) {
                    $contentObj = $document->getContent();
                    if ($contentObj && method_exists($contentObj, 'getUri') && !empty($contentObj->getUri())) {
                        $pageUrl = $contentObj->getUri();
                    }
                }
                
                // Process URL for proper formatting
                if (!empty($pageUrl)) {
                    $slashPos = strpos($pageUrl, '/');
                    if ($slashPos !== false && $slashPos > 0) {
                        // Replace leading directory with "../"
                        $pageUrl = '../' . substr($pageUrl, $slashPos + 1);
                    } else {
                        // If no slash, just add "../"
                        $pageUrl = '../' . $pageUrl;
                    }
                }

                // Summarize long text for better display
                $originalText = $answerText;
                $hasFullText = false;
                
                if (mb_strlen($answerText) > 500) {
                    $hasFullText = true;
                    $answerText = summarizeText($answerText);
                }

                // Improve text readability by adding line breaks
                $answerText = improveTextReadability($answerText);
                if ($hasFullText) {
                    $originalText = improveTextReadability($originalText);
                }

                // Convert arrays/objects to JSON if needed
                if (is_array($answerText) || is_object($answerText)) {
                    $answerText = json_encode($answerText, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
                
                // Save as relevant result
                $resultItem = [
                    'answer' => $answerText, 
                    'page_url' => $pageUrl, 
                    'doc_id' => $document->getId(),
                    'relevance_score' => $relevanceScore
                ];
                
                // Save original text if summarized
                if ($hasFullText) {
                    $resultItem['has_full_text'] = true;
                    $resultItem['full_text'] = $originalText;
                }
                
                $relevantResults[] = $resultItem;
            } // end foreach

            // Sort by relevance
            usort($relevantResults, function($a, $b) {
                return $b['relevance_score'] <=> $a['relevance_score'];
            });

            // Send top relevant results
            $resultsSent = 0;
            foreach ($relevantResults as $result) {
                echo "event: answer\n";
                echo "data: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n\n";
                ob_flush(); flush();
                usleep(50000);
                $resultsSent++;
            }

            // Handle case when no results are found
            if ($resultsSent === 0) {
                // Use summary if available
                if (method_exists($response, 'getSummary') && ($summary = $response->getSummary()) !== null) {
                    if (method_exists($summary, 'getSummaryText') && !empty(trim($summary->getSummaryText()))) {
                        $finalMessage = $summary->getSummaryText();
                    } else { 
                        $finalMessage = '関連情報が見つかりませんでした。';
                    }
                    
                    // Get references if available
                    if (method_exists($summary, 'getReferences') && !empty($summary->getReferences())) {
                        $references = [];
                        foreach ($summary->getReferences() as $reference) {
                            if (method_exists($reference, 'getUri')) {
                                $references[] = $reference->getUri();
                            }
                        }
                        
                        echo "event: answer\n";
                        echo "data: " . json_encode([
                            'answer' => $finalMessage,
                            'references' => $references,
                            'is_summary' => true
                        ], JSON_UNESCAPED_UNICODE) . "\n\n";
                        ob_flush(); flush();
                    } else {
                        echo "event: status\n";
                        echo "data: " . json_encode([
                            'status' => 'no_results', 
                            'message' => $finalMessage
                        ], JSON_UNESCAPED_UNICODE) . "\n\n";
                        ob_flush(); flush();
                    }
                } else {
                    echo "event: status\n";
                    echo "data: " . json_encode([
                        'status' => 'no_results', 
                        'message' => '関連情報が見つかりませんでした。'
                    ], JSON_UNESCAPED_UNICODE) . "\n\n";
                    ob_flush(); flush();
                }
            } else {
                // Send summary info if multiple results
                if ($resultsSent > 1) {
                    // Send search results overview
                    echo "event: summary\n";
                    echo "data: " . json_encode([
                        'status' => 'results_summary',
                        'count' => $resultsSent,
                        'message' => "{$resultsSent}件の検索結果が見つかりました。各結果は自動的に要約されています。"
                    ], JSON_UNESCAPED_UNICODE) . "\n\n";
                    ob_flush(); flush();
                }
            }

        } catch (ApiException $e) { 
            $errorMsg = 'API call error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')';
            debug_log($errorMsg);
            
            // Notify client of error
            echo "event: error\n";
            echo "data: " . json_encode(['error' => '検索サービスでエラーが発生しました。'], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();
        }
        catch (Exception $e) { 
            $errorMsg = 'General error: ' . $e->getMessage();
            debug_log($errorMsg);
            
            // Notify client of error
            echo "event: error\n";
            echo "data: " . json_encode(['error' => 'エラーが発生しました。'], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();
        }

        // Send completion signal
        echo "event: status\n";
        echo "data: " . json_encode(['status' => 'completed'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();

    } catch (Throwable $e) { 
        $errorMsg = 'Fatal error: ' . $e->getMessage();
        debug_log($errorMsg);
        
        // Notify client of error
        echo "event: error\n";
        echo "data: " . json_encode(['error' => '深刻なエラーが発生しました。'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();
    }
    finally {
        if ($searchServiceClient !== null) {
            $searchServiceClient->close();
        }
    }
} // end function queryVertexAIStreaming

// Process request
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Read JSON request body
    $json_data = file_get_contents('php://input');
    
    // Decode JSON data
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
    $requiredConfigKeys = ['projectId', 'location', 'dataStoreId', 'searchEngineId'];
    foreach ($requiredConfigKeys as $key) {
        if (!isset($config[$key]) || empty($config[$key])) {
            debug_log('必須の設定キーがありません: ' . $key);
            die("Missing required configuration key: {$key}");
        }
    }

    // Call main function
    queryVertexAIStreaming($userPrompt, $config, $credentialsPath);
} else {
    // Only accept POST requests
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
