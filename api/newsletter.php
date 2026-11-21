<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = get_json_input();
$email = strtolower(trim((string)($data['email'] ?? '')));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['error' => 'Please enter a valid email address.'], 422);
}

$pdo = get_db();
$stmt = $pdo->prepare('INSERT OR IGNORE INTO newsletter_subscribers (email) VALUES (?)');
$stmt->execute([$email]);

json_response([
    'success' => true,
    'message' => 'Welcome to the Pahervesh Inner Circle! Use code AKSHREE10 at checkout for 10% off.',
    'coupon' => 'AKSHREE10',
]);
