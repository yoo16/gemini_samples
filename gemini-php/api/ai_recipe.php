<?php
// env.php を読み込み
require_once '../env.php';

// Gemini APIの場合
$posts = json_decode(file_get_contents('php://input'), true);
$data = createByAI($posts);

// テストデータの場合
// $data = testData();

header('Content-Type: application/json');
echo $data;

function createByAI($conditions)
{
    // Google APIキー
    $api_key = GEMINI_API_KEY;

    // TODO 欲しいJSONデータがレスポンスされるようにプロンプトを考える
    $prompot = "つぎの条件でレシピをJSONのみでレスポンス" . PHP_EOL;
    $prompot .= "ジャンル: {$conditions['genre']}" . PHP_EOL;
    $prompot .= "時間帯: {$conditions['time']}" . PHP_EOL;
    $prompot .= "キーワード: {$conditions['keywords']}" . PHP_EOL;
    $prompot .= "JSONテンプレート" . PHP_EOL;
    $prompot .= template();

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompot],
                ]
            ]
        ]
    ];

    // TODO Gemini AI処理
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key);
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
