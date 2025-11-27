<?php
require_once 'config.php';

if ($_POST) {
    if (isset($_POST['create_customer'])) {
        try {
            // Create customer
            $stmt = $pdo->prepare("INSERT INTO customers (name, type, email, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $_POST['type'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['address']
            ]);
            
            $customerId = $pdo->lastInsertId();
            
            // Add primary contact if provided
            if (!empty($_POST['contact_name'])) {
                $stmt = $pdo->prepare("INSERT INTO customer_contacts (customer_id, name, email, phone, is_primary) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([
                    $customerId,
                    $_POST['contact_name'],
                    $_POST['contact_email'],
                    $_POST['contact_phone']
                ]);
            }
            
            header('Location: /SupporTracker/customers');
            exit;
        } catch (Exception $e) {
            // Log error and redirect back
            header('Location: /SupporTracker/customers?error=1');
            exit;
        }
    }
}

// Check if viewing individual customer
$customerId = $_GET['id'] ?? null;

if ($customerId) {
    // Get individual customer details
    $customer = $pdo->prepare("
        SELECT c.*, l.name as location_name
        FROM customers c
        LEFT JOIN locations l ON c.location_id = l.id
        WHERE c.id = ?
    ");
    $customer->execute([$customerId]);
    $customer = $customer->fetch();
    
    if (!$customer) {
        header('Location: /SupporTracker/customers');
        exit;
    }
    
    // Get customer tickets
    $tickets = $pdo->prepare("
        SELECT t.*, u.name as technician_name
        FROM tickets t
        LEFT JOIN users u ON t.assigned_to = u.id
        WHERE t.customer_id = ?
        ORDER BY t.created_at DESC
    ");
    $tickets->execute([$customerId]);
    $tickets = $tickets->fetchAll();
    
    // Get customer assets
    $assets = $pdo->prepare("
        SELECT * FROM assets WHERE customer_id = ? ORDER BY name
    ");
    $assets->execute([$customerId]);
    $assets = $assets->fetchAll();
    
    renderModernPage(
        $customer['name'] . ' - SupportTracker',
        $customer['name'],
        'customer_detail.php',
        compact('customer', 'tickets', 'assets'),
        '<a href="/SupporTracker/customers" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Customers</a>'
    );
} else {
    // Get customers with contact info
    $customers = $pdo->query("
        SELECT c.*, 
               l.name as location_name,
               COUNT(DISTINCT t.id) as ticket_count,
               COUNT(DISTINCT a.id) as asset_count,
               MAX(cc.name) as primary_contact,
               MAX(cc.email) as contact_email,
               MAX(cc.phone) as contact_phone
        FROM customers c
        LEFT JOIN locations l ON c.location_id = l.id
        LEFT JOIN tickets t ON c.id = t.customer_id
        LEFT JOIN assets a ON c.id = a.customer_id
        LEFT JOIN customer_contacts cc ON c.id = cc.customer_id AND cc.is_primary = 1
        GROUP BY c.id, l.name
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
}
?>