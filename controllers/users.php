<?php
require_once 'config.php';

// Check if user can manage users
$userPermissions = isset($_SESSION['user_permissions']) ? json_decode($_SESSION['user_permissions'], true) : [];
if ($_SESSION['user_role'] !== 'admin' && !in_array('can_manage_users', $userPermissions)) {
    header('Location: /SupporTracker/dashboard');
    exit;
}

if ($_POST) {
    if (isset($_POST['create_user'])) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, name, email, role, must_change_password) VALUES (?, ?, ?, ?, ?, 1)");
        // Process permissions
        $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
        $permissionsJson = json_encode($permissions);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, name, email, role, permissions, location_id, must_change_password) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $_POST['username'],
            $_POST['password'],
            $_POST['name'],
            $_POST['email'],
            $_POST['role'],
            $permissionsJson,
            $_POST['location_id'] ?: null
        ]);
        
        // Save accessible locations
        $userId = $pdo->lastInsertId();
        if (isset($_POST['accessible_locations'])) {
            foreach ($_POST['accessible_locations'] as $locationId) {
                $stmt = $pdo->prepare("INSERT INTO user_locations (user_id, location_id) VALUES (?, ?)");
                $stmt->execute([$userId, $locationId]);
            }
        } else if ($_POST['location_id']) {
            // If no locations selected but has default, give access to default only
            $stmt = $pdo->prepare("INSERT INTO user_locations (user_id, location_id) VALUES (?, ?)");
            $stmt->execute([$userId, $_POST['location_id']]);
        }
        $success = "User created successfully!";
    }
    
    if (isset($_POST['toggle_user'])) {
        $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_POST['user_id']]);
        $success = "User status updated!";
    }
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

// Get locations for dropdown
$locations = $pdo->query("SELECT id, name FROM locations WHERE is_active = 1 ORDER BY is_default DESC, name")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
    <i class="bi bi-plus-lg me-1"></i>
    New User
</button>';

renderModernPage(
    'Users - SupportTracker',
    'User Management',
    'users.php',
    compact('users', 'locations', 'success'),
    $headerActions
);
?>