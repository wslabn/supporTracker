<?php
require_once 'config.php';

$customerId = $_GET['customer_id'] ?? null;
$customer = null;

if ($customerId) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch();
}

if ($_POST) {
    if (isset($_POST['create_asset'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO assets (customer_id, category_id, name, model, serial_number, vendor, version, location, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['customer_id'],
                $_POST['category_id'],
                $_POST['name'],
                $_POST['model'],
                $_POST['serial_number'],
                $_POST['vendor'],
                $_POST['version'],
                $_POST['location'],
                $_POST['status']
            ]);
            
            header('Location: /SupporTracker/assets');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get asset categories
$categories = $pdo->query("SELECT * FROM asset_categories WHERE status = 'active' ORDER BY name")->fetchAll();

renderModernPage(
    'Add Asset - SupportTracker',
    'Add New Asset',
    'asset-create.php',
    compact('customer', 'categories', 'error'),
    ''
);
?>