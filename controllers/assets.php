<?php
require_once 'config_v2.php';

if ($_POST) {
    if (isset($_POST['create_asset'])) {
        $stmt = $pdo->prepare("INSERT INTO assets (customer_id, name, type, serial_number, model, location, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['name'],
            $_POST['type'],
            $_POST['serial_number'],
            $_POST['model'],
            $_POST['location'],
            $_POST['notes']
        ]);
        header('Location: /SupporTracker/assets');
        exit;
    }
}

// Get assets
$assets = $pdo->query("
    SELECT a.*, c.name as customer_name,
           COUNT(t.id) as ticket_count
    FROM assets a
    LEFT JOIN customers c ON a.customer_id = c.id
    LEFT JOIN tickets t ON a.id = t.asset_id
    GROUP BY a.id
    ORDER BY c.name, a.name
")->fetchAll();

$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assetModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Asset
</button>';

renderModernPage(
    'Assets - SupportTracker',
    'Assets',
    'assets.php',
    compact('assets', 'customers'),
    $headerActions
);
?>