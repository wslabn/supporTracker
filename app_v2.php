<?php
require_once 'config_v2.php';

// Simple routing for v2
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/SupporTracker', '', $path);
$path = trim($path, '/');

// Default to dashboard
if (empty($path)) {
    $path = 'dashboard';
}

// Route to controllers
$controllerFile = "controllers_v2/{$path}.php";

if (file_exists($controllerFile)) {
    include $controllerFile;
} else {
    http_response_code(404);
    echo "Page not found: $path";
}
?>