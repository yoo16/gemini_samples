<?php
// env.php を読み込み
require_once '../../env.php';

// 画像データ取得
$image = getImage();

// Gemini APIの場合
$data = createByAI($image);

// テストデータの場合
// $data = testData();

header('Content-Type: application/json');
echo $data;

/**
 * 画像取得
 */
function getImage()
{
    // アップロードされたファイルが存在し、エラーがないか確認
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileType = $_FILES['image']['type'];

        // 画像の種類をチェック
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedFileTypes)) {
            echo json_encode(['error' => '許可されていないファイル形式です。JPEG, PNG, GIFのみアップロード可能です。']);
            exit;
        }
        $data = ['path' => $fileTmpPath, 'mime_type' => $fileType];
        return $data;
    }
}

/**
 * Gemini API処理
 */
function createByAI($image)
{
    // プロンプト作成
    // TODO 欲しいJSONデータがレスポンスされるようにプロンプトを考える
    $prompt = "画像から料理レシピを作成（日本語）し、JSONフォーマットでレスポンス" . PHP_EOL;
    $prompt .= template();

    $image_base64 = base64_encode(file_get_contents($image['path']));
    $mime_type = $image['mime_type'];

    // データ作成
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mime_type,
                            'data' => $image_base64
                        ]
                    ]
                ]
            ]
        ]
    ];

    // リクエスト処理
    $ch = curl_init();
    // Google APIキー で Gemini APIのURL生成
    $uri = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . GEMINI_API_KEY;

    // Gemini AIにリクエスト
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
    } else {
        $response_data = json_decode($response, true);
        if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $response_data['candidates'][0]['content']['parts'][0]['text'];
            $json = str_replace(['```json', '```'], '', $text);
        }
    }
    curl_close($ch);
    return $json;
}

/**
 * AIの結果フォーマット
 */
function template()
{
    $template = '
    {
    "title": "xxxxxxxx",
    "description": "xxxxxxxxxxxx",
    "genre": "xxxx",
    "keywords": "xxx,xxx,xxx,xxx",
    "ingredients": [
        {
            "name": "xxxx",
            "quantity": "xxxx"
        },
        {
            "name": "xxxx",
            "quantity": "xxxx"
        }
    ]
    "steps": [
        {
            "stepNumber": 1,
            "instruction": "xxxx"
        },
        {
            "stepNumber": 2,
            "instruction": "xxxx"
        }
    ]
}';
    return $template;
}


function testData()
{
    $data = '
    {
        "title": "チキンカレー",
        "description": "簡単で美味しいチキンカレーのレシピです。",
        "genre": "カレー",
        "keywords": "チキン,カレー,簡単,スパイス",
        "ingredients": [
            {
                "name": "鶏肉",
                "quantity": "200g"
            },
            {
                "name": "玉ねぎ",
                "quantity": "1個"
            }
        ],
        "steps": [
            {
                "stepNumber": 1,
                "instruction": "鶏肉を一口大に切る。"
            },
            {
                "stepNumber": 2,
                "instruction": "玉ねぎをみじん切りにする。"
            }
        ]
    }';
    return $data;
}
