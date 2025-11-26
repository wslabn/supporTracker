<?php
require_once 'config.php';

// Get dashboard metrics
$stats = [
    'open_tickets' => $pdo->query("SELECT COUNT(*) FROM tickets WHERE status IN ('open', 'in_progress')")->fetchColumn(),
    'customers' => $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn(),
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn(),
    'monthly_revenue' => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM invoices WHERE MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn()
];

// Recent tickets with unread message counts
$recent_tickets = $pdo->query("
    SELECT t.*, c.name as customer_name, u.name as technician_name,
           (SELECT COUNT(*) FROM ticket_messages tm WHERE tm.ticket_id = t.id AND tm.sender_type = 'customer' AND tm.is_read = FALSE) as unread_messages
    FROM tickets t 
    LEFT JOIN customers c ON t.customer_id = c.id 
    LEFT JOIN users u ON t.assigned_to = u.id
    ORDER BY t.created_at DESC LIMIT 5
")->fetchAll();

// Overdue tickets with unread message counts
$overdue_tickets = $pdo->query("
    SELECT t.*, c.name as customer_name,
           (SELECT COUNT(*) FROM ticket_messages tm WHERE tm.ticket_id = t.id AND tm.sender_type = 'customer' AND tm.is_read = FALSE) as unread_messages
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
    'dashboard.php',
    compact('stats', 'recent_tickets', 'overdue_tickets'),
    $headerActions
);
?>