<?php
// Dashboard controller
$stats = [];

// Get basic stats
$stmt = $pdo->query("SELECT COUNT(*) as count FROM companies WHERE status = 'active'");
$stats['companies'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM assets");
$stats['assets'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM employees");
$stats['employees'] = $stmt->fetch()['count'];

renderPage(
    'Dashboard - SupportTracker',
    'dashboard.php',
    compact('stats')
);
?>