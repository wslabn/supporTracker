<?php
require_once 'config.php';

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = str_replace('/SupporTracker/portal', '', $path);
$path = trim($path, '/');

// Simple routing
switch ($path) {
    case '':
    case 'lookup':
        require 'controllers/lookup.php';
        break;
    case 'ticket':
        require 'controllers/ticket.php';
        break;
    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
?>