<?php
require_once 'config.php';

if ($_POST) {
    if (isset($_POST['assign_ticket'])) {
        $stmt = $pdo->prepare("UPDATE tickets SET assigned_to = ? WHERE id = ?");
        $stmt->execute([$_POST['technician_id'], $_POST['ticket_id']]);
        header('Location: /SupporTracker/tickets');
        exit;
    }
    
    if (isset($_POST['update_status'])) {
        $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['ticket_id']]);
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
           a.name as asset_name, tech.name as technician_name,
           sc.name as service_category_name, sc.sla_hours
    FROM tickets t
    LEFT JOIN customers c ON t.customer_id = c.id
    LEFT JOIN assets a ON t.asset_id = a.id
    LEFT JOIN users tech ON t.assigned_to = tech.id
    LEFT JOIN service_categories sc ON t.service_category_id = sc.id
    $where
    ORDER BY t.created_at DESC
");
$tickets->execute($params);
$tickets = $tickets->fetchAll();

// Get data for filters and forms
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();
$technicians = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll();

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