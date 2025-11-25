<?php
$employee_id = $_GET['id'] ?? 0;

// Get employee details with company info
$stmt = $pdo->prepare("
    SELECT e.*, c.name as company_name, c.phone as company_phone
    FROM employees e 
    LEFT JOIN companies c ON e.company_id = c.id 
    WHERE e.id = ?
");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: /SupporTracker/employees');
    exit;
}

// Get assets assigned to this employee
$stmt = $pdo->prepare("
    SELECT a.*, ac.name as category_name 
    FROM assets a 
    LEFT JOIN asset_categories ac ON a.category_id = ac.id 
    WHERE a.employee_id = ? 
    ORDER BY a.name
");
$stmt->execute([$employee_id]);
$assets = $stmt->fetchAll();

// Get credentials for employee's assets
$stmt = $pdo->prepare("
    SELECT ac.*, a.name as asset_name 
    FROM asset_credentials ac 
    JOIN assets a ON ac.asset_id = a.id 
    WHERE a.employee_id = ? 
    ORDER BY a.name, ac.created_at DESC
");
$stmt->execute([$employee_id]);
$credentials = $stmt->fetchAll();

function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'active': return 'success';
        case 'inactive': return 'secondary';
        case 'repair': return 'warning';
        case 'retired': return 'dark';
        default: return 'secondary';
    }
}

renderPage(
    'Employee: ' . htmlspecialchars($employee['name']),
    'employee_detail.php',
    compact('employee', 'employee_id', 'assets', 'credentials')
);
?>