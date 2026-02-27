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
$inputJSON = file_get_contents('php://input');
$recipeData = json_decode($inputJSON, true);

// データのバリデーション
if (!isset($recipeData['title']) || !isset($recipeData['ingredients']) || !isset($recipeData['steps'])) {
    http_response_code(400); // バッドリクエスト
    echo json_encode(['message' => 'Invalid recipe data']);
    exit;
}

// テスト返却
// echo json_encode($recipeData);
// exit;

// データベース接続設定
$host = 'localhost';
$dbname = 'recipe-creator';
$user = 'root';
$pass = '';

$info = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    // PDOを使用してデータベースに接続
    $pdo = new PDO($info, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // トランザクション開始
    $pdo->beginTransaction();

    // レシピを保存
    $sql = 'INSERT INTO recipes (title, description, genre, keywords) VALUES (?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $recipeData['title'],
        $recipeData['description'] ?? '',
        $recipeData['genre'] ?? '',
        $recipeData['keywords'] ?? ''
    ]);

    $recipeId = $pdo->lastInsertId();

    $sql = 'INSERT INTO ingredients (recipeId, name, quantity) VALUES (?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    foreach ($recipeData['ingredients'] as $ingredient) {
        $stmt->execute([
            $recipeId,
            $ingredient['name'],
            $ingredient['quantity']
        ]);
    }
    $stmt = $pdo->prepare('INSERT INTO steps (recipeId, stepNumber, instruction) VALUES (?, ?, ?)');
    foreach ($recipeData['steps'] as $step) {
        $stmt->execute([
            $recipeId,
            $step['stepNumber'],
            $step['instruction']
        ]);
    }

    $pdo->commit();
    echo json_encode(['message' => 'Recipe saved successfully', 'recipeId' => $recipeId]);
} catch (PDOException $e) {
    // エラー時はトランザクションをロールバック
    $pdo->rollBack();
    http_response_code(500); // 内部サーバーエラー
    echo json_encode(['message' => 'Failed to save recipe', 'error' => $e->getMessage()]);
}
?>