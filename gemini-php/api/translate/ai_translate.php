<?php
// env.php を読み込み
require_once '../../env.php';
require_once '../../lib/Lang.php';

// CORS設定
header("Access-Control-Allow-Origin: *"); // 必要に応じて "*" を特定のオリジンに変更
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// PHPでPOSTリクエストからデータを受け取る
$postData = json_decode(file_get_contents('php://input'), true);

// 送信されてきたデータを取得
$origin = isset($postData['origin']) ? $postData['origin'] : null;
$fromLang = isset($postData['fromLang']) ? $postData['fromLang'] : null;
$toLang = isset($postData['toLang']) ? $postData['toLang'] : null;

// Gemini APIの場合
$translate = createByAI($origin, $fromLang, $toLang);

// テストデータの場合
// $translate = testTranslateData();

$data['origin'] = $origin;
$data['translate'] = $translate;
$data['fromLang'] = $fromLang;
$data['toLang'] = $toLang;

header('Content-Type: application/json');
$json = json_encode($data);
echo $json;

function createByAI($origin, $fromLang, $toLang)
{
    // Google APIキー
    $api_key = GEMINI_API_KEY;

    $fromLang = Lang::getByCode($fromLang);
    $toLang = Lang::getByCode($toLang);

    $prompt = "Please translate from {$fromLang} to {$toLang} 
    without bracket character.
    If it cannot be translated, 
    please return it as it cannot be translated in {$toLang}.
    \n {$origin}";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
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
            $translate = $response_data['candidates'][0]['content']['parts'][0]['text'];
        }
    }
    curl_close($ch);
    return $translate;
}

// AIの結果を想定（テストデータ）
function testTranslateData()
{
    $data = "Hello";
    return $data;
}
