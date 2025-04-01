<?php
/*
 * Copyright (C) 2025 Open Source Digital Signage Initiative.
 *
 * You can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * You should have received a copy of the GNU Affero General Public License.
 * see <http://www.gnu.org/licenses/>.
 */

require 'vendor/autoload.php';

use Google\Cloud\DiscoveryEngine\V1\SearchRequest;

// 認証ファイルのパスをserviceAccountPath.txtから読み込む
$serviceAccountPathFile = __DIR__ . '/serviceAccountPath.txt';
if (!file_exists($serviceAccountPathFile)) {
    die('サービスアカウントパスファイルが見つかりません: ' . $serviceAccountPathFile);
}

$credentialsPath = trim(file_get_contents($serviceAccountPathFile));
if (empty($credentialsPath)) {
    die('サービスアカウントパスファイルが空です');
}

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);

// ファイルが存在するか確認
if (!file_exists($credentialsPath)) {
    die('認証ファイルが見つかりません: ' . $credentialsPath);
}

function queryVertexAIStreaming($userPrompt, $conversationHistory, $config) {
    $searchServiceClient = null;
    $stream = null;

    try {
        // APIクライアントの初期化
        $searchServiceClient = new Google\Cloud\DiscoveryEngine\V1\Client\SearchServiceClient([
            'credentials' => $credentialsPath,
            'projectId' => $config['projectId'],
            'location' => $config['location'],
        ]);

//        $searchServiceClient = new SearchServiceClient([
//            'projectId' => $config['projectId'],
//            'location' => $config['location'],
//        ]);

        // プロンプトをファイルから読み込む
        $systemPrompt = file_get_contents($config['promptFile']);
        
        // 会話履歴がある場合は、それを含めたプロンプトを構築
        $fullPrompt = $systemPrompt;
        
        if (!empty($conversationHistory)) {
            $historyText = "これまでの会話履歴:\n";
            foreach ($conversationHistory as $message) {
                $role = $message['role'] === 'user' ? 'ユーザー' : 'アシスタント';
                $historyText .= "{$role}: {$message['content']}\n";
            }
            $fullPrompt .= $historyText . "\n現在の質問: " . $userPrompt;
        } else {
            $fullPrompt .= $userPrompt;
        }

        // リクエストの作成
        $servingConfig = $searchServiceClient->servingConfigName(
            $config['projectId'],
            $config['location'],
            $config['dataStoreId'],
            $config['searchEngineId']
        );
        $searchRequest = (new SearchRequest())
            ->setServingConfig($servingConfig)
            ->setQuery($fullPrompt)
            ->setOffset(0)
            ->setPageSize(10);

        // ストリーミングレスポンスの処理
        $stream = $searchServiceClient->search($searchRequest);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');

        foreach ($stream->getResponse()->getResults() as $result) {
            $document = $result->getDocument();
            $content = $document->getContent();
            $pageUrl = $document->getDerivedStructData()['link'] ?? 'URLが見つかりませんでした。';

            echo "data: " . json_encode(['answer' => $content, 'page_url' => $pageUrl]) . "\n\n";
            ob_flush();
            flush();
            sleep(1);
        }
    } catch (Exception $e) {
        // エラーハンドリング
        echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
        ob_flush();
        flush();
    } finally {
        // クローズ処理
        if ($stream) {
            $stream->close();
        }
        if ($searchServiceClient) {
            $searchServiceClient->close();
        }
    }
}


//$envData = file_get_contents("/home/xs118061/OpenSignage/cloudsignage-449313-c2028de75f83.json");
//echo "data: " . $envData . "\n\n";

$userPrompt = $_POST['question'];

// 会話履歴を取得
$conversationHistory = [];
if (isset($_POST['history'])) {
    $conversationHistory = json_decode($_POST['history'], true);
}

// 設定ファイルを読み込む
$config = require 'config.php';

queryVertexAIStreaming($userPrompt, $conversationHistory, $config);

?>
