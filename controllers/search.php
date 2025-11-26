<?php
require_once 'config.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
if (strlen($query) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$results = [];
$searchTerm = '%' . $query . '%';

try {
    // Search customers
    $stmt = $pdo->prepare("SELECT id, name, type, phone, email, 'customer' as result_type FROM customers WHERE name LIKE ? OR phone LIKE ? OR email LIKE ? LIMIT 5");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $customers = $stmt->fetchAll();
    
    // Search tickets
    $stmt = $pdo->prepare("SELECT t.id, t.ticket_number, t.title, c.name as customer_name, 'ticket' as result_type FROM tickets t LEFT JOIN customers c ON t.customer_id = c.id WHERE t.ticket_number LIKE ? OR t.title LIKE ? LIMIT 5");
    $stmt->execute([$searchTerm, $searchTerm]);
    $tickets = $stmt->fetchAll();
    
    // Search assets
    $stmt = $pdo->prepare("SELECT a.id, a.name, a.model, c.name as customer_name, 'asset' as result_type FROM assets a LEFT JOIN customers c ON a.customer_id = c.id WHERE a.name LIKE ? OR a.model LIKE ? OR a.serial_number LIKE ? LIMIT 5");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $assets = $stmt->fetchAll();
    
    // Search invoices
    $stmt = $pdo->prepare("SELECT i.id, i.invoice_number, c.name as customer_name, 'invoice' as result_type FROM invoices i LEFT JOIN customers c ON i.customer_id = c.id WHERE i.invoice_number LIKE ? LIMIT 5");
    $stmt->execute([$searchTerm]);
    $invoices = $stmt->fetchAll();
    
    // Combine and format results
    foreach ($customers as $item) {
        $results[] = [
            'type' => 'customer',
            'id' => $item['id'],
            'title' => $item['name'],
            'subtitle' => $item['phone'] . ' • ' . $item['email'],
            'icon' => 'bi-person',
            'url' => '/SupporTracker/customers'
        ];
    }
    
    foreach ($tickets as $item) {
        $results[] = [
            'type' => 'ticket',
            'id' => $item['id'],
            'title' => $item['ticket_number'] . ' - ' . $item['title'],
            'subtitle' => $item['customer_name'],
            'icon' => 'bi-ticket-perforated',
            'url' => '/SupporTracker/tickets'
        ];
    }
    
    foreach ($assets as $item) {
        $results[] = [
            'type' => 'asset',
            'id' => $item['id'],
            'title' => $item['name'],
            'subtitle' => $item['model'] . ' • ' . $item['customer_name'],
            'icon' => 'bi-laptop',
            'url' => '/SupporTracker/assets'
        ];
    }
    
    foreach ($invoices as $item) {
        $results[] = [
            'type' => 'invoice',
            'id' => $item['id'],
            'title' => $item['invoice_number'],
            'subtitle' => $item['customer_name'],
            'icon' => 'bi-receipt',
            'url' => '/SupporTracker/invoices'
        ];
    }
    
    echo json_encode(['results' => $results]);
    
} catch (Exception $e) {
    echo json_encode(['results' => [], 'error' => $e->getMessage()]);
}
?>