<?php
// SupportTracker Configuration Example
// Copy this file to config.php and update with your actual values

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// Application Settings
define('APP_NAME', 'SupportTracker');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://your-domain.com');

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('ENCRYPTION_KEY', 'generate-random-32-character-key-here'); // Use: openssl_rand_pseudo_bytes(32)

// Email Settings (for invoice delivery)
define('SMTP_HOST', 'your-smtp-host');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@domain.com');
define('SMTP_PASS', 'your-smtp-password');
define('FROM_EMAIL', 'your-email@domain.com');
define('FROM_NAME', 'Your Company Name');

// Business Settings
define('DEFAULT_HOURLY_RATE', 75.00);
define('DEFAULT_TAX_RATE', 0.00); // 0% = no tax, 0.08 = 8%
define('INVOICE_DUE_DAYS', 30);

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', 'uploads/');

// Start session
session_start();

// Database Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper Functions
function redirect($url) {
    // Handle relative URLs
    if (strpos($url, '/') === 0) {
        header("Location: " . $url);
    } else {
        header("Location: " . $url);
    }
    exit();
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function encryptPassword($password) {
    return openssl_encrypt($password, 'AES-256-CBC', ENCRYPTION_KEY, 0, substr(md5(ENCRYPTION_KEY), 0, 16));
}

function decryptPassword($encrypted) {
    return openssl_decrypt($encrypted, 'AES-256-CBC', ENCRYPTION_KEY, 0, substr(md5(ENCRYPTION_KEY), 0, 16));
}

// Include template system
require_once 'includes/template.php';
?>