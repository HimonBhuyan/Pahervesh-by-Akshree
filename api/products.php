<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $slug = trim((string)($_GET['slug'] ?? ''));

    if ($id > 0 || $slug !== '') {
        $stmt = $id > 0
            ? $pdo->prepare('SELECT * FROM products WHERE id = ?')
            : $pdo->prepare('SELECT * FROM products WHERE slug = ?');
        $stmt->execute([$id > 0 ? $id : $slug]);
        $product = $stmt->fetch();

        if (!$product) {
            json_response(['error' => 'Product not found'], 404);
        }

        $rStmt = $pdo->prepare('SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC');
        $rStmt->execute([$product['id']]);
        $reviews = $rStmt->fetchAll();

        $formatted = format_product_row($product);
        $formatted['reviews'] = $reviews;
        json_response(['product' => $formatted]);
    }

    $where = [];
    $params = [];

    $category = trim((string)($_GET['category'] ?? ''));
    if ($category !== '' && strcasecmp($category, 'All') !== 0) {
        if (strcasecmp($category, 'Pre-Orders') === 0) {
            $where[] = '(category = ? OR is_preorder = 1)';
            $params[] = 'Pre-Orders';
        } else {
            $where[] = 'category = ?';
            $params[] = $category;
        }
    }

    $search = trim((string)($_GET['q'] ?? ''));
    if ($search !== '') {
        $where[] = '(name LIKE ? OR subtitle LIKE ? OR craft LIKE ? OR fabric LIKE ? OR color_name LIKE ? OR category LIKE ?)';
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like, $like, $like, $like);
    }

    $maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
    if ($maxPrice > 0) {
        $where[] = 'price_inr <= ?';
        $params[] = $maxPrice;
    }

    $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;
    if ($featured === 1) {
        $where[] = 'is_featured = 1';
    }

    $sql = 'SELECT * FROM products';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sort = trim((string)($_GET['sort'] ?? 'featured'));
    $sql .= match ($sort) {
        'price_asc' => ' ORDER BY price_inr ASC, id ASC',
        'price_desc' => ' ORDER BY price_inr DESC, id ASC',
        'rating' => ' ORDER BY rating DESC, review_count DESC',
        'newest' => ' ORDER BY id DESC',
        default => ' ORDER BY is_featured DESC, id ASC',
    };

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $products = array_map('format_product_row', $rows);
    json_response(['products' => $products, 'total' => count($products)]);
}

if ($method === 'POST') {
    $data = get_json_input();
    $action = $data['action'] ?? 'create';

    if ($action === 'update_stock') {
        $id = (int)($data['id'] ?? 0);
        $stock = max(0, (int)($data['stock'] ?? 0));
        $stmt = $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?');
        $stmt->execute([$stock, $id]);
        json_response(['success' => true]);
    }

    $name = trim((string)($data['name'] ?? ''));
    $category = trim((string)($data['category'] ?? 'Suit Sets'));
    $price = (int)($data['price_inr'] ?? 0);
    if ($name === '' || $price <= 0) {
        json_response(['error' => 'Product name and valid INR price are required'], 422);
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . substr((string)time(), -4);
    $comparePrice = (int)($data['compare_price_inr'] ?? round($price * 1.2));
    $image = trim((string)($data['image'] ?? 'assets/images/prod-blush-farshi.jpg'));

    $stmt = $pdo->prepare("
        INSERT INTO products (
            slug, name, subtitle, category, collection_tag, price_inr, compare_price_inr,
            fabric, craft, color_name, color_hex, sizes_json, images_json, badge,
            rating, review_count, stock, is_featured, is_preorder, description, care_notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 5.0, 0, ?, 1, ?, ?, ?)
    ");
    $stmt->execute([
        $slug,
        $name,
        trim((string)($data['subtitle'] ?? 'Handcrafted Pure Cotton Ensemble')),
        $category,
        trim((string)($data['collection_tag'] ?? 'New Arrival')),
        $price,
        $comparePrice,
        trim((string)($data['fabric'] ?? '100% Pure Cotton')),
        trim((string)($data['craft'] ?? 'Hand-Block Print')),
        trim((string)($data['color_name'] ?? 'Rose Ivory')),
        trim((string)($data['color_hex'] ?? '#D98C96')),
        json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
        json_encode([$image, 'assets/images/brand-packaging.jpg']),
        trim((string)($data['badge'] ?? 'New Drop')),
        max(1, (int)($data['stock'] ?? 10)),
        $category === 'Pre-Orders' ? 1 : 0,
        trim((string)($data['description'] ?? 'Artisanal cotton wear crafted with love by Pahervesh by Akshree.')),
        'Gentle hand wash in cold water. Dry in shade.'
    ]);

    json_response(['success' => true, 'id' => (int)$pdo->lastInsertId()], 201);
}

json_response(['error' => 'Method not allowed'], 405);
