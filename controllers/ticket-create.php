<?php
require_once 'config.php';

$customerId = $_GET['customer_id'] ?? null;
$customer = null;
$customerAssets = [];

if ($customerId) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch();
    
    // Get customer's assets
    $stmt = $pdo->prepare("SELECT a.*, ac.name as category_name FROM assets a LEFT JOIN asset_categories ac ON a.category_id = ac.id WHERE a.customer_id = ?");
    $stmt->execute([$customerId]);
    $customerAssets = $stmt->fetchAll();
}

if ($_POST) {
    if (isset($_POST['create_ticket'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (customer_id, asset_id, service_category_id, title, description, priority, status, billable) VALUES (?, ?, ?, ?, ?, ?, 'open', ?)");
            $stmt->execute([
                $_POST['customer_id'],
                $_POST['asset_id'] ?: null,
                $_POST['service_category_id'],
                $_POST['title'],
                $_POST['description'],
                $_POST['priority'],
                isset($_POST['billable']) ? 1 : 0
            ]);
            
            $ticketId = $pdo->lastInsertId();
            
            if (isset($_POST['print_receipt'])) {
                header('Location: /SupporTracker/receipt?id=' . $ticketId);
            } else {
                header('Location: /SupporTracker/tickets');
            }
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get service categories
$serviceCategories = $pdo->query("SELECT * FROM service_categories WHERE type IN ('ticket', 'both') ORDER BY name")->fetchAll();

// Get all customers for dropdown
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();

renderModernPage(
    'Create Ticket - SupportTracker',
    'Create New Ticket',
    'ticket-create.php',
    compact('customer', 'customers', 'customerAssets', 'serviceCategories', 'error'),
    ''
);
?>