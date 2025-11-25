<?php
// Handle form submissions
if ($_POST) {
    // Process form before any output
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
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/companies');
        exit;
    }
    
    if (isset($_POST['update_company'])) {
        $stmt = $pdo->prepare("UPDATE companies SET name=?, email=?, phone=?, address=?, monthly_rate=?, notes=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['monthly_rate'],
            $_POST['notes'],
            $_POST['company_id']
        ]);
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/companies');
        exit;
    }
}

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        include 'views/modals/company_modal.php';
        exit;
    }
    
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $company = $stmt->fetch();
        include 'views/modals/company_modal.php';
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