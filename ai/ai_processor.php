<?php

require 'vendor/autoload.php';

use Google\Cloud\DiscoveryEngine\V1\SearchServiceClient;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest;

function queryVertexAIStreaming($userPrompt, $projectId, $location, $dataStoreId, $searchEngineId) {
    $searchServiceClient = null;
    $stream = null;

    try {
        // APIクライアントの初期化
        $searchServiceClient = new SearchServiceClient([
            'projectId' => $projectId,
            'location' => $location,
        ]);

        // プロンプトを組み込む
        $systemPrompt = "あなたは顧客からの問い合わせに対応する優秀なサポート担当者です。以下の点に注意して、顧客からの質問に回答してください。\n\n* 丁寧な言葉遣いを心がける\n* 顧客の質問を正確に理解し、適切な回答を提供する\n* 必要に応じて、関連する情報や資料を提供する\n\n顧客からの質問：";
        $query = $systemPrompt . $userPrompt;

        // リクエストの作成
        $servingConfig = $searchServiceClient->servingConfigName($projectId, $location, $dataStoreId, $searchEngineId);
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

$projectId = 'YOUR_PROJECT_ID';
$location = 'global';
$dataStoreId = 'YOUR_DATA_STORE_ID';
$searchEngineId = 'YOUR_SEARCH_ENGINE_ID';

queryVertexAIStreaming($userPrompt, $projectId, $location, $dataStoreId, $searchEngineId);

?>
