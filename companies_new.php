<?php
require_once 'config.php';
require_once 'includes/template.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_company'])) {
        $stmt = $pdo->prepare("INSERT INTO companies (name, email, phone, address, monthly_rate, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['monthly_rate'],
            $_POST['notes']
        ]);
        
        if (isset($_POST['ajax'])) {
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: companies_new.php');
        exit;
    }
}

// Get companies with counts
$stmt = $pdo->query("
    SELECT c.*, 
           COUNT(DISTINCT e.id) as employee_count,
           COUNT(DISTINCT a.id) as asset_count
    FROM companies c 
    LEFT JOIN employees e ON c.id = e.company_id 
    LEFT JOIN assets a ON c.id = a.company_id 
    WHERE c.status = 'active'
    GROUP BY c.id 
    ORDER BY c.name
");
$companies = $stmt->fetchAll();

renderPage(
    'Companies - SupportTracker',
    'companies.php',
    compact('companies')
);
?>