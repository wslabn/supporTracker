<?php
require_once 'config_v2.php';

// Get dashboard metrics
$stats = [
    'open_tickets' => $pdo->query("SELECT COUNT(*) FROM tickets WHERE status IN ('open', 'in_progress')")->fetchColumn(),
    'customers' => $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn(),
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn(),
    'monthly_revenue' => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM invoices WHERE MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn()
];

// Recent tickets
$recent_tickets = $pdo->query("
    SELECT t.*, c.name as customer_name, tech.name as technician_name
    FROM tickets t 
    LEFT JOIN customers c ON t.customer_id = c.id 
    LEFT JOIN technicians tech ON t.assigned_to = tech.id
    ORDER BY t.created_at DESC LIMIT 5
")->fetchAll();

// Overdue tickets
$overdue_tickets = $pdo->query("
    SELECT t.*, c.name as customer_name
    FROM tickets t 
    LEFT JOIN customers c ON t.customer_id = c.id 
    WHERE t.status NOT IN ('resolved', 'closed') 
    AND t.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
    ORDER BY t.created_at ASC LIMIT 5
")->fetchAll();

$headerActions = '
<button class="btn btn-primary" onclick="createTicket()">
    <i class="bi bi-plus-lg me-1"></i>
    New Ticket
</button>';

renderModernPage(
    'Dashboard - SupportTracker',
    'Dashboard',
    'dashboard_v2.php',
    compact('stats', 'recent_tickets', 'overdue_tickets'),
    $headerActions
);
?>