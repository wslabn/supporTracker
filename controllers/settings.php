<?php
require_once 'config.php';

// Check if user is admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: /SupporTracker/dashboard');
    exit;
}

$activeTab = $_GET['tab'] ?? 'company';
$success = '';
$error = '';

if ($_POST) {
    if (isset($_POST['save_company_settings'])) {
        // Create settings table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(100) UNIQUE,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Save settings - disable messaging if portal is disabled
        $portal_enabled = isset($_POST['portal_enabled']) ? '1' : '0';
        $messaging_enabled = ($portal_enabled && isset($_POST['customer_messaging_enabled'])) ? '1' : '0';
        
        $settings = [
            'company_name' => $_POST['company_name'],
            'company_logo_url' => $_POST['company_logo_url'],
            'default_hourly_rate' => $_POST['default_hourly_rate'],
            'default_tax_rate' => $_POST['default_tax_rate'],
            'invoice_due_days' => $_POST['invoice_due_days'],
            'portal_enabled' => $portal_enabled,
            'customer_messaging_enabled' => $messaging_enabled
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        }
        
        $success = "Company settings saved successfully!";
        $activeTab = 'company';
    }
    
    // Handle location operations
    if (isset($_POST['add_location'])) {
        $stmt = $pdo->prepare("INSERT INTO locations (name, address, phone, email, tax_rate) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['tax_rate']
        ]);
        $success = "Location added successfully!";
        $activeTab = 'locations';
    }
    
    if (isset($_POST['update_location'])) {
        $stmt = $pdo->prepare("UPDATE locations SET name = ?, address = ?, phone = ?, email = ?, tax_rate = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['tax_rate'],
            $_POST['location_id']
        ]);
        $success = "Location updated successfully!";
        $activeTab = 'locations';
    }
    
    if (isset($_POST['delete_location'])) {
        $stmt = $pdo->prepare("DELETE FROM locations WHERE id = ?");
        $stmt->execute([$_POST['location_id']]);
        $success = "Location deleted successfully!";
        $activeTab = 'locations';
    }
    
    // Handle user operations
    if (isset($_POST['add_user'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, permissions, location_access) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $password_hash,
            $_POST['role'],
            json_encode($_POST['permissions'] ?? []),
            json_encode($_POST['location_access'] ?? [])
        ]);
        $success = "User added successfully!";
        $activeTab = 'users';
    }
    
    // Handle asset category operations
    if (isset($_POST['add_asset_category'])) {
        $stmt = $pdo->prepare("INSERT INTO asset_categories (name, description) VALUES (?, ?)");
        $stmt->execute([$_POST['name'], $_POST['description']]);
        $success = "Asset category added successfully!";
        $activeTab = 'asset-categories';
    }
    
    // Handle service category operations
    if (isset($_POST['add_service_category'])) {
        $stmt = $pdo->prepare("INSERT INTO service_categories (name, description, sla_hours, priority) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['sla_hours'],
            $_POST['priority']
        ]);
        $success = "Service category added successfully!";
        $activeTab = 'service-categories';
    }
}

// Get current settings
$current_settings = [];
try {
    $settings = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    foreach ($settings as $setting) {
        $current_settings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Settings table doesn't exist yet
}

// Default values
$defaults = [
    'company_name' => 'Your MSP Company',
    'company_logo_url' => '',
    'default_hourly_rate' => '75.00',
    'default_tax_rate' => '0.00',
    'invoice_due_days' => '30',
    'portal_enabled' => '1',
    'customer_messaging_enabled' => '1'
];

foreach ($defaults as $key => $value) {
    if (!isset($current_settings[$key])) {
        $current_settings[$key] = $value;
    }
}

// Get locations
$locations = $pdo->query("SELECT * FROM locations ORDER BY name")->fetchAll();

// Get users
$users = $pdo->query("SELECT * FROM users ORDER BY name")->fetchAll();

// Get asset categories
$asset_categories = $pdo->query("SELECT * FROM asset_categories ORDER BY name")->fetchAll();

// Get service categories
$service_categories = $pdo->query("SELECT * FROM service_categories ORDER BY name")->fetchAll();

renderModernPage(
    'Settings - SupportTracker',
    'System Settings',
    'settings.php',
    compact('current_settings', 'success', 'error', 'activeTab', 'locations', 'users', 'asset_categories', 'service_categories'),
    ''
);
?>