<?php
// SupportTracker v2.0 Configuration

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'supporttracker');
define('DB_USER', 'supporttracker');
define('DB_PASS', '3Ga55ociates1nc!');

// Application Settings
define('APP_NAME', 'SupportTracker');
define('APP_VERSION', '2.0.0');

// Business Settings
define('DEFAULT_HOURLY_RATE', 75.00);
define('DEFAULT_TAX_RATE', 0.00);
define('INVOICE_DUE_DAYS', 30);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Include template system
require_once 'includes/template_v2.php';
?>