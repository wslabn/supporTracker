<?php
session_start();

// Database connection for login
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supporttracker", 'supporttracker', '3Ga55ociates1nc!');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database fails, continue with hardcoded password
}

if (isset($_POST['login'])) {
    // Create users table if it doesn't exist
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE,
            password VARCHAR(255),
            name VARCHAR(100),
            email VARCHAR(100),
            role ENUM('admin', 'technician', 'manager') DEFAULT 'technician',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Insert default admin user if not exists
        $pdo->exec("INSERT IGNORE INTO users (username, password, name, email, role, must_change_password) VALUES ('admin', 'admin123', 'Administrator', 'admin@company.com', 'admin', 1)");
        
        // Add must_change_password column if it doesn't exist
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN must_change_password BOOLEAN DEFAULT FALSE");
        } catch (Exception $e) {}
        
        // Update existing admin user's password from old system
        try {
            $old_password = $pdo->query("SELECT password FROM user_password WHERE id = 1")->fetchColumn();
            if ($old_password) {
                $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$old_password]);
            }
        } catch (Exception $e) {}
    } catch (Exception $e) {}
    
    // Check login credentials
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();
        
        if ($user && $_POST['password'] === $user['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_permissions'] = $user['permissions'];
            
            // Get user's accessible locations
            $userLocations = $pdo->prepare("SELECT l.id, l.name FROM locations l JOIN user_locations ul ON l.id = ul.location_id WHERE ul.user_id = ? AND l.is_active = 1 ORDER BY l.is_default DESC, l.name");
            $userLocations->execute([$user['id']]);
            $_SESSION['user_locations'] = $userLocations->fetchAll();
            
            // Set default location - use user's default or first accessible location
            if ($user['location_id']) {
                $defaultLocation = $pdo->prepare("SELECT name FROM locations WHERE id = ?");
                $defaultLocation->execute([$user['location_id']]);
                $locationName = $defaultLocation->fetchColumn();
                if ($locationName) {
                    $_SESSION['current_location_id'] = $user['location_id'];
                    $_SESSION['current_location_name'] = $locationName;
                }
            } elseif (!empty($_SESSION['user_locations'])) {
                // If no default location, use first accessible location
                $firstLocation = $_SESSION['user_locations'][0];
                $_SESSION['current_location_id'] = $firstLocation['id'];
                $_SESSION['current_location_name'] = $firstLocation['name'];
            }
            
            // Check if password must be changed
            if ($user['must_change_password'] || $user['password'] === 'admin123') {
                $_SESSION['must_change_password'] = true;
                header('Location: /SupporTracker/change-password');
            } else {
                header('Location: /SupporTracker/dashboard');
            }
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } catch (Exception $e) {
        $error = "Login system error";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SupportTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Auto-detect system theme
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
                        <h4 class="card-title text-center mb-4">SupportTracker Login</h4>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Default: admin / admin123</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>