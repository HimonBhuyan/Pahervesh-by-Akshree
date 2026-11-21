<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = get_json_input();
$name = trim((string)($data['name'] ?? ''));
$contact = trim((string)($data['contact'] ?? ''));
$productName = trim((string)($data['product_name'] ?? 'Custom / Pre-Order Enquiry'));
$measurements = $data['measurements'] ?? [];
$notes = trim((string)($data['notes'] ?? ''));

if ($name === '' || $contact === '' || $notes === '') {
    json_response(['error' => 'Please enter your name, phone/Instagram handle, and styling notes.'], 422);
}

$pdo = get_db();
$stmt = $pdo->prepare('INSERT INTO custom_enquiries (name, contact, product_name, measurements_json, notes) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$name, $contact, $productName, json_encode($measurements), $notes]);

json_response([
    'success' => true,
    'message' => 'Thank you! Akshree’s styling desk has received your bespoke request and will reach out within 4 hours.',
], 201);
