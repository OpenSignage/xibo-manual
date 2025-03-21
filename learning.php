<?php

function extractTextFromHtml($htmlFilePath, $targetClass) {
    $dom = new DOMDocument();
    @$dom->loadHTMLFile($htmlFilePath);
    $xpath = new DOMXPath($dom);
    $text = '';

    // 指定されたclassを持つdiv要素内のテキストを抽出
    $nodes = $xpath->query('//div[contains(@class, "' . $targetClass . '")]//*[not(self::script or self::style)]/text()');
    foreach ($nodes as $node) {
        $text .= trim($node->nodeValue) . "\n";
    }
    return $text;
}

function processHtmlFiles($directoryPath, $targetClass) {
    $fileData = [];
    $files = scandir($directoryPath);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directoryPath . '/' . $file;

        if (pathinfo($filePath, PATHINFO_EXTENSION) === 'html') {
print_r($filePath);
            $extractedText = extractTextFromHtml($filePath, $targetClass);
            // テキストを整形
            $formattedText = formatText($extractedText);
            $fileKey = pathinfo($file, PATHINFO_FILENAME);
            $fileData[] = [
                'id' => $fileKey,
                'content' => $formattedText,
                'page_url' => $filePath,
            ];
        }
    }
    return $fileData;
}

// テキストを整形する関数
function formatText($text) {
    // 不要な空白や改行を削除
    $text = preg_replace('/\s+/', ' ', $text);
    // 前後の空白を削除
    $text = trim($text);
    // その他の不要な文字を削除（必要に応じて追加）
    $text = str_replace(["\t", "\r", "\n", "\o", "\x0B"], '', $text);
    return $text;
}

$sourceDirectory = 'output/ja';
$aiDirectory = 'ai';
$outputFile = $aiDirectory . '/learning.json';

// 抽出対象のclass名を指定
$targetClass = 'col-md-7';

// 出力先のディレクトリが存在しない場合は作成
if (!is_dir($aiDirectory)) {
    mkdir($aiDirectory, 0755, true);
}

$extractedData = processHtmlFiles($sourceDirectory, $targetClass);

// 抽出したデータをJSON形式でファイルに書き込み
$jsonData = json_encode($extractedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents($outputFile, $jsonData);

echo "データが {$outputFile} に保存されました。\n";

?>
