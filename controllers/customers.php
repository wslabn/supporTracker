<?php
require_once 'config.php';

if ($_POST) {
    if (isset($_POST['create_customer'])) {
        $stmt = $pdo->prepare("INSERT INTO customers (name, type, email, phone, address, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['type'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['notes']
        ]);
        header('Location: /SupporTracker/customers');
        exit;
    }
}

// Get customers
$customers = $pdo->query("
    SELECT c.*, 
           COUNT(DISTINCT t.id) as ticket_count,
           COUNT(DISTINCT a.id) as asset_count
    FROM customers c
    LEFT JOIN tickets t ON c.id = t.customer_id
    LEFT JOIN assets a ON c.id = a.customer_id

    GROUP BY c.id
    ORDER BY c.name
")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Customer
</button>';

renderModernPage(
    'Customers - SupportTracker',
    'Customers',
    'customers.php',
    compact('customers'),
    $headerActions
);
?>