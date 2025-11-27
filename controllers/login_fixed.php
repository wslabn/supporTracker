<?php
require_once 'config.php';

if (isset($_POST['login'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();
        
        if ($user && $_POST['password'] === $user['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['current_location_id'] = 1;
            $_SESSION['current_location_name'] = 'Main Office';
            
            header('Location: /SupporTracker/dashboard');
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } catch (Exception $e) {
        $error = "Login system error: " . $e->getMessage();
    }
}

renderModernPage(
    'Login - SupportTracker',
    'Login',
    'login.php',
    compact('error'),
    ''
);
?>