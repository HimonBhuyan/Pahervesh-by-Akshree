<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/collection_view.php';

$requestedCat = trim((string)($_GET['category'] ?? 'All'));
$allowed = ['All', 'Farshi Sets', 'Suit Sets', 'Co-ord Sets', 'Office Kurtis', 'Pre-Orders'];
if (!in_array($requestedCat, $allowed, true)) {
    $requestedCat = 'All';
}

render_collection_page([
    'category' => $requestedCat,
    'activeNav' => 'shop',
    'title' => 'Shop All Collections | PAHERVESH by Akshree',
    'description' => 'Browse all pure cotton Farshi Sets, Mulmul Suit Sets, Co-ord Sets, Office Kurtis, and Pre-Orders by Pahervesh by Akshree.',
    'kicker' => 'Complete Wardrobe',
    'heading' => $requestedCat === 'All' ? 'Shop All Cotton Ensembles' : $requestedCat,
    'lead' => 'Every garment is cut from breathable 60s cambric & mulmul cotton, hand-block printed by Jaipur artisans, and shipped across India & Worldwide.',
    'heroImage' => 'assets/images/hero-editorial.jpg',
    'highlights' => ['100% Pure Cotton', 'India & Worldwide Shipping', 'Custom Tailoring Available'],
]);
