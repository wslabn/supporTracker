<?php
$company_id = $_GET['id'] ?? null;
if (!$company_id) {
    header('Location: /SupporTracker/companies');
    exit;
}

// Get company info
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch();

if (!$company) {
    header('Location: /SupporTracker/companies');
    exit;
}

// Get company stats
$stats = [];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM employees WHERE company_id = ?");
$stmt->execute([$company_id]);
$stats['employees'] = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assets WHERE company_id = ?");
$stmt->execute([$company_id]);
$stats['assets'] = $stmt->fetch()['count'];

renderPage(
    'Company: ' . htmlspecialchars($company['name']),
    'company_detail.php',
    compact('company', 'company_id', 'stats')
);
?>