<?php
require_once 'config.php';

$date = $_GET['date'] ?? date('Y-m-d');

// Simple test queries
try {
    $tickets = $pdo->query("SELECT * FROM tickets LIMIT 5")->fetchAll();
    $parts = $pdo->query("SELECT * FROM ticket_parts LIMIT 5")->fetchAll();
    $summary = [];
} catch (Exception $e) {
    error_log("Reports error: " . $e->getMessage());
    $tickets = [];
    $parts = [];
    $summary = [];
}

renderModernPage(
    'Reports - SupportTracker',
    'Daily Report - ' . date('M j, Y', strtotime($date)),
    'reports.php',
    compact('tickets', 'parts', 'summary', 'date'),
    ''
);
?>