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

// 設定ファイルのパスを定義
$configFile = 'translateConfig.json';

// 設定を読み込む関数
function loadConfig(string $filename): array {
    if (file_exists($filename)) {
        $config = json_decode(file_get_contents($filename), true);
    }
    
    // デフォルト設定
    return array_merge([
        'sourceLanguage' => 'en',
        'targetLanguage' => 'ja',
        'inputDir' => 'source/en/',
        'outputDir' => 'source/ja/'
    ], $config ?? []);
}

// 設定を読み込み
$config = loadConfig($configFile);

// apikey.txt ファイルから API キーを読み込みます
$apiKey = trim(file_get_contents('apikey.txt'));

$inputDir = $config['inputDir'];
$outputDir = $config['outputDir'];
$targetLanguage = $config['targetLanguage'];

// 出力ディレクトリが存在しない場合は作成します
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// コマンドライン引数から入力ファイル名を取得します
$inputFiles = $argv;
array_shift($inputFiles); // スクリプト名を除外

// 入力ファイル名の指定がない場合は、source/en/ の全ての .md ファイルを対象とします
if (empty($inputFiles)) {
    $inputFiles = glob($inputDir . '*.md');
} else {
    // 指定されたファイル名に source/en/ ディレクトリを追加します
    foreach ($inputFiles as &$inputFile) {
        $inputFile = $inputDir . $inputFile;
    }
}

// グロッサリーファイルのパスを定義
$glossaryFile = 'glossary.json';

// グロッサリーを読み込む関数
function loadGlossary(string $filename): array {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true) ?? [];
    }
    return [];
}

// グロッサリーを適用する関数
function applyGlossary(string $text, array $glossary): string {
    foreach ($glossary as $source => $target) {
        $text = str_replace($source, $target, $text);
    }
    return $text;
}

// グロッサリーを読み込み
$glossary = loadGlossary($glossaryFile);

// 翻訳除外リストのファイルパスを定義
$excludeFile = 'exclude.json';

// 翻訳除外リストを読み込む関数
function loadExcludeList(string $filename): array {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true) ?? [];
    }
    return [];
}

// 翻訳除外単語を一時的にプレースホルダーに置き換える関数
function protectExcludedWords(string $text, array $excludeList): array {
    $replacements = [];
    foreach ($excludeList as $word) {
        $placeholder = '___PROTECTED_' . md5($word) . '___';
        $text = str_replace($word, $placeholder, $text);
        $replacements[$placeholder] = $word;
    }
    return ['text' => $text, 'replacements' => $replacements];
}

// プレースホルダーを元の単語に戻す関数
function restoreExcludedWords(string $text, array $replacements): string {
    return str_replace(array_keys($replacements), array_values($replacements), $text);
}

// 翻訳除外リストを読み込み
$excludeList = loadExcludeList($excludeFile);

/**
 * Google Cloud Translation API を使用してテキストを翻訳します。
 *
 * @param string $text 翻訳するテキスト
 * @param string $apiKey API キー
 * @param string $targetLang 翻訳先言語コード
 * @return string 翻訳されたテキスト
 */
function translateText(string $text, string $apiKey, string $targetLang = 'ja'): string {
    global $glossary, $excludeList;
    
    // グロッサリーを適用
    $text = applyGlossary($text, $glossary);
    
    // 除外単語を保護
    $protected = protectExcludedWords($text, $excludeList);
    $text = $protected['text'];
    
    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey;
    $data = [
        'q' => $text,
        'source' => 'en',
        'target' => $targetLang,
        'format' => 'text'
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);
    
    // 翻訳結果から除外単語を復元
    return restoreExcludedWords($response['data']['translations'][0]['translatedText'], $protected['replacements']);
}

// 各入力ファイルに対して翻訳を実行します
foreach ($inputFiles as $inputFile) {
    // ファイルの内容を読み込みます
    $content = file_get_contents($inputFile);

    // Google Cloud Translation API を使用して翻訳します
    $translatedContent = translateText($content, $apiKey, $targetLanguage);

    // 翻訳された内容を、outputDir に元のファイル名と同じ名前のファイルとして保存します
    $outputFilename = basename($inputFile);
    $outputFile = $outputDir . $outputFilename;
    file_put_contents($outputFile, $translatedContent);

    echo "ファイル '$inputFile' を {$targetLanguage} に翻訳し、'$outputFile' に保存しました。\n";
}
?>
