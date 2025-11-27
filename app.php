<?php
require_once 'config.php';

// Simple routing for v2
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/SupporTracker', '', $path);
$path = trim($path, '/');

// Default to dashboard
if (empty($path)) {
    $path = 'dashboard';
}

// Handle special routes
if ($path === 'assets/create') {
    $controllerFile = "controllers/asset-create.php";
} elseif ($path === 'tickets/create') {
    $controllerFile = "controllers/ticket-create.php";
} elseif (strpos($path, 'ticket-detail') === 0) {
    $controllerFile = "controllers/ticket-detail.php";
} elseif (preg_match('/^invoices\/(\d+)$/', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $controllerFile = "controllers/invoice_detail.php";
} elseif (preg_match('/^customers\/(\d+)$/', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $controllerFile = "controllers/customers.php";
} elseif (preg_match('/^customers\/(\d+)$/', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $controllerFile = "controllers/customers.php";
} else {
    // Route to controllers
    $controllerFile = "controllers/{$path}.php";
}

if (file_exists($controllerFile)) {
    include $controllerFile;
} else {
    http_response_code(404);
    echo "Page not found: $path";
}
?>