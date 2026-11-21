<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = get_json_input();
$productId = (int)($data['product_id'] ?? 0);
$author = trim((string)($data['author_name'] ?? ''));
$city = trim((string)($data['city'] ?? 'India'));
$rating = max(1, min(5, (int)($data['rating'] ?? 5)));
$title = trim((string)($data['title'] ?? ''));
$comment = trim((string)($data['comment'] ?? ''));

if ($productId <= 0 || $author === '' || $title === '' || $comment === '') {
    json_response(['error' => 'Please fill in your name, review title, and feedback.'], 422);
}

$check = $pdo->prepare('SELECT id FROM products WHERE id = ?');
$check->execute([$productId]);
if (!$check->fetch()) {
    json_response(['error' => 'Product not found'], 404);
}

$stmt = $pdo->prepare('INSERT INTO reviews (product_id, author_name, city, rating, title, comment, verified) VALUES (?, ?, ?, ?, ?, ?, 1)');
$stmt->execute([$productId, $author, $city, $rating, $title, $comment]);

$stats = $pdo->prepare('SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS cnt FROM reviews WHERE product_id = ?');
$stats->execute([$productId]);
$agg = $stats->fetch();

$upd = $pdo->prepare('UPDATE products SET rating = ?, review_count = ? WHERE id = ?');
$upd->execute([(float)$agg['avg_rating'], (int)$agg['cnt'], $productId]);

$rStmt = $pdo->prepare('SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC');
$rStmt->execute([$productId]);

json_response([
    'success' => true,
    'rating' => (float)$agg['avg_rating'],
    'review_count' => (int)$agg['cnt'],
    'reviews' => $rStmt->fetchAll(),
], 201);
