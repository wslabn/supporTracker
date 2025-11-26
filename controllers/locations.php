<?php
require_once 'config.php';

// Check if user is admin or manager for locations
if (!in_array($_SESSION['user_role'], ['admin', 'manager'])) {
    header('Location: /SupporTracker/dashboard');
    exit;
}

if ($_POST) {
    if (isset($_POST['update_location'])) {
        $stmt = $pdo->prepare("UPDATE locations SET name = ?, address = ?, phone = ?, tax_rate = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['address'],
            $_POST['phone'],
            $_POST['tax_rate'] ?: 0.0000,
            $_POST['location_id']
        ]);
        header('Location: /SupporTracker/locations');
        exit;
    }
    
    if (isset($_POST['toggle_location'])) {
        $stmt = $pdo->prepare("UPDATE locations SET is_active = NOT is_active WHERE id = ? AND is_default = 0");
        $stmt->execute([$_POST['location_id']]);
        header('Location: /SupporTracker/locations');
        exit;
    }
    
    if (isset($_POST['create_location'])) {
        $stmt = $pdo->prepare("INSERT INTO locations (name, address, phone, tax_rate) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['address'],
            $_POST['phone'],
            $_POST['tax_rate'] ?: 0.0000
        ]);
        header('Location: /SupporTracker/locations');
        exit;
    }
    
    if (isset($_POST['set_default'])) {
        $pdo->query("UPDATE locations SET is_default = 0");
        $pdo->prepare("UPDATE locations SET is_default = 1 WHERE id = ?")->execute([$_POST['location_id']]);
        header('Location: /SupporTracker/locations');
        exit;
    }
}

// Get locations with stats
$locations = $pdo->query("
    SELECT l.*, 
           COUNT(DISTINCT c.id) as customer_count
    FROM locations l
    LEFT JOIN customers c ON l.id = c.location_id
    GROUP BY l.id
    ORDER BY l.is_default DESC, l.name
")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#locationModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Location
</button>';

renderModernPage(
    'Locations - SupportTracker',
    'Locations',
    'locations.php',
    compact('locations', 'error'),
    $headerActions
);
?>