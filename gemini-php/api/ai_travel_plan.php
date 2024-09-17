<?php
// env.php を読み込み
require_once '../env.php';

$posts = json_decode(file_get_contents('php://input'), true);

// Gemini APIの場合
$data = createByAI($posts);

// テストデータの場合
// $data = testData();

header('Content-Type: application/json');
echo $data;

function createByAI($conditions)
{
    if (!$conditions) return;

    // Google APIキー
    $api_key = GEMINI_API_KEY;

    // TODO 欲しいJSONデータがレスポンスされるようにプロンプトを考える    
    $prompot = "つぎの条件で旅行プランをJSONのみでレスポンス" . PHP_EOL;
    $prompot .= "departure: {$conditions['departure']}" . PHP_EOL;
    $prompot .= "destination: {$conditions['destination']}" . PHP_EOL;
    $prompot .= "departureDate: {$conditions['destination']}" . PHP_EOL;
    $prompot .= "arrivalDate: {$conditions['destination']}" . PHP_EOL;
    $prompot .= "budget: {$conditions['budget']}" . PHP_EOL;
    $prompot .= "keywords: {$conditions['keywords']}" . PHP_EOL;
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

// AIの結果を想定（テストデータ）
function testData()
{
    $data = '
{
    "departure": "東京",
    "destination": "札幌",
    "departureDate": "2024-03-01",
    "arrivalDate": "2024-03-05",
    "budget": "50000",
    "keywords": "観光",
    "planItems": [
        [
            {
                "date": "2024-03-01",
                "transportation": "新幹線",
                "place": "東京駅",
                "activity": "到着",
                "memo": "ホテルにチェックイン"
            },
            {
                "date": "2024-03-01",
                "transportation": "徒歩",
                "place": "東京スカイツリー",
                "activity": "展望台",
                "memo": "東京の景色を満喫"
            },
            {
                "date": "2024-03-01",
                "transportation": "徒歩",
                "place": "浅草寺",
                "activity": "参拝",
                "memo": "雷門を通って仲見世通りを散策"
            }
        ],
        [
            {
                "date": "2024-03-02",
                "transportation": "電車",
                "place": "上野動物園",
                "activity": "動物鑑賞",
                "memo": "パンダに会いに行く"
            },
            {
                "date": "2024-03-02",
                "transportation": "徒歩",
                "place": "上野公園",
                "activity": "散歩",
                "memo": "桜並木を歩く"
            },
            {
                "date": "2024-03-02",
                "transportation": "電車",
                "place": "秋葉原",
                "activity": "買い物",
                "memo": "電気街で買い物を楽しむ"
            }
        ],
        [
            {
                "date": "2024-03-03",
                "transportation": "電車",
                "place": "渋谷",
                "activity": "ショッピング",
                "memo": "流行の洋服や雑貨を見る"
            },
            {
                "date": "2024-03-03",
                "transportation": "徒歩",
                "place": "渋谷スクランブル交差点",
                "activity": "散策",
                "memo": "スクランブル交差点を渡る"
            },
            {
                "date": "2024-03-03",
                "transportation": "電車",
                "place": "新宿",
                "activity": "ディナー",
                "memo": "新宿ゴールデン街で食事"
            }
        ],
        [
            {
                "date": "2024-03-04",
                "transportation": "電車",
                "place": "築地市場",
                "activity": "市場見学",
                "memo": "新鮮な魚介類を食べる"
            },
            {
                "date": "2024-03-04",
                "transportation": "電車",
                "place": "皇居",
                "activity": "散策",
                "memo": "皇居東御苑を歩く"
            },
            {
                "date": "2024-03-04",
                "transportation": "電車",
                "place": "銀座",
                "activity": "買い物",
                "memo": "高級ブランドショップを巡る"
            }
        ],
        [
            {
                "date": "2024-03-05",
                "transportation": "新幹線",
                "place": "東京駅",
                "activity": "出発",
                "memo": "お土産を買って帰路につく"
            }
        ]
    ]
}';
    return $data;
}


function template()
{
    $template = '
{
        "departure": "xxxx",
        "destination": "xxxx",
        "departureDate": "xxxx-xx-xx",
        "arrivalDate": "xxxx-xx-xx",
        "budget": "xxxxxx",
        "keywords": "xxxx, xxxx, xxxx",
        "planItems": [
            [
                {
                    "date": "xxxx-xx-xx",
                    "transportation": "xxxx",
                    "place": "xxxx",
                    "activity": "xxxx",
                    "memo": "xxxxxxxx"
                },
                {
                    "date": "xxxx-xx-xx",
                    "transportation": "xxxx",
                    "place": "xxxx",
                    "activity": "xxxx",
                    "memo": "xxxxxxxx"
                }
            ],
            [
                {
                    "date": "xxxx-xx-xx",
                    "transportation": "xxxx",
                    "place": "xxxx",
                    "activity": "xxxx",
                    "memo": "xxxxxxxx"
                },
                {
                    "date": "xxxx-xx-xx",
                    "transportation": "xxxx",
                    "place": "xxxx",
                    "activity": "xxxx",
                    "memo": "xxxxxxxx"
                }
            ]
        ]
    }';
    return $template;
}
