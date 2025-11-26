<?php
require_once 'config.php';

if ($_POST) {
    if (isset($_POST['change_password'])) {
        if ($_POST['current_password'] === 'admin123') {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                // Create password table if it doesn't exist
                $pdo->exec("CREATE TABLE IF NOT EXISTS user_password (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    password VARCHAR(255),
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
                
                // Save new password (in production, this should be hashed)
                $stmt = $pdo->prepare("INSERT INTO user_password (id, password) VALUES (1, ?) ON DUPLICATE KEY UPDATE password = ?");
                $stmt->execute([$_POST['new_password'], $_POST['new_password']]);
                
                $password_success = "Password changed successfully!";
            } else {
                $password_error = "New passwords do not match";
            }
        } else {
            $password_error = "Current password is incorrect";
        }
    }
    
    if (isset($_POST['save_profile'])) {
        // Create user_profile table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS user_profile (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(20),
            title VARCHAR(100),
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Save or update profile
        $stmt = $pdo->prepare("INSERT INTO user_profile (id, name, email, phone, title) VALUES (1, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?, phone = ?, title = ?");
        $stmt->execute([
            $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['title'],
            $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['title']
        ]);
        
        $_SESSION['user_name'] = $_POST['name'];
        $success = "Profile updated successfully!";
    }
}

// Get current profile
$profile = [];
try {
    $profile = $pdo->query("SELECT * FROM user_profile WHERE id = 1")->fetch() ?: [];
} catch (Exception $e) {
    // Profile table doesn't exist yet
}

// Default values
$defaults = [
    'name' => $_SESSION['user_name'] ?? 'Admin User',
    'email' => 'admin@company.com',
    'phone' => '',
    'title' => 'System Administrator'
];

foreach ($defaults as $key => $value) {
    if (!isset($profile[$key]) || empty($profile[$key])) {
        $profile[$key] = $value;
    }
}

renderModernPage(
    'Profile - SupportTracker',
    'User Profile',
    'profile.php',
    compact('profile', 'success', 'password_success', 'password_error'),
    ''
);
?>