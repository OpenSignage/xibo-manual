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

use Google\Cloud\DiscoveryEngine\V1\SearchServiceClient;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest;

function queryVertexAIStreaming($userPrompt, $config) {
    $searchServiceClient = null;
    $stream = null;

    try {
        // APIクライアントの初期化
        $searchServiceClient = new SearchServiceClient([
            'projectId' => $config['projectId'],
            'location' => $config['location'],
        ]);

        // プロンプトをファイルから読み込む
        $systemPrompt = file_get_contents($config['promptFile']);
        $query = $systemPrompt . $userPrompt;

        // リクエストの作成
        $servingConfig = $searchServiceClient->servingConfigName(
            $config['projectId'],
            $config['location'],
            $config['dataStoreId'],
            $config['searchEngineId']
        );
        $searchRequest = (new SearchRequest())
            ->setServingConfig($servingConfig)
            ->setQuery($query)
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

putenv('GOOGLE_APPLICATION_CREDENTIALS="apiAccessKey.json"');

// 設定された環境変数を取得する例
$credentials = getenv("GOOGLE_APPLICATION_CREDENTIALS");
echo "GOOGLE_APPLICATION_CREDENTIALS: " . $credentials . "\n";
$userPrompt = $_POST['question'];

// 設定ファイルを読み込む
$config = require 'config.php';

queryVertexAIStreaming($userPrompt, $config);

?>
