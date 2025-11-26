<?php
require_once 'config_v2.php';

if ($_POST) {
    if (isset($_POST['create_ticket'])) {
        $stmt = $pdo->prepare("INSERT INTO tickets (customer_id, asset_id, assigned_to, title, description, priority, billable, estimated_hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['asset_id'] ?: null,
            $_POST['assigned_to'] ?: null,
            $_POST['title'],
            $_POST['description'],
            $_POST['priority'],
            isset($_POST['billable']) ? 1 : 0,
            $_POST['estimated_hours'] ?: null
        ]);
        header('Location: /SupporTracker/tickets');
        exit;
    }
}

// Get tickets with filters
$where = "WHERE 1=1";
$params = [];

if (isset($_GET['status']) && $_GET['status']) {
    $where .= " AND t.status = ?";
    $params[] = $_GET['status'];
}

if (isset($_GET['priority']) && $_GET['priority']) {
    $where .= " AND t.priority = ?";
    $params[] = $_GET['priority'];
}

$tickets = $pdo->prepare("
    SELECT t.*, c.name as customer_name, 
           a.name as asset_name, tech.name as technician_name
    FROM tickets t
    LEFT JOIN customers c ON t.customer_id = c.id
    LEFT JOIN assets a ON t.asset_id = a.id
    LEFT JOIN technicians tech ON t.assigned_to = tech.id
    $where
    ORDER BY t.created_at DESC
");
$tickets->execute($params);
$tickets = $tickets->fetchAll();

// Get data for filters and forms
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();
$technicians = $pdo->query("SELECT id, name FROM technicians WHERE is_active = 1 ORDER BY name")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ticketModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Ticket
</button>';

renderModernPage(
    'Tickets - SupportTracker',
    'Support Tickets',
    'tickets.php',
    compact('tickets', 'customers', 'technicians'),
    $headerActions
);
?>