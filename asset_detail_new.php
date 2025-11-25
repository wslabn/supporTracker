<?php
require_once 'config.php';
require_once 'includes/template.php';

$asset_id = $_GET['id'] ?? 0;

// Get asset details with company info
$stmt = $pdo->prepare("
    SELECT a.*, c.name as company_name, e.name as employee_name 
    FROM assets a 
    LEFT JOIN companies c ON a.company_id = c.id 
    LEFT JOIN employees e ON a.employee_id = e.id 
    WHERE a.id = ?
");
$stmt->execute([$asset_id]);
$asset = $stmt->fetch();

if (!$asset) {
    header('Location: assets.php');
    exit;
}

// Get credentials for this asset
$stmt = $pdo->prepare("SELECT * FROM credentials WHERE asset_id = ? ORDER BY created_at DESC");
$stmt->execute([$asset_id]);
$credentials = $stmt->fetchAll();

// Get work orders for this asset
$stmt = $pdo->prepare("
    SELECT wo.*, c.name as company_name 
    FROM work_orders wo 
    LEFT JOIN companies c ON wo.company_id = c.id 
    WHERE wo.asset_id = ? 
    ORDER BY wo.created_at DESC
");
$stmt->execute([$asset_id]);
$work_orders = $stmt->fetchAll();

function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'open': return 'primary';
        case 'in progress': return 'warning';
        case 'completed': return 'success';
        case 'closed': return 'secondary';
        default: return 'secondary';
    }
}

function getPriorityColor($priority) {
    switch (strtolower($priority)) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'danger';
        default: return 'secondary';
    }
}

renderPage(
    'Asset: ' . htmlspecialchars($asset['name']),
    'asset_detail.php',
    compact('asset', 'asset_id', 'credentials', 'work_orders')
);
?>