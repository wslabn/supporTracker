<?php
session_start();

if (isset($_POST['login'])) {
    if ($_POST['password'] === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: /SupporTracker/dashboard');
        exit;
    } else {
        $error = "Invalid password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SupportTracker - Login</title>
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
    <h2>SupportTracker Admin</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
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