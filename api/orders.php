<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $orderNum = strtoupper(trim((string)($_GET['order_number'] ?? '')));
    if ($orderNum !== '') {
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE UPPER(order_number) = ?');
        $stmt->execute([$orderNum]);
        $order = $stmt->fetch();
        if (!$order) {
            json_response(['error' => 'No order found with number ' . $orderNum], 404);
        }
        $order['items'] = json_decode($order['items_json'], true) ?: [];
        unset($order['items_json']);
        json_response(['order' => $order]);
    }

    // List orders for Boutique Admin
    $rows = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 50')->fetchAll();
    foreach ($rows as &$r) {
        $r['items'] = json_decode($r['items_json'], true) ?: [];
        unset($r['items_json']);
    }
    json_response(['orders' => $rows]);
}

if ($method === 'POST') {
    $data = get_json_input();

    if (($data['action'] ?? '') === 'update_status') {
        $orderNumber = strtoupper(trim((string)($data['order_number'] ?? '')));
        $status = trim((string)($data['status'] ?? ''));
        $allowed = ['Confirmed', 'Hand-Packed with Ribbon', 'Dispatched', 'Out for Delivery', 'Delivered'];
        if (!in_array($status, $allowed, true)) {
            json_response(['error' => 'Invalid status'], 422);
        }
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE UPPER(order_number) = ?');
        $stmt->execute([$status, $orderNumber]);
        json_response(['success' => true, 'status' => $status]);
    }

    $name = trim((string)($data['customer_name'] ?? ''));
    $email = trim((string)($data['customer_email'] ?? ''));
    $phone = trim((string)($data['customer_phone'] ?? ''));
    $country = trim((string)($data['country'] ?? 'India'));
    $address = trim((string)($data['address_line'] ?? ''));
    $city = trim((string)($data['city'] ?? ''));
    $state = trim((string)($data['state'] ?? ''));
    $postal = trim((string)($data['postal_code'] ?? ''));
    $shippingMethod = trim((string)($data['shipping_method'] ?? 'Standard Pan-India'));
    $paymentMethod = trim((string)($data['payment_method'] ?? 'UPI / Instant Pay'));
    $couponCode = trim((string)($data['coupon_code'] ?? ''));
    $currency = trim((string)($data['currency'] ?? 'INR'));
    $giftWrap = !empty($data['gift_wrap']) ? 1 : 0;
    $giftNote = trim((string)($data['gift_note'] ?? ''));
    $rawItems = $data['items'] ?? [];

    if ($name === '' || $email === '' || $phone === '' || $address === '' || $city === '' || empty($rawItems)) {
        json_response(['error' => 'Please complete all required shipping details and ensure your bag has items.'], 422);
    }

    // Verify product prices server-side
    $verifiedItems = [];
    $subtotalInr = 0;
    $pStmt = $pdo->prepare('SELECT id, name, price_inr, images_json FROM products WHERE id = ?');

    foreach ($rawItems as $item) {
        $pid = (int)($item['id'] ?? 0);
        $qty = max(1, min(20, (int)($item['qty'] ?? 1)));
        $size = trim((string)($item['size'] ?? 'M'));

        $pStmt->execute([$pid]);
        $prod = $pStmt->fetch();
        if (!$prod) {
            continue;
        }
        $imgs = json_decode($prod['images_json'], true) ?: [];
        $lineTotal = (int)$prod['price_inr'] * $qty;
        $subtotalInr += $lineTotal;

        $verifiedItems[] = [
            'id' => (int)$prod['id'],
            'name' => $prod['name'],
            'size' => $size,
            'custom_measurements' => $item['custom_measurements'] ?? null,
            'qty' => $qty,
            'price_inr' => (int)$prod['price_inr'],
            'image' => $imgs[0] ?? 'assets/images/prod-blush-farshi.jpg',
        ];
    }

    if (empty($verifiedItems)) {
        json_response(['error' => 'No valid items found in order'], 422);
    }

    $discountInr = 0;
    $appliedCode = '';
    if ($couponCode !== '') {
        $couponEval = evaluate_coupon($couponCode, $subtotalInr);
        if ($couponEval['valid']) {
            $discountInr = $couponEval['discount_inr'];
            $appliedCode = $couponEval['code'];
        }
    }

    $afterDiscount = max(0, $subtotalInr - $discountInr);
    $shippingInr = calculate_shipping($country, $shippingMethod, $afterDiscount);
    $totalInr = $afterDiscount + $shippingInr;

    $orderNumber = 'PHV-2026-' . random_int(1100, 9999);

    $pdo->beginTransaction();
    try {
        $ins = $pdo->prepare("
            INSERT INTO orders (
                order_number, customer_name, customer_email, customer_phone, country,
                address_line, city, state, postal_code, shipping_method, payment_method,
                coupon_code, subtotal_inr, discount_inr, shipping_inr, total_inr,
                currency, gift_wrap, gift_note, status, items_json
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?)
        ");
        $ins->execute([
            $orderNumber, $name, $email, $phone, $country,
            $address, $city, $state, $postal, $shippingMethod, $paymentMethod,
            $appliedCode, $subtotalInr, $discountInr, $shippingInr, $totalInr,
            $currency, $giftWrap, $giftNote, json_encode($verifiedItems)
        ]);

        // Decrement stock gently (keep min 1 for demo continuity)
        $updStock = $pdo->prepare('UPDATE products SET stock = MAX(1, stock - ?) WHERE id = ?');
        foreach ($verifiedItems as $vi) {
            $updStock->execute([$vi['qty'], $vi['id']]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        json_response(['error' => 'Could not complete order: ' . $e->getMessage()], 500);
    }

    $created = $pdo->prepare('SELECT * FROM orders WHERE order_number = ?');
    $created->execute([$orderNumber]);
    $orderRow = $created->fetch();
    $orderRow['items'] = json_decode($orderRow['items_json'], true) ?: [];
    unset($orderRow['items_json']);

    json_response(['success' => true, 'order' => $orderRow], 201);
}

json_response(['error' => 'Method not allowed'], 405);
