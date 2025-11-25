<?php
$company_filter = $_GET['company_id'] ?? '';
$company_name = '';

// Handle form submissions
if ($_POST) {
    error_log('POST data: ' . print_r($_POST, true));
    if (isset($_POST['add_asset'])) {
        error_log('Processing add_asset');
        // Generate auto-incrementing asset tag if not provided
        $asset_tag = $_POST['asset_tag'];
        if (empty($asset_tag)) {
            $stmt = $pdo->query("SELECT COUNT(*) + 1 as next_num FROM assets");
            $result = $stmt->fetch();
            $next_num = $result['next_num'];
            $asset_tag = 'AST-' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
        }
        
        $stmt = $pdo->prepare("INSERT INTO assets (company_id, name, category_id, make, model, serial_number, asset_tag, location, employee_id, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['company_id'],
            $_POST['name'],
            $_POST['category_id'] ?? 1, // Default to 'Computers'
            $_POST['make'],
            $_POST['model'],
            $_POST['serial_number'],
            $asset_tag,
            $_POST['location'],
            empty($_POST['employee_id']) ? null : $_POST['employee_id'],
            $_POST['status'] ?? 'active',
            $_POST['notes']
        ]);
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'asset_tag' => $asset_tag]);
            exit;
        }
        
        header('Location: /SupporTracker/assets');
        exit;
    }
    
    if (isset($_POST['update_asset'])) {
        $stmt = $pdo->prepare("UPDATE assets SET company_id=?, name=?, category_id=?, make=?, model=?, serial_number=?, asset_tag=?, location=?, employee_id=?, status=?, notes=? WHERE id=?");
        $stmt->execute([
            $_POST['company_id'],
            $_POST['name'],
            $_POST['category_id'],
            $_POST['make'],
            $_POST['model'],
            $_POST['serial_number'],
            $_POST['asset_tag'],
            $_POST['location'],
            empty($_POST['employee_id']) ? null : $_POST['employee_id'],
            $_POST['status'],
            $_POST['notes'],
            $_POST['asset_id']
        ]);
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/assets');
        exit;
    }
}

// Handle JSON format requests for dropdowns
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    $company_id = $_GET['company_id'] ?? null;
    if ($company_id) {
        $stmt = $pdo->prepare("SELECT id, name, asset_tag FROM assets WHERE company_id = ? AND status = 'active' ORDER BY name");
        $stmt->execute([$company_id]);
        $assets = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode($assets);
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        $selected_company_id = $_GET['company_id'] ?? null;
        include 'views/modals/asset_modal.php';
        exit;
    }
    
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM assets WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $asset = $stmt->fetch();
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        include 'views/modals/asset_modal.php';
        exit;
    }
}

// Get companies for filter
$companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();

// Get company name if filtering
if ($company_filter) {
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$company_filter]);
    $company_name = $stmt->fetchColumn();
}

// Get assets with company and category info
$where_clause = $company_filter ? "WHERE a.company_id = " . intval($company_filter) : "";
$stmt = $pdo->query("
    SELECT a.*, c.name as company_name, ac.name as category_name
    FROM assets a 
    JOIN companies c ON a.company_id = c.id 
    LEFT JOIN asset_categories ac ON a.category_id = ac.id
    $where_clause
    ORDER BY c.name, a.name
");
$assets = $stmt->fetchAll();

if (function_exists('renderPage')) {
    renderPage(
        'Assets - SupportTracker',
        'assets.php',
        compact('assets', 'companies', 'company_filter', 'company_name')
    );
} else {
    error_log('renderPage function not found');
    include 'views/assets.php';
}
?>