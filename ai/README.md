# Xiboマニュアル検索エンジンの処理フロー

## 1. 初期設定
- エラー表示の有効化
- 実行時間とメモリ制限の設定
- デバッグログの設定

## 2. 主要な機能コンポーネント

### a) システムプロンプトの管理
```php
function loadSystemPrompt() {
    // システムプロンプトファイルを読み込み
    // 存在しない場合はデフォルトプロンプトを返す
}
```

### b) マニュアルデータの管理
```php
function loadManualData() {
    // JSONファイルからマニュアルデータを読み込む
    // エラー処理を含む
}
```

### c) コンテキスト検索
```php
function findRelevantContext($query, $manualData) {
    // ユーザーの質問に関連するマニュアルセクションを検索
    // キーワードマッチングによるスコアリング
    // 最も関連性の高いセクションを返す
}
```

## 3. メインの処理フロー

### a) リクエスト処理
- POSTリクエストの受け付け
- ユーザーの質問を取得

### b) Gemini AIとの連携
```php
function queryGeminiAIStreaming($userPrompt, $config, $credentialsPath) {
    // 1. SSE（Server-Sent Events）のヘッダー設定
    // 2. APIキーの取得
    // 3. マニュアルデータの読み込みと関連コンテキストの検索
    // 4. Gemini APIへのリクエスト
    // 5. レスポンスの処理とストリーミング
}
```

## 4. 補助機能
- `preprocessQuery`: クエリの前処理
- `extractKeywords`: キーワード抽出
- `calculateRelevanceScore`: 関連性スコアの計算
- `summarizeText`: テキストの要約
- `improveTextReadability`: 読みやすさの改善

## 処理の流れ
1. ユーザーの質問を受信
2. マニュアルデータから関連する情報を検索
3. Gemini AIを使用して回答を生成
4. 回答を整形してServer-Sent Eventsでクライアントにストリーミング

※ エラー処理やログ記録も適切に実装されており、堅牢なシステムとなっています。 