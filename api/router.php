<?php
declare(strict_types=1);

/**
 * Vercel PHP Front-Controller Router
 * Maps /, /<page>.php, and extensionless /<page> routes to root PHP pages.
 */

$rootDir = dirname(__DIR__);
chdir($rootDir);

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$clean = ltrim($uriPath, '/');

if ($clean === '' || $clean === 'index' || $clean === 'index.php') {
    $target = 'index.php';
} else {
    $target = str_ends_with($clean, '.php') ? $clean : $clean . '.php';
}

$allowedPages = [
    'index.php',
    'shop.php',
    'farshi-sets.php',
    'suit-sets.php',
    'coord-sets.php',
    'kurtis.php',
    'lookbook.php',
    'track-order.php',
    'custom-fit.php',
    'admin.php',
];

if (!in_array($target, $allowedPages, true)) {
    http_response_code(404);
    $target = 'index.php';
}

require $rootDir . '/' . $target;
