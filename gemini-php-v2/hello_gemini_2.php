<?php
// env.php を読み込み
require_once 'env.php';

// Google APIキー
$api_key = GEMINI_API_KEY;

// 画像ファイルのパス
$image_path = './images/sample1.jpg';

// 画像をBase64にエンコード
$image_base64 = base64_encode(file_get_contents($image_path));

// リクエストのペイロードを作成
$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'この写真はなんですか？'],
                [
                    'inline_data' => [
                        'mime_type' => 'image/jpeg',
                        'data' => $image_base64
                    ]
                ]
            ]
        ]
    ]
];

// RESTプログラム
// cURLセッションを初期化
$ch = curl_init();

// リクエストのオプションを設定
curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Gemini APIリクエスト&レスポンス
$response = curl_exec($ch);

// エラーが発生した場合
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // レスポンスを表示
    echo $response;
}

// cURLセッションを閉じる
curl_close($ch);
?>