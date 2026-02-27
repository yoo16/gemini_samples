<?php
// env.php を読み込み
require_once '../../env.php';

// JSONデータ取得
$posts = json_decode(file_get_contents('php://input'), true);

// Gemini APIの場合
$data = createByAI($posts);

// テストデータの場合
// $data = testData();

header('Content-Type: application/json');
echo $data;

/**
 * Gemini API処理
 */
function createByAI($conditions)
{
    // プロンプト作成
    // TODO 欲しいJSONデータがレスポンスされるようにプロンプトを考える
    $prompt = "つぎの条件でレシピをJSONのみでレスポンス" . PHP_EOL;
    $prompt .= "ジャンル: {$conditions['genre']}" . PHP_EOL;
    $prompt .= "時間帯: {$conditions['time']}" . PHP_EOL;
    $prompt .= "キーワード: {$conditions['keywords']}" . PHP_EOL;
    $prompt .= "JSONテンプレート:" . PHP_EOL;
    $prompt .= template();


    // データ作成
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
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