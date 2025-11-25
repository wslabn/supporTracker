<?php
$company_id = $_GET['company_id'] ?? $_POST['company_id'] ?? null;

// If no company_id and not a POST request, show all employees
if (!$company_id && !$_POST) {
    // Get all employees with company info
    $stmt = $pdo->query("
        SELECT e.*, c.name as company_name, COUNT(a.id) as asset_count 
        FROM employees e 
        JOIN companies c ON e.company_id = c.id 
        LEFT JOIN assets a ON e.id = a.employee_id 
        GROUP BY e.id 
        ORDER BY c.name, e.name
    ");
    $employees = $stmt->fetchAll();
    
    renderPage(
        'All Employees - SupportTracker',
        'all_employees.php',
        compact('employees')
    );
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

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_employee'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO employees (company_id, name, email, phone, cell_phone, department, title, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $company_id,
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['cell_phone'] ?? '',
                $_POST['department'],
                $_POST['position'],
                $_POST['status'] ?? 'active'
            ]);
        } catch (Exception $e) {
            error_log('Employee insert error: ' . $e->getMessage());
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }
        
        if (isset($_POST['ajax'])) {
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/employees?company_id=' . $company_id);
        exit;
    }
    
    if (isset($_POST['update_employee'])) {
        $stmt = $pdo->prepare("UPDATE employees SET name=?, email=?, phone=?, cell_phone=?, department=?, title=?, status=? WHERE id=? AND company_id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['cell_phone'] ?? '',
            $_POST['department'],
            $_POST['position'],
            $_POST['status'],
            $_POST['employee_id'],
            $company_id
        ]);
        
        if (isset($_POST['ajax'])) {
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/employees?company_id=' . $company_id);
        exit;
    }
}

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        include 'views/modals/employee_modal.php';
        exit;
    }
    
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ? AND company_id = ?");
        $stmt->execute([$_GET['id'], $company_id]);
        $employee = $stmt->fetch();
        include 'views/modals/employee_modal.php';
        exit;
    }
}

// Get employees for this company with asset counts
$stmt = $pdo->prepare("
    SELECT e.*, COUNT(a.id) as asset_count 
    FROM employees e 
    LEFT JOIN assets a ON e.id = a.employee_id 
    WHERE e.company_id = ? 
    GROUP BY e.id 
    ORDER BY e.name
");
$stmt->execute([$company_id]);
$employees = $stmt->fetchAll();

renderPage(
    'Employees - ' . htmlspecialchars($company['name']),
    'employees.php',
    compact('employees', 'company', 'company_id')
);
?>