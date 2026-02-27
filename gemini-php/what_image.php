<?php
// env.php を読み込み
require_once 'env.php';

// リクエストメソッドがPOSTの場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $results = chat();
}

/**
 * Gemini APIを使用して画像をテキストに変換
 */
function chat()
{
    // 画像ファイルのパスチェック
    if (!isset($_FILES['image']) || !isset($_FILES['image']['tmp_name'])) {
        return 'No file uploaded.';
    }
    $image_path = $_FILES['image']['tmp_name'];

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

    // cURLセッションを初期化
    $ch = curl_init();
    // ベースURL
    $base_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent";
    // Google APIキー
    $api_key = GEMINI_API_KEY;
    // URL
    $url = "{$base_url}?key={$api_key}";
    // リクエストのオプションを設定
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Gemini APIリクエスト&レスポンス
    $response = curl_exec($ch);

    // 結果
    $results = [
        'text' => '',
        'error' => ''
    ];
    // エラーが発生した場合
    if (curl_errno($ch)) {
        $results['error'] = 'Error:' . curl_error($ch);
    } else {
        // レスポンスをデコード
        $data = json_decode($response, true);
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $results['text'] = nl2br(htmlspecialchars($data['candidates'][0]['content']['parts'][0]['text']));
        }
    }
    // cURLセッションを閉じる
    curl_close($ch);

    // resultsを返す
    return $results;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- tailwind.css cdn -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <main class="container mx-auto mt-10">
        <div class="flex justify-center mb-8">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="image" class="border border-gray-300 p-2">
                <input type="hidden" name="MAX_FILE_SIZE" value="30000">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    送信
                </button>
            </form>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-3xl">Text</h3>
            <p><?= @$results['text'] ?></p>
        </div>
    </main>
</body>

</html>