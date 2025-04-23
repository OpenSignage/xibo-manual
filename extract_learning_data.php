<?php

function extractTextFromHtml($htmlFilePath, $targetClass) {
    $dom = new DOMDocument();
    @$dom->loadHTMLFile($htmlFilePath);
    $xpath = new DOMXPath($dom);
    $text = '';

    $nodes = $xpath->query("//div[@class='col-md-7']//text()[not(parent::script) and not(parent::style)]");
    foreach ($nodes as $node) {
        $trimmed = trim($node->nodeValue);
        if ($trimmed !== '') {
            $text .= $trimmed . "\n";
        }
    }
    return $text;
}

function processHtmlFiles($directoryPath, $targetClass) {
    $files = scandir($directoryPath);
    $output = [];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directoryPath . '/' . $file;

        if (pathinfo($filePath, PATHINFO_EXTENSION) === 'html') {
            $extractedText = extractTextFromHtml($filePath, $targetClass);
            $formattedText = formatText($extractedText);
            $fileKey = pathinfo($file, PATHINFO_FILENAME);

            // 配列にオブジェクトを追加
            $output[] = [
                'id' => $fileKey,
                'content' => $formattedText,
                'page_url' => $filePath,
            ];
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
$outputFile = 'ai/learning_data.json';
$targetClass = 'col-md-7';

$output = processHtmlFiles($sourceDirectory, $targetClass);

// JSON形式でデータをファイルに書き込み
file_put_contents($outputFile, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "データが {$outputFile} に保存されました。\n";

?>
