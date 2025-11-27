<?php
require_once 'config.php';

// Simple stats that won't crash
$stats = [
    'open_tickets' => 0,
    'customers' => 0,
    'projects' => 0,
    'monthly_revenue' => 0
];

try {
    $stats['open_tickets'] = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
} catch (Exception $e) {}

try {
    $stats['customers'] = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
} catch (Exception $e) {}

try {
    $stats['projects'] = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
} catch (Exception $e) {}

try {
    $stats['monthly_revenue'] = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM invoices")->fetchColumn();
} catch (Exception $e) {}

$recent_tickets = [];
$overdue_tickets = [];

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