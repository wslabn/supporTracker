<?php
require_once 'config.php';
require_once 'includes/template.php';
require_once 'includes/router.php';

if (!isset($_SESSION['admin_logged_in'])) {
    include_once 'index.php';
    exit;
}

// Make PDO available globally for controllers
global $pdo;

// Start output buffering to prevent duplicate output
ob_start();

$router->dispatch();

// End output buffering and send
ob_end_flush();
?>