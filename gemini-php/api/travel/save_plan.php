<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// POSTメソッドかどうかを確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // メソッドが許可されていない
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit;
}

// 受け取ったJSONデータを取得
// $inputJSON = file_get_contents('php://input');

// テストデータ
$inputJSON = testData();

// JSONでコード（JSON -> PHPオブジェクト）
$planData = json_decode($inputJSON, true);

// データのバリデーション
if (!isset($planData['plan'])) {
    http_response_code(400); // バッドリクエスト
    echo json_encode(['message' => 'Invalid plan']);
    exit;
}

if (!isset($planData['plan_items'])) {
    http_response_code(400); // バッドリクエスト
    echo json_encode(['message' => 'Invalid plan_items']);
    exit;
}

// データベース接続設定
$host = 'localhost';
$dbname = 'travel-planner';
$user = 'root';
$pass = '';

$info = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    // PDOを使用してデータベースに接続
    $pdo = new PDO($info, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // トランザクションを開始
    $pdo->beginTransaction();

    // plansテーブルにデータをINSERT
    $plan = $planData['plan'];
    $stmt = $pdo->prepare("
        INSERT INTO plans (departure, destination, departureDate, arrivalDate, budget, keywords)
        VALUES (:departure, :destination, :departureDate, :arrivalDate, :budget, :keywords)
    ");
    $stmt->execute([
        ':departure' => $plan['departure'],
        ':destination' => $plan['destination'],
        ':departureDate' => $plan['departureDate'],
        ':arrivalDate' => $plan['arrivalDate'],
        ':budget' => $plan['budget'],
        ':keywords' => $plan['keywords']
    ]);

    // 新しく作成されたplanのIDを取得
    $planId = $pdo->lastInsertId();

    // plan_itemsテーブルにデータをINSERT
    $planItems = $planData['plan_items'];
    $stmt = $pdo->prepare("
        INSERT INTO plan_items (plan_id, date, place, activity, accommodation, transportation, budget, memo, `order`)
        VALUES (:plan_id, :date, :place, :activity, :accommodation, :transportation, :budget, :memo, :order)
    ");

    foreach ($planItems as $item) {
        $stmt->execute([
            ':plan_id' => $planId,
            ':date' => $item['date'],
            ':place' => $item['place'],
            ':activity' => $item['activity'],
            ':accommodation' => $item['accommodation'],
            ':transportation' => $item['transportation'],
            ':budget' => $item['budget'],
            ':memo' => $item['memo'],
            ':order' => $item['order']
        ]);
    }

    // トランザクションをコミット
    $pdo->commit();

    // 成功メッセージを返却
    echo json_encode(['message' => 'Plan and items saved successfully', 'planId' => $planId]);
} catch (PDOException $e) {
    // エラーが発生した場合はロールバック
    $pdo->rollBack();
    http_response_code(500); // 内部サーバーエラー
    echo json_encode(['message' => 'Failed to save plan', 'error' => $e->getMessage()]);
}


function testData()
{
    $data = '
{
    "plan": {
        "departure": "東京",
        "destination": "札幌",
        "departureDate": "2024-03-01",
        "arrivalDate": "2024-03-05",
        "budget": "50000",
        "keywords": "観光"
    },
    "plan_items": [
        {
            "date": "2024-03-01 10:00",
            "transportation": "新幹線",
            "place": "東京駅",
            "activity": "到着",
            "memo": "ホテルにチェックイン"
        },
        {
            "date": "2024-03-01 11:00",
            "transportation": "徒歩",
            "place": "東京スカイツリー",
            "activity": "展望台",
            "memo": "東京の景色を満喫"
        },
        {
            "date": "2024-03-01 12:00",
            "transportation": "徒歩",
            "place": "浅草寺",
            "activity": "参拝",
            "memo": "雷門を通って仲見世通りを散策"
        },
        {
            "date": "2024-03-02 13:00",
            "transportation": "電車",
            "place": "上野動物園",
            "activity": "動物鑑賞",
            "memo": "パンダに会いに行く"
        },
        {
            "date": "2024-03-02 14:00",
            "transportation": "徒歩",
            "place": "上野公園",
            "activity": "散歩",
            "memo": "桜並木を歩く"
        },
        {
            "date": "2024-03-02 15:00",
            "transportation": "電車",
            "place": "秋葉原",
            "activity": "買い物",
            "memo": "電気街で買い物を楽しむ"
        },
        {
            "date": "2024-03-03 10:00",
            "transportation": "電車",
            "place": "渋谷",
            "activity": "ショッピング",
            "memo": "流行の洋服や雑貨を見る"
        },
        {
            "date": "2024-03-03 11:00",
            "transportation": "徒歩",
            "place": "渋谷スクランブル交差点",
            "activity": "散策",
            "memo": "スクランブル交差点を渡る"
        },
        {
            "date": "2024-03-03 12:00",
            "transportation": "電車",
            "place": "新宿",
            "activity": "ディナー",
            "memo": "新宿ゴールデン街で食事"
        },
        {
            "date": "2024-03-04 13:00",
            "transportation": "電車",
            "place": "築地市場",
            "activity": "市場見学",
            "memo": "新鮮な魚介類を食べる"
        },
        {
            "date": "2024-03-04 14:00",
            "transportation": "電車",
            "place": "皇居",
            "activity": "散策",
            "memo": "皇居東御苑を歩く"
        },
        {
            "date": "2024-03-04 15:00",
            "transportation": "電車",
            "place": "銀座",
            "activity": "買い物",
            "memo": "高級ブランドショップを巡る"
        },
        {
            "date": "2024-03-05 16:00",
            "transportation": "新幹線",
            "place": "東京駅",
            "activity": "出発",
            "memo": "お土産を買って帰路につく"
        }
    ]
}';
    return $data;
}
