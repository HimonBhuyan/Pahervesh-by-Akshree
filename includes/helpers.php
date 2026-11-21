<?php
declare(strict_types=1);

/**
 * Shared helpers for Pahervesh by Akshree
 */

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function get_json_input(): array
{
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return $_POST ?: [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function format_product_row(array $row): array
{
    $row['id'] = (int)$row['id'];
    $row['price_inr'] = (int)$row['price_inr'];
    $row['compare_price_inr'] = (int)$row['compare_price_inr'];
    $row['rating'] = (float)$row['rating'];
    $row['review_count'] = (int)$row['review_count'];
    $row['stock'] = (int)$row['stock'];
    $row['is_featured'] = (bool)$row['is_featured'];
    $row['is_preorder'] = (bool)$row['is_preorder'];
    $row['sizes'] = json_decode($row['sizes_json'] ?? '[]', true) ?: [];
    $row['images'] = json_decode($row['images_json'] ?? '[]', true) ?: [];
    unset($row['sizes_json'], $row['images_json']);
    return $row;
}

function evaluate_coupon(string $code, int $subtotalInr): array
{
    $clean = strtoupper(trim($code));
    if ($clean === '') {
        return ['valid' => false, 'discount_inr' => 0, 'message' => 'Enter a coupon code'];
    }

    $coupons = [
        'AKSHREE10' => ['type' => 'percent', 'value' => 10, 'min' => 999, 'label' => '10% Off First Order'],
        'PAHERVESH15' => ['type' => 'percent', 'value' => 15, 'min' => 3500, 'label' => '15% Off Orders ₹3,500+'],
        'WELCOME500' => ['type' => 'flat', 'value' => 500, 'min' => 2499, 'label' => '₹500 Off Orders ₹2,499+'],
    ];

    if (!isset($coupons[$clean])) {
        return ['valid' => false, 'discount_inr' => 0, 'message' => 'Invalid coupon code. Try AKSHREE10'];
    }

    $c = $coupons[$clean];
    if ($subtotalInr < $c['min']) {
        return [
            'valid' => false,
            'discount_inr' => 0,
            'message' => sprintf('Minimum order ₹%s required for %s', number_format($c['min']), $clean),
        ];
    }

    $discount = $c['type'] === 'percent'
        ? (int)round($subtotalInr * ($c['value'] / 100))
        : (int)$c['value'];

    $discount = min($discount, $subtotalInr);

    return [
        'valid' => true,
        'code' => $clean,
        'discount_inr' => $discount,
        'label' => $c['label'],
        'message' => sprintf('%s applied! You save ₹%s', $clean, number_format($discount)),
    ];
}

function calculate_shipping(string $country, string $method, int $subtotalAfterDiscount): int
{
    $isIndia = strcasecmp(trim($country), 'India') === 0;
    if ($isIndia) {
        if ($method === 'Boutique Pickup (Jaipur)') {
            return 0;
        }
        if ($method === 'Express Pan-India Air') {
            return $subtotalAfterDiscount >= 3999 ? 0 : 199;
        }
        return $subtotalAfterDiscount >= 1999 ? 0 : 120;
    }

    // Worldwide shipping
    return $subtotalAfterDiscount >= 12000 ? 0 : 1490;
}
