<?php

function extractTextFromHtml($htmlFilePath, $targetClass) {
    $dom = new DOMDocument();
    @$dom->loadHTMLFile($htmlFilePath);
    $xpath = new DOMXPath($dom);
    $text = '';

    $nodes = $xpath->query('//div[contains(@class, "' . $targetClass . '")]//*[not(self::script or self::style)]/text()');
    foreach ($nodes as $node) {
        $text .= trim($node->nodeValue) . "\n";
    }
    return $text;
}

function processHtmlFiles($directoryPath, $targetClass) {
    $files = scandir($directoryPath);
    $output = '';

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directoryPath . '/' . $file;

        if (pathinfo($filePath, PATHINFO_EXTENSION) === 'html') {
            $extractedText = extractTextFromHtml($filePath, $targetClass);
            $formattedText = formatText($extractedText);
            $fileKey = pathinfo($file, PATHINFO_FILENAME);

            // NDJSON形式で出力文字列を生成
            $jsonLine = json_encode([
                'id' => $fileKey,
                'content' => $formattedText,
                'page_url' => $filePath,
            ], JSON_UNESCAPED_UNICODE) . "\n";
            $output .= $jsonLine;
        }
    }
    return $output;
}

function formatText($text) {
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    $text = str_replace(["\t", "\r", "\n", "\o", "\x0B"], '', $text);
    return $text;
}

$sourceDirectory = 'output/ja';
$outputFile = 'learning_data.json'; // 出力ファイル名を変更
$targetClass = 'col-md-7'; // 抽出対象のclass名を指定


$ndjsonOutput = processHtmlFiles($sourceDirectory, $targetClass);

// NDJSON形式のデータをファイルに書き込み
file_put_contents($outputFile, $ndjsonOutput);

echo "データが {$outputFile} に保存されました。\n";

?>
