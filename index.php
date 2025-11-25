<?php
require_once 'config.php';

// Simple admin login check - we'll build proper auth later
if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['login'])) {
        // Simple password check - replace with proper auth
        if ($_POST['password'] === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            redirect('/SupporTracker/dashboard');
        } else {
            $error = "Invalid password";
        }
    }
    
    // Show login form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo APP_NAME; ?> - Login</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input[type="password"] { width: 100%; padding: 8px; }
            button { background: #007cba; color: white; padding: 10px 20px; border: none; cursor: pointer; }
            .error { color: red; margin-bottom: 15px; }
        </style>
    </head>
    <body>
        <h2><?php echo APP_NAME; ?> Admin</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Admin Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </body>
    </html>
    <?php
    exit();
}

// Redirect to dashboard if already logged in
redirect('/SupporTracker/dashboard');
?>