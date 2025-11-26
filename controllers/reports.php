<?php
require_once 'config_v2.php';

// Get report data
$stats = [
    'total_customers' => $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn(),
    'total_tickets' => $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn(),
    'open_tickets' => $pdo->query("SELECT COUNT(*) FROM tickets WHERE status IN ('open', 'in_progress')")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM invoices WHERE status = 'paid'")->fetchColumn()
];

renderModernPage(
    'Reports - SupportTracker',
    'Reports',
    'reports.php',
    compact('stats'),
    ''
);
?>