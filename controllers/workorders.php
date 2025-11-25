<?php
$company_filter = $_GET['company_id'] ?? null;
$asset_filter = $_GET['asset_id'] ?? null;

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_workorder'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO work_orders (company_id, asset_id, title, description, priority, billable, estimated_hours, hourly_rate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['company_id'],
                empty($_POST['asset_id']) ? null : $_POST['asset_id'],
                $_POST['title'],
                $_POST['description'],
                $_POST['priority'] ?? 'medium',
                isset($_POST['billable']) ? 1 : 0,
                empty($_POST['estimated_hours']) ? null : $_POST['estimated_hours'],
                empty($_POST['hourly_rate']) ? DEFAULT_HOURLY_RATE : $_POST['hourly_rate']
            ]);
        } catch (Exception $e) {
            error_log('Work order insert error: ' . $e->getMessage());
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/workorders');
        exit;
    }
    
    if (isset($_POST['update_workorder'])) {
        $stmt = $pdo->prepare("UPDATE work_orders SET company_id=?, asset_id=?, title=?, description=?, status=?, priority=?, billable=?, estimated_hours=?, actual_hours=?, hourly_rate=? WHERE id=?");
        $stmt->execute([
            $_POST['company_id'],
            empty($_POST['asset_id']) ? null : $_POST['asset_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['status'],
            $_POST['priority'],
            isset($_POST['billable']) ? 1 : 0,
            empty($_POST['estimated_hours']) ? null : $_POST['estimated_hours'],
            empty($_POST['actual_hours']) ? null : $_POST['actual_hours'],
            empty($_POST['hourly_rate']) ? DEFAULT_HOURLY_RATE : $_POST['hourly_rate'],
            $_POST['workorder_id']
        ]);
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/workorders');
        exit;
    }
}

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        $selected_company_id = $_GET['company_id'] ?? null;
        $selected_asset_id = $_GET['asset_id'] ?? null;
        
        // Get assets for selected company
        $assets = [];
        if ($selected_company_id) {
            $stmt = $pdo->prepare("SELECT id, name, asset_tag FROM assets WHERE company_id = ? AND status = 'active' ORDER BY name");
            $stmt->execute([$selected_company_id]);
            $assets = $stmt->fetchAll();
        }
        
        include 'views/modals/workorder_modal.php';
        exit;
    }
    
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM work_orders WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $workorder = $stmt->fetch();
        
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        
        // Get assets for workorder's company
        $assets = [];
        if ($workorder['company_id']) {
            $stmt = $pdo->prepare("SELECT id, name, asset_tag FROM assets WHERE company_id = ? ORDER BY name");
            $stmt->execute([$workorder['company_id']]);
            $assets = $stmt->fetchAll();
        }
        
        include 'views/modals/workorder_modal.php';
        exit;
    }
}

// Get companies for filter
$companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();

// Build WHERE clause for filters
$where_conditions = [];
$params = [];

if ($company_filter) {
    $where_conditions[] = "wo.company_id = ?";
    $params[] = $company_filter;
}

if ($asset_filter) {
    $where_conditions[] = "wo.asset_id = ?";
    $params[] = $asset_filter;
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get work orders with company and asset info
$stmt = $pdo->prepare("
    SELECT wo.*, c.name as company_name, a.name as asset_name, a.asset_tag
    FROM work_orders wo 
    JOIN companies c ON wo.company_id = c.id 
    LEFT JOIN assets a ON wo.asset_id = a.id
    $where_clause
    ORDER BY wo.created_at DESC
");
$stmt->execute($params);
$workorders = $stmt->fetchAll();

// Get filter names
$company_name = '';
$asset_name = '';

if ($company_filter) {
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$company_filter]);
    $company_name = $stmt->fetchColumn();
}

if ($asset_filter) {
    $stmt = $pdo->prepare("SELECT name FROM assets WHERE id = ?");
    $stmt->execute([$asset_filter]);
    $asset_name = $stmt->fetchColumn();
}

renderPage(
    'Work Orders - SupportTracker',
    'workorders.php',
    compact('workorders', 'companies', 'company_filter', 'asset_filter', 'company_name', 'asset_name')
);
?>