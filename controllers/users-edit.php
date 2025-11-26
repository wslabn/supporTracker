<?php
require_once 'config_v2.php';

// Check if user is admin or manager
if (!in_array($_SESSION['user_role'], ['admin', 'manager'])) {
    header('Location: /SupporTracker/dashboard');
    exit;
}

// Get user ID from URL
$userId = $_GET['id'] ?? null;
if (!$userId) {
    header('Location: /SupporTracker/users');
    exit;
}

// Get user data
$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$userId]);
$user = $user->fetch();

if (!$user) {
    header('Location: /SupporTracker/users');
    exit;
}

if ($_POST) {
    if (isset($_POST['update_user'])) {
        // Process permissions
        $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
        $permissionsJson = json_encode($permissions);
        
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, permissions = ?, location_id = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['role'],
            $permissionsJson,
            $_POST['location_id'] ?: null,
            $userId
        ]);
        
        // Update accessible locations
        $pdo->prepare("DELETE FROM user_locations WHERE user_id = ?")->execute([$userId]);
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
        
        header('Location: /SupporTracker/users');
        exit;
    }
}

// Parse current permissions
$currentPermissions = $user['permissions'] ? json_decode($user['permissions'], true) : [];

// Get locations for dropdown
$locations = $pdo->query("SELECT id, name FROM locations WHERE is_active = 1 ORDER BY is_default DESC, name")->fetchAll();

// Get user's accessible locations
$userAccessibleLocations = $pdo->prepare("SELECT location_id FROM user_locations WHERE user_id = ?");
$userAccessibleLocations->execute([$userId]);
$userAccessibleLocations = $userAccessibleLocations->fetchAll(PDO::FETCH_COLUMN);

renderModernPage(
    'Edit User - SupportTracker',
    'Edit User: ' . $user['name'],
    'users-edit.php',
    compact('user', 'currentPermissions', 'locations', 'userAccessibleLocations'),
    ''
);
?>