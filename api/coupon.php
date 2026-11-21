<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = get_json_input();
$code = (string)($data['code'] ?? '');
$subtotal = max(0, (int)($data['subtotal_inr'] ?? 0));

$result = evaluate_coupon($code, $subtotal);
json_response($result, $result['valid'] ? 200 : 422);
