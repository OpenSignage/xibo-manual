<?php
/*
 * Copyright (C) 2025 Open Source Digital Signage Initiative.
 */

// エラー表示を有効化
ini_set('display_errors', 1);
error_reporting(E_ALL);

// PHP実行時間制限の緩和（300秒）
set_time_limit(300);

// メモリ制限を緩和（512MB）
ini_set('memory_limit', '512M');

// タイムアウトやバッファリング設定
ini_set('default_socket_timeout', 300);
ini_set('max_execution_time', 300);
ignore_user_abort(true);

// デバッグログファイル - UTF-8で書き込むように設定
$logFile = __DIR__ . '/debug.log';
$logDir = dirname($logFile);

// ログディレクトリ確認
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// ログファイルの書き込み権限確認
if (!is_writable($logDir)) {
    error_log('警告: ログディレクトリに書き込み権限がありません: ' . $logDir);
}

function debug_log($message) {
    global $logFile;
    $log_entry = date('[Y-m-d H:i:s] ') . print_r($message, true) . "\n";
    
    // エラーをキャッチしてPHPエラーログに記録
    try {
        file_put_contents($logFile, $log_entry, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        error_log('デバッグログ書き込みエラー: ' . $e->getMessage());
    }
}

debug_log('スクリプト開始');

// Composer オートローダーの存在確認
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    $errorMessage = 'Composer autoload ファイルが見つかりません。`composer install` を実行してください。';
    debug_log('エラー: ' . $errorMessage);
    if (!headers_sent()) { /* ... ヘッダー & エラー出力 ... */ }
    die($errorMessage);
}
require 'vendor/autoload.php';
debug_log('Composerオートロード完了');

// 使用するクラス
use Google\Cloud\DiscoveryEngine\V1\Client\SearchServiceClient;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest;
use Google\ApiCore\ApiException;

debug_log('クラスのインポート完了');

// --- 認証設定 ---
$serviceAccountPathFile = __DIR__ . '/serviceAccountPath.txt';
if (!file_exists($serviceAccountPathFile)) { /* ... エラー処理 ... */ die('サービスアカウントパスファイルが見つかりません'); }
$credentialsPath = trim(file_get_contents($serviceAccountPathFile));
debug_log('認証ファイルパス読み込み完了');
if (empty($credentialsPath)) { /* ... エラー処理 ... */ die('サービスアカウントパスファイルが空です'); }
if (!file_exists($credentialsPath)) { /* ... エラー処理 ... */ die('指定された認証ファイルが見つかりません'); }
debug_log('認証ファイル存在確認完了');

// デバッグ用の情報出力を強化
function dump_debug_info($credentialsPath) {
    global $logFile;
    
    // PHP情報
    debug_log('PHP バージョン: ' . phpversion());
    
    // Googleライブラリ情報
    $lib_paths = [
        'SearchServiceClient' => '\Google\Cloud\DiscoveryEngine\V1\Client\SearchServiceClient',
        'SearchRequest' => '\Google\Cloud\DiscoveryEngine\V1\SearchRequest',
        'ContentSearchSpec' => '\Google\Cloud\DiscoveryEngine\V1\SearchRequest\ContentSearchSpec',
        'SnippetSpec' => '\Google\Cloud\DiscoveryEngine\V1\SearchRequest\ContentSearchSpec\SnippetSpec',
        'SummarySpec' => '\Google\Cloud\DiscoveryEngine\V1\SearchRequest\ContentSearchSpec\SummarySpec'
    ];
    
    debug_log('=== Google API クラスの存在チェック ===');
    foreach ($lib_paths as $name => $path) {
        debug_log("クラス $name ($path): " . (class_exists($path) ? '存在します' : '見つかりません'));
    }
    
    // 設定ファイル情報
    debug_log('認証ファイルパス: ' . $credentialsPath);
    debug_log('認証ファイル存在: ' . (file_exists($credentialsPath) ? 'はい' : 'いいえ'));
    if (file_exists($credentialsPath)) {
        debug_log('認証ファイルサイズ: ' . filesize($credentialsPath) . ' バイト');
    }
    
    // メモリ情報
    debug_log('メモリ使用量: ' . memory_get_usage() / 1024 / 1024 . ' MB');
    debug_log('メモリ制限: ' . ini_get('memory_limit'));
}

// --- メイン関数 ---
function queryVertexAIStreaming($userPrompt, $conversationHistory, $config, $credentialsPath) {
    debug_log('queryVertexAIStreaming関数開始');
    debug_log('パラメータ: userPrompt=' . $userPrompt);

    $searchServiceClient = null;

    try {
        debug_log('try ブロック開始');
        while (ob_get_level() > 0) { ob_end_clean(); }

        if (!headers_sent()) {
            header('Content-Type: text/event-stream; charset=UTF-8');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Nginxバッファリング無効化
            debug_log('HTTPヘッダー設定完了');
        } else { debug_log('警告: ヘッダーは既に送信されています。'); }

        // 接続確認メッセージを送信
        echo "event: status\n";
        echo "data: " . json_encode(['status' => 'connected', 'message' => '接続しました。AIからの応答を待っています...'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();
        debug_log('接続開始メッセージ送信完了');

        // ハートビート送信関数
        $lastHeartbeat = time();
        function sendHeartbeat(&$lastHeartbeat) {
            $now = time();
            if ($now - $lastHeartbeat >= 10) { // 10秒ごとにハートビート送信
                echo "event: heartbeat\n";
                echo "data: " . json_encode(['timestamp' => $now], JSON_UNESCAPED_UNICODE) . "\n\n";
                ob_flush(); flush();
                $lastHeartbeat = $now;
                debug_log('ハートビート送信: ' . $now);
                return true;
            }
            return false;
        }

        debug_log('SearchServiceClient初期化開始');
        try {
            $searchServiceClient = new SearchServiceClient(['credentials' => $credentialsPath]);
            debug_log('SearchServiceClient初期化成功');
            
            // ハートビート送信
            sendHeartbeat($lastHeartbeat);
        } catch (Exception $e) { 
            $errorMsg = 'SearchServiceClient初期化エラー: ' . $e->getMessage();
            debug_log($errorMsg);
            echo "event: error\n";
            echo "data: " . json_encode(['error' => $errorMsg], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();
            return; 
        }

        try {
            $promptFilePath = $config['promptFile'] ?? __DIR__ . '/prompt.txt';
            if (!file_exists($promptFilePath)) { throw new Exception('プロンプトファイルが見つかりません: ' . $promptFilePath); }
            debug_log('プロンプトファイル読み込み: ' . $promptFilePath);
            $systemPrompt = file_get_contents($promptFilePath); // プロンプト利用方法は要検討
            debug_log('プロンプト読み込み完了');
            
            // ハートビート送信
            sendHeartbeat($lastHeartbeat);

            // クエリの前処理 - キーワード抽出と最適化
            debug_log('クエリ前処理開始: ' . $userPrompt);
            $searchQuery = preprocessQuery($userPrompt);
            debug_log('最適化されたクエリ: ' . $searchQuery);
            
            // 処理状況をクライアントに通知
            echo "event: status\n";
            echo "data: " . json_encode(['status' => 'processing', 'message' => '検索クエリ処理中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();

            debug_log('ServingConfigName生成開始');
            $formattedName = SearchServiceClient::servingConfigName(
                $config['projectId'], $config['location'],
                $config['dataStoreId'], $config['searchEngineId']
            );
            debug_log('ServingConfigName: ' . $formattedName);
            
            // ハートビート送信
            sendHeartbeat($lastHeartbeat);

            debug_log('SearchRequest作成開始');
            $searchRequest = (new SearchRequest())
                ->setServingConfig($formattedName)
                ->setQuery($searchQuery)
                ->setPageSize(15); // より多くの結果を取得して後でフィルタリング

            // 利用可能クラスをデバッグ出力
            debug_log('SearchRequest クラス: ' . get_class($searchRequest));
            $reflectionClass = new ReflectionClass($searchRequest);
            debug_log('利用可能メソッド: ' . implode(', ', array_map(function($method) {
                return $method->getName();
            }, $reflectionClass->getMethods())));
            
            // 処理状況をクライアントに通知
            echo "event: status\n";
            echo "data: " . json_encode(['status' => 'processing', 'message' => 'Google APIに接続中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();

            // ContentSearchSpec機能が利用できるかどうかをチェック（使用しない）
            $contentSearchEnabled = false;
            try {
                debug_log('DataStoreのContentSearch機能チェック');
                if (class_exists('\Google\Cloud\DiscoveryEngine\V1\SearchRequest\ContentSearchSpec')) {
                    // すでにエラーが発生しているため、この機能は使用しない
                    debug_log('ContentSearchSpecクラスは存在しますが、DataStoreがサポートしていないため使用しません');
                }
            } catch (Exception $e) {
                debug_log('ContentSearchSpecチェックエラー: ' . $e->getMessage());
            }
            
            // ハートビート送信
            sendHeartbeat($lastHeartbeat);

            // 検索実行
            try {
                debug_log('基本検索モードでAPI呼び出し開始');
                
                // 処理状況をクライアントに通知
                echo "event: status\n";
                echo "data: " . json_encode(['status' => 'processing', 'message' => '検索実行中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
                ob_flush(); flush();
                
                $response = $searchServiceClient->search($searchRequest);
                debug_log('search API呼び出し完了');
                
                // 検索完了通知
                echo "event: status\n";
                echo "data: " . json_encode(['status' => 'processing', 'message' => '検索結果処理中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
                ob_flush(); flush();
            } catch (ApiException $e) {
                // エラー処理
                debug_log('API呼び出しエラー: ' . $e->getMessage() . ' - 再試行');
                
                // クライアントに通知
                echo "event: status\n";
                echo "data: " . json_encode(['status' => 'processing', 'message' => 'エラーが発生したため、基本検索モードで再試行中...'], JSON_UNESCAPED_UNICODE) . "\n\n";
                ob_flush(); flush();
                
                // より基本的な検索リクエストを作成
                $searchRequest = (new SearchRequest())
                    ->setServingConfig($formattedName)
                    ->setQuery($searchQuery)
                    ->setPageSize(15);
                
                $response = $searchServiceClient->search($searchRequest);
                debug_log('基本検索モードでの検索完了');
            }
            
            // ハートビート送信
            sendHeartbeat($lastHeartbeat);

            // 接続を維持するために定期的にハートビートを送信
            $lastHeartbeat = time();
            function maintainConnection(&$lastHeartbeat) {
                if (time() - $lastHeartbeat > 10) {
                    echo ":\n\n"; // コメント行を送信して接続を維持
                    ob_flush(); flush();
                    $lastHeartbeat = time();
                }
            }

            // --- レスポンスの処理 ---
            debug_log('結果処理開始');
            $resultCount = 0;
            $relevantResults = [];  // 関連性の高い結果のみを保持する配列

            foreach ($response->getIterator() as $searchResult) {
                $resultCount++;
                debug_log("結果 #{$resultCount} 処理中");
                if (!method_exists($searchResult, 'getDocument')) { continue; }
                $document = $searchResult->getDocument();
                debug_log('Document ID: ' . $document->getId());

                // クライアント側で関連性スコアを計算
                $relevanceScore = calculateRelevanceScore($searchQuery, $document, $searchResult);
                debug_log("計算された関連性スコア: {$relevanceScore}");
                
                // 関連性スコアが低すぎる場合はスキップ (0.3未満の結果は表示しない)
                if ($relevanceScore < 0.3) {
                    debug_log("関連性スコアが低いためスキップ: {$relevanceScore}");
                    continue;
                }
                
                // --- Protobuf\Struct を PHP 連想配列に変換 ---
                $structData = null;
                $_structDataProto = $document->getStructData();
                if ($_structDataProto !== null) {
                    try {
                        if (method_exists($_structDataProto, 'serializeToJsonString')) {
                            $structDataJson = $_structDataProto->serializeToJsonString();
                            $structData = json_decode($structDataJson, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                debug_log("警告: structData の JSON デコード失敗: " . json_last_error_msg()); $structData = null;
                            }
                        } else { debug_log("警告: structData は serializeToJsonString 未実装"); }
                    } catch (Exception $e) { debug_log("警告: structData 変換エラー: " . $e->getMessage()); $structData = null; }
                } else { debug_log('structData が null です。'); }

                $derivedData = null;
                $_derivedDataProto = $document->getDerivedStructData();
                if ($_derivedDataProto !== null) {
                    try {
                        if (method_exists($_derivedDataProto, 'serializeToJsonString')) {
                            $derivedDataJson = $_derivedDataProto->serializeToJsonString();
                            $derivedData = json_decode($derivedDataJson, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                debug_log("警告: derivedData の JSON デコード失敗: " . json_last_error_msg()); $derivedData = null;
                            } else { debug_log('Derived Data (Array): ' . print_r($derivedData, true)); }
                        } else { debug_log("警告: derivedData は serializeToJsonString 未実装"); }
                    } catch (Exception $e) { debug_log("警告: derivedData 変換エラー: " . $e->getMessage()); $derivedData = null; }
                } else { debug_log('Derived Data が null です。'); }

                // --- 変換後の PHP 配列を使ってコンテンツとURLを取得 ---
                $answerText = '関連情報が見つかりませんでした。';
                if ($structData !== null) {
                    if (isset($structData['snippet']) && !empty(trim($structData['snippet']))) { $answerText = $structData['snippet']; }
                    elseif (isset($structData['content']) && !empty(trim($structData['content']))) { $answerText = $structData['content']; }
                    elseif (isset($structData['text']) && !empty(trim($structData['text']))) { $answerText = $structData['text']; }
                    elseif (isset($structData['description']) && !empty(trim($structData['description']))) { $answerText = $structData['description']; }
                    elseif (isset($structData['title']) && !empty(trim($structData['title']))) { $answerText = $structData['title']; }
                    else { debug_log("変換後のstructData配列から有効なコンテンツフィールドが見つかりません。"); }
                } else {
                    debug_log('structDataがnullのため、Contentオブジェクトからの取得を試みます。');
                    $contentObj = $document->getContent();
                    if ($contentObj && method_exists($contentObj, 'getRawText') && !empty(trim($contentObj->getRawText()))) {
                        $answerText = $contentObj->getRawText(); debug_log('Content->getRawText() からテキストを取得。');
                    } elseif ($contentObj && method_exists($contentObj, 'getUri')) {
                        $answerText = 'コンテンツの場所: ' . $contentObj->getUri(); debug_log('Content->getUri() からURIを取得。');
                    }
                 }

                $pageUrl = '';
                if ($derivedData !== null) {
                    if (isset($derivedData['link']) && !empty($derivedData['link'])) { $pageUrl = $derivedData['link']; }
                    elseif (isset($derivedData['url']) && !empty($derivedData['url'])) { $pageUrl = $derivedData['url']; }
                    elseif (isset($derivedData['uri']) && !empty($derivedData['uri'])) { $pageUrl = $derivedData['uri']; }
                    elseif (isset($derivedData['page_url']) && !empty($derivedData['page_url'])) { $pageUrl = $derivedData['page_url']; }
                    debug_log('derivedDataからURL取得試行: ' . ($pageUrl ? '成功' : '失敗'));
                } else { 
                    debug_log('derivedDataがnullのため、URLの取得ができません'); 
                }
                
                // $structDataからもURLを探す（$derivedDataからURLが取得できなかった場合）
                if (empty($pageUrl) && $structData !== null) {
                    if (isset($structData['link']) && !empty($structData['link'])) { $pageUrl = $structData['link']; }
                    elseif (isset($structData['url']) && !empty($structData['url'])) { $pageUrl = $structData['url']; }
                    elseif (isset($structData['uri']) && !empty($structData['uri'])) { $pageUrl = $structData['uri']; }
                    elseif (isset($structData['page_url']) && !empty($structData['page_url'])) { $pageUrl = $structData['page_url']; }
                    debug_log('structDataからURL取得試行: ' . ($pageUrl ? '成功' : '失敗'));
                }
                
                // ドキュメントオブジェクトの他のプロパティからURLを探す（最後の手段）
                if (empty($pageUrl)) {
                    $contentObj = $document->getContent();
                    if ($contentObj && method_exists($contentObj, 'getUri') && !empty($contentObj->getUri())) {
                        $pageUrl = $contentObj->getUri();
                        debug_log('Content->getUri()からURL取得: ' . $pageUrl);
                    }
                }
                
                // URLの先頭部分（最初の「/」まで）を削除
                if (!empty($pageUrl)) {
                    $slashPos = strpos($pageUrl, '/');
                    if ($slashPos !== false && $slashPos > 0) {
                        // 先頭ディレクトリを「../」に置き換え
                        $pageUrl = '../' . substr($pageUrl, $slashPos + 1);
                        debug_log('URLの先頭ディレクトリを「../」に変更しました');
                    } else {
                        // スラッシュがない場合はそのまま「../」を追加
                        $pageUrl = '../' . $pageUrl;
                        debug_log('URLにスラッシュがないため、先頭に「../」を追加しました');
                    }
                }
                
                debug_log('最終URL=' . $pageUrl);

                // 長すぎるテキストは要約
                $originalText = $answerText;
                $hasFullText = false;
                
                // テキストが長い場合は要約
                if (mb_strlen($answerText) > 500) {
                    debug_log('長いテキスト（' . mb_strlen($answerText) . '文字）を要約します');
                    $hasFullText = true;
                    $answerText = summarizeText($answerText);
                    debug_log('要約後のテキスト: ' . mb_strlen($answerText) . '文字');
                }

                // 句点の後に改行を挿入して読みやすくする
                $answerText = improveTextReadability($answerText);
                if ($hasFullText) {
                    $originalText = improveTextReadability($originalText);
                }

                // --- 結果送信 ---
                if (is_array($answerText) || is_object($answerText)) { $answerText = json_encode($answerText, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); }
                debug_log("結果 #{$resultCount} 送信: Answer=" . mb_substr($answerText, 0, 50, 'UTF-8') . "URL=" . $pageUrl);
                
                // 関連性の高い結果として保存
                $resultItem = [
                    'answer' => $answerText, 
                    'page_url' => $pageUrl, 
                    'doc_id' => $document->getId(),
                    'relevance_score' => $relevanceScore
                ];
                
                // 元のテキストも保存（要約された場合）
                if ($hasFullText) {
                    $resultItem['has_full_text'] = true;
                    $resultItem['full_text'] = $originalText;
                }
                
                $relevantResults[] = $resultItem;
            } // end foreach

            // 関連性でソート
            usort($relevantResults, function($a, $b) {
                return $b['relevance_score'] <=> $a['relevance_score'];
            });

            // 関連性の高い上位の結果のみを送信
            $resultsSent = 0;
            foreach ($relevantResults as $result) {
                echo "event: answer\n";
                echo "data: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n\n";
                ob_flush(); flush();
                usleep(50000);
                $resultsSent++;
            }

            // --- 結果0件の場合の処理 ---
            if ($resultsSent === 0) {
                debug_log('結果0件 - サマリー処理開始');
                // サマリー機能が利用可能な場合は使用
                if (method_exists($response, 'getSummary') && ($summary = $response->getSummary()) !== null) {
                    if (method_exists($summary, 'getSummaryText') && !empty(trim($summary->getSummaryText()))) {
                        $finalMessage = $summary->getSummaryText();
                        debug_log('サマリーテキスト: ' . $finalMessage);
                    } else { 
                        $finalMessage = '該当する情報が見つかりませんでした。';
                        debug_log('サマリーテキストがありません'); 
                    }
                    
                    // サマリー内の参照情報があれば取得
                    if (method_exists($summary, 'getReferences') && !empty($summary->getReferences())) {
                        debug_log('参照情報あり');
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
                    debug_log('サマリー機能は利用できません');
                    echo "event: status\n";
                    echo "data: " . json_encode([
                        'status' => 'no_results', 
                        'message' => '該当する情報が見つかりませんでした。'
                    ], JSON_UNESCAPED_UNICODE) . "\n\n";
                    ob_flush(); flush();
                }
            } else {
                // サマリー情報を送信（結果が複数ある場合）
                if ($resultsSent > 1) {
                    debug_log("検索完了: {$resultsSent}件の関連結果を送信しました");
                    
                    // 検索結果の概要情報を送信
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
            $errorMsg = 'API呼び出しエラー: ' . $e->getMessage() . ' (コード: ' . $e->getCode() . ')';
            debug_log($errorMsg);
            
            // クライアントにエラーを通知
            echo "event: error\n";
            echo "data: " . json_encode(['error' => $errorMsg], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();
        }
        catch (Exception $e) { 
            $errorMsg = '一般エラー: ' . $e->getMessage();
            debug_log($errorMsg);
            
            // クライアントにエラーを通知
            echo "event: error\n";
            echo "data: " . json_encode(['error' => $errorMsg], JSON_UNESCAPED_UNICODE) . "\n\n";
            ob_flush(); flush();
        }

        debug_log('完了シグナル送信');
        echo "event: status\n";
        echo "data: " . json_encode(['status' => 'completed'], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();

    } catch (Throwable $e) { 
        $errorMsg = '致命的エラー: ' . $e->getMessage();
        debug_log($errorMsg);
        
        // クライアントにエラーを通知
        echo "event: error\n";
        echo "data: " . json_encode(['error' => $errorMsg], JSON_UNESCAPED_UNICODE) . "\n\n";
        ob_flush(); flush();
    }
    finally {
        if ($searchServiceClient !== null) { $searchServiceClient->close(); }
        debug_log('queryVertexAIStreaming関数終了');
    }
} // end function queryVertexAIStreaming

// --- リクエスト処理 ---
debug_log('リクエストメソッド: ' . ($_SERVER['REQUEST_METHOD'] ?? 'N/A'));
debug_log('=== 詳細デバッグ情報 ===');
dump_debug_info($credentialsPath);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // JSONリクエストボディの読み取り
    $json_data = file_get_contents('php://input');
    debug_log('受信したJSONデータ: ' . $json_data);
    
    // JSONデータをデコード
    $post_data = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        debug_log('エラー: JSONデータのデコードに失敗: ' . json_last_error_msg());
        echo "event: error\n";
        echo "data: " . json_encode(['error' => 'JSONデータの解析に失敗しました。'], JSON_UNESCAPED_UNICODE) . "\n\n";
        die();
    }
    
    debug_log('デコードされたPOSTデータ: ' . print_r($post_data, true));
    
    $userPrompt = $post_data['question'] ?? '';
    $conversationHistory = $post_data['history'] ?? [];
    
    if (empty($userPrompt)) {
        debug_log('エラー: 質問が空です');
        echo "event: error\n";
        echo "data: " . json_encode(['error' => '質問が空です。'], JSON_UNESCAPED_UNICODE) . "\n\n";
        die();
    }
    
    debug_log('質問: ' . $userPrompt);
    debug_log('会話履歴件数: ' . count($conversationHistory));

    // --- 設定ファイルの読み込み & チェック ---
    $configPath = __DIR__ . '/config.php';
    if (!file_exists($configPath)) { /* ... エラー処理 ... */ die(); }
    debug_log('設定ファイル読み込み開始');
    $config = require $configPath;
    $requiredConfigKeys = ['projectId', 'location', 'dataStoreId', 'searchEngineId'];
    foreach ($requiredConfigKeys as $key) { if (!isset($config[$key]) || empty($config[$key])) { /* ... エラー処理 ... */ die(); } }

    // --- メイン関数呼び出し ---
    queryVertexAIStreaming($userPrompt, $conversationHistory, $config, $credentialsPath);
} else { /* ... POST以外エラー処理 ... */ }

debug_log('スクリプト終了');

// クエリ前処理関数を追加
function preprocessQuery($userPrompt) {
    // 簡単なクエリ最適化
    // 長すぎるクエリは要約
    if (mb_strlen($userPrompt) > 200) {
        // 長いクエリの場合、重要そうな部分を抽出
        $keywords = extractKeywords($userPrompt);
        return $keywords;
    }
    
    // 特殊文字の処理
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\.\,\?\!]/u', ' ', $userPrompt);
    
    return trim($cleaned);
}

// キーワード抽出関数
function extractKeywords($text) {
    // 簡易的なキーワード抽出
    // ストップワードのリスト（日本語と英語の一般的なもの）
    $stopWords = ['の', 'に', 'は', 'を', 'た', 'が', 'で', 'て', 'と', 'し', 'れ', 'さ', 'ある', 'いる', 'する', 'から', 'など', 'the', 'is', 'at', 'which', 'on', 'a', 'an', 'and', 'or', 'for', 'in', 'to', 'that', 'this', 'with'];
    
    // 文字列を単語に分割（日本語は形態素解析が理想だが、ここでは簡易的に空白で分割）
    $words = preg_split('/[\s\.\,\?\!]+/u', $text);
    
    // ストップワードと短すぎる単語を除外
    $keywords = [];
    foreach ($words as $word) {
        $word = trim($word);
        if (!empty($word) && mb_strlen($word) > 1 && !in_array(mb_strtolower($word), $stopWords)) {
            $keywords[] = $word;
        }
    }
    
    // 重複を除去して結合
    $keywords = array_unique($keywords);
    
    // 最大10個のキーワードに制限
    $keywords = array_slice($keywords, 0, 10);
    
    return implode(' ', $keywords);
}

// 関連性スコア計算関数を追加
function calculateRelevanceScore($query, $document, $searchResult) {
    // APIから直接スコアが取得できる場合はそれを使用
    if (method_exists($searchResult, 'getRelevanceScore')) {
        $apiScore = $searchResult->getRelevanceScore();
        if ($apiScore > 0) {
            debug_log("API提供の関連性スコア: {$apiScore}を使用");
            return $apiScore;
        }
    }
    
    // APIからスコアが取得できない場合は簡易的なスコア計算を行う
    debug_log('独自の関連性スコア計算を実行');
    
    // ドキュメントからテキストを抽出
    $docText = '';
    $structData = null;
    
    // structDataからテキスト抽出を試みる
    $_structDataProto = $document->getStructData();
    if ($_structDataProto !== null) {
        try {
            if (method_exists($_structDataProto, 'serializeToJsonString')) {
                $structDataJson = $_structDataProto->serializeToJsonString();
                $structData = json_decode($structDataJson, true);
                
                // 主要フィールドからテキストを抽出
                foreach (['content', 'text', 'description', 'title', 'snippet'] as $field) {
                    if (isset($structData[$field]) && !empty(trim($structData[$field]))) {
                        $docText .= ' ' . $structData[$field];
                    }
                }
            }
        } catch (Exception $e) {
            debug_log('structData解析エラー: ' . $e->getMessage());
        }
    }
    
    // Contentオブジェクトからテキスト抽出を試みる
    if (empty($docText)) {
        $contentObj = $document->getContent();
        if ($contentObj && method_exists($contentObj, 'getRawText')) {
            $docText = $contentObj->getRawText();
        }
    }
    
    // テキストが取得できない場合はドキュメントIDをスコア計算に使用
    if (empty($docText)) {
        $docText = $document->getId();
    }
    
    // クエリとドキュメントの類似性を簡易的に計算
    $score = calculateTextSimilarity($query, $docText);
    
    debug_log("計算された類似性スコア: {$score}");
    return $score;
}

// テキスト類似性計算関数
function calculateTextSimilarity($query, $text) {
    // クエリとテキストを正規化
    $normalizedQuery = mb_strtolower(trim($query));
    $normalizedText = mb_strtolower(trim($text));
    
    // 単語の重複率を計算
    $queryWords = preg_split('/\s+/', $normalizedQuery);
    $textWords = preg_split('/\s+/', $normalizedText);
    
    $matchCount = 0;
    foreach ($queryWords as $queryWord) {
        if (empty($queryWord)) continue;
        
        // 完全一致またはテキスト内に単語が含まれる場合
        if (in_array($queryWord, $textWords) || mb_strpos($normalizedText, $queryWord) !== false) {
            $matchCount++;
        }
    }
    
    // 基本スコア - 単語の一致率（0〜1）
    $baseScore = count($queryWords) > 0 ? $matchCount / count($queryWords) : 0;
    
    // テキストの長さも考慮（短すぎるテキストは信頼性が低い）
    $lengthFactor = min(mb_strlen($normalizedText) / 100, 1.0);
    
    // 最終スコア = 基本スコア × 長さファクター
    $finalScore = $baseScore * $lengthFactor;
    
    // 0.1〜1.0の範囲にスコアを調整
    return max(0.1, min(1.0, $finalScore));
}

// テキスト要約関数
function summarizeText($text, $maxLength = 200) {
    // テキストが既に十分短い場合はそのまま返す
    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }
    
    // まず段落に分割して重要な段落を抽出
    $paragraphs = preg_split('/\n\s*\n|\r\n\s*\r\n/', $text);
    
    // 最初の段落は通常重要なので必ず含める
    $summary = isset($paragraphs[0]) ? trim($paragraphs[0]) : '';
    
    // 最初の段落だけで十分な長さがある場合
    if (mb_strlen($summary) >= $maxLength * 0.8) {
        return mb_substr($summary, 0, $maxLength) . '…';
    }
    
    // 文に分割
    $sentences = preg_split('/(?<=[。．!\?！？])\s*/', $text);
    
    // 最大長に収まるまで文を追加
    $summary = '';
    foreach ($sentences as $sentence) {
        $sentence = trim($sentence);
        if (empty($sentence)) continue;
        
        // 追加するとオーバーするなら終了
        if (mb_strlen($summary . $sentence) > $maxLength) {
            // 既に十分な長さがあれば終了
            if (mb_strlen($summary) > $maxLength * 0.5) {
                break;
            }
            
            // そうでなければ、切り詰めて追加
            $remainingLength = $maxLength - mb_strlen($summary);
            if ($remainingLength > 15) { // 少なくとも15文字は追加
                $summary .= mb_substr($sentence, 0, $remainingLength) . '…';
            }
            break;
        }
        
        $summary .= $sentence . ' ';
    }
    
    // 文末が...でない場合は追加
    if (mb_substr($summary, -3) !== '...' && mb_substr($summary, -1) !== '…') {
        $summary .= '…';
    }

    // 結果の整形（三点リーダーの正規化）
    $summary = str_replace('...', '…', trim($summary));
    $summary = str_replace('  ', ' ', $summary);
    
    return $summary;
}

// テキストの読みやすさを改善する関数
function improveTextReadability($text) {
    // 三点リーダー（...）を特殊文字の「…」に置換
    $text = str_replace('...', '…', $text);
    
    // 句点（。）の後に改行を挿入 - HTMLタグも追加
    $text = preg_replace('/。(?!\n)/u', "。<br>\n", $text);
    
    // 連続する改行タグを削除
    $text = preg_replace('/<br>\s*<br>\s*<br>/u', "<br><br>", $text);
    
    // 英文の場合のピリオド後も改行 - HTMLタグも追加
    $text = preg_replace('/\.(?!\n)(?!\d)(?!<br>)/u', ".<br>\n", $text);
    
    // "？"や"！"の後も改行 - HTMLタグも追加
    $text = preg_replace('/([！？!?])(?!\n)(?!<br>)/u', "$1<br>\n", $text);
    
    // すでに連続する改行があれば整理
    $text = preg_replace('/\n{3,}/u', "\n\n", $text);
    
    return $text;
}
?>
