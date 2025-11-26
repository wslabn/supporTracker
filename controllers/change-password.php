<?php
session_start();

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supporttracker", 'supporttracker', '3Ga55ociates1nc!');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed");
}

// Must be logged in and required to change password
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['must_change_password'])) {
    header('Location: /SupporTracker/login');
    exit;
}

if ($_POST) {
    if (isset($_POST['change_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            if (strlen($_POST['new_password']) >= 6) {
                // Update password
                $stmt = $pdo->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
                $stmt->execute([$_POST['new_password'], $_SESSION['user_id']]);
                
                unset($_SESSION['must_change_password']);
                header('Location: /SupporTracker/dashboard');
                exit;
            } else {
                $error = "Password must be at least 6 characters";
            }
        } else {
            $error = "Passwords do not match";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - SupportTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        document.documentElement.setAttribute('data-bs-theme', mediaQuery.matches ? 'dark' : 'light');
    </script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mt-5">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Change Password Required</h4>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            You must change your password before continuing.
                        </div>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6">
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary w-100">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>