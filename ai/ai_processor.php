<?php

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

$userPrompt = $_POST['question'];

// 設定ファイルを読み込む
$config = require 'config.php';

queryVertexAIStreaming($userPrompt, $config);

?>
