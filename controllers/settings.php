<?php
require_once 'config_v2.php';

// Check if user is admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: /SupporTracker/dashboard');
    exit;
}

if ($_POST) {
    if (isset($_POST['save_settings'])) {
        // Create settings table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(100) UNIQUE,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Save settings
        $settings = [
            'company_name' => $_POST['company_name'],
            'company_logo_url' => $_POST['company_logo_url'],
            'default_hourly_rate' => $_POST['default_hourly_rate'],
            'default_tax_rate' => $_POST['default_tax_rate'],
            'invoice_due_days' => $_POST['invoice_due_days']
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        }
        
        $success = "Settings saved successfully!";
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
    'invoice_due_days' => '30'
];

foreach ($defaults as $key => $value) {
    if (!isset($current_settings[$key])) {
        $current_settings[$key] = $value;
    }
}

renderModernPage(
    'Settings - SupportTracker',
    'System Settings',
    'settings.php',
    compact('current_settings', 'success'),
    ''
);
?>