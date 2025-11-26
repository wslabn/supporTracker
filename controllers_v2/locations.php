<?php
require_once 'config_v2.php';

if ($_POST) {
    if (isset($_POST['create_location'])) {
        $stmt = $pdo->prepare("INSERT INTO locations (name, address, phone, email, tax_rate, tax_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['tax_rate'] ?: null,
            $_POST['tax_id']
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
           COUNT(DISTINCT c.id) as customer_count,
           COUNT(DISTINCT t.id) as technician_count,
           COUNT(DISTINCT tickets.id) as ticket_count
    FROM locations l
    LEFT JOIN customers c ON l.id = c.location_id
    LEFT JOIN technicians t ON l.id = t.location_id
    LEFT JOIN tickets ON l.id = tickets.location_id
    WHERE l.status = 'active'
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
    compact('locations'),
    $headerActions
);
?>