<?php
// Debug: Log all requests to parts controller
if ($_POST) {
    file_put_contents('/tmp/parts_debug.log', date('Y-m-d H:i:s') . " POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
}

$company_filter = $_GET['company_id'] ?? null;
$project_filter = $_GET['project_id'] ?? null;
$workorder_filter = $_GET['workorder_id'] ?? null;

// Handle form submissions
if ($_POST) {
    error_log('Parts controller POST data: ' . print_r($_POST, true));
    
    if (isset($_POST['add_part'])) {
        file_put_contents('/tmp/parts_debug.log', "REACHED ADD_PART SECTION\n", FILE_APPEND);
        error_log('Processing add_part request');
        // Calculate totals
        $quantity = floatval($_POST['quantity']);
        $unit_cost = floatval($_POST['unit_cost']);
        $markup_percent = floatval($_POST['markup_percent'] ?? 0);
        $unit_price = $unit_cost * (1 + ($markup_percent / 100));
        $total_cost = $quantity * $unit_cost;
        $total_price = $quantity * $unit_price;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO parts_orders (company_id, item_name, project_id, work_order_id, asset_id, description, part_number, quantity, unit_cost, markup_percent, unit_price, total_cost, total_price, vendor, vendor_url, order_date, expected_date, status, billable, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $params = [
                $_POST['company_id'],
                $_POST['item_name'],
                empty($_POST['project_id']) ? null : $_POST['project_id'],
                empty($_POST['work_order_id']) ? null : $_POST['work_order_id'],
                empty($_POST['asset_id']) ? null : $_POST['asset_id'],
                $_POST['description'] ?? '',
                $_POST['part_number'] ?? '',
                $quantity,
                $unit_cost,
                $markup_percent,
                $unit_price,
                $total_cost,
                $total_price,
                $_POST['vendor'] ?? '',
                $_POST['vendor_url'] ?? '',
                $_POST['order_date'] ?: null,
                $_POST['expected_date'] ?: null,
                $_POST['status'] ?? 'pending',
                isset($_POST['billable']) ? 1 : 0,
                $_POST['notes'] ?? ''
            ];
            
            error_log('Insert params: ' . print_r($params, true));
            $stmt->execute($params);
            error_log('Part inserted successfully with ID: ' . $pdo->lastInsertId());
        } catch (Exception $e) {
            error_log('Part insert error: ' . $e->getMessage());
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
        
        header('Location: /SupporTracker/parts');
        exit;
    }
    
    if (isset($_POST['update_part'])) {
        // Calculate totals
        $quantity = floatval($_POST['quantity']);
        $unit_cost = floatval($_POST['unit_cost']);
        $markup_percent = floatval($_POST['markup_percent'] ?? 0);
        $unit_price = $unit_cost * (1 + ($markup_percent / 100));
        $total_cost = $quantity * $unit_cost;
        $total_price = $quantity * $unit_price;
        
        $stmt = $pdo->prepare("UPDATE parts_orders SET company_id=?, project_id=?, work_order_id=?, asset_id=?, description=?, part_number=?, quantity=?, unit_cost=?, markup_percent=?, unit_price=?, total_cost=?, total_price=?, vendor=?, vendor_url=?, order_date=?, expected_date=?, received_date=?, status=?, billable=?, notes=? WHERE id=?");
        $stmt->execute([
            $_POST['company_id'],
            empty($_POST['project_id']) ? null : $_POST['project_id'],
            empty($_POST['work_order_id']) ? null : $_POST['work_order_id'],
            empty($_POST['asset_id']) ? null : $_POST['asset_id'],
            $_POST['description'],
            $_POST['part_number'],
            $quantity,
            $unit_cost,
            $markup_percent,
            $unit_price,
            $total_cost,
            $total_price,
            $_POST['vendor'],
            $_POST['vendor_url'],
            $_POST['order_date'] ?: null,
            $_POST['expected_date'] ?: null,
            $_POST['received_date'] ?: null,
            $_POST['status'],
            isset($_POST['billable']) ? 1 : 0,
            $_POST['notes'],
            $_POST['part_id']
        ]);
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/parts');
        exit;
    }
}

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        $selected_company_id = $_GET['company_id'] ?? null;
        $selected_project_id = $_GET['project_id'] ?? null;
        $selected_workorder_id = $_GET['workorder_id'] ?? null;
        
        // Get projects and work orders for selected company
        $projects = [];
        $workorders = [];
        $assets = [];
        
        if ($selected_company_id) {
            $stmt = $pdo->prepare("SELECT id, name FROM projects WHERE company_id = ? ORDER BY name");
            $stmt->execute([$selected_company_id]);
            $projects = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("SELECT id, title FROM work_orders WHERE company_id = ? ORDER BY title");
            $stmt->execute([$selected_company_id]);
            $workorders = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("SELECT id, name, asset_tag FROM assets WHERE company_id = ? ORDER BY name");
            $stmt->execute([$selected_company_id]);
            $assets = $stmt->fetchAll();
        }
        
        include 'views/modals/part_modal.php';
        exit;
    }
    
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM parts_orders WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $part = $stmt->fetch();
        
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        
        // Get projects, work orders, and assets for part's company
        $projects = [];
        $workorders = [];
        $assets = [];
        
        if ($part['company_id']) {
            $stmt = $pdo->prepare("SELECT id, name FROM projects WHERE company_id = ? ORDER BY name");
            $stmt->execute([$part['company_id']]);
            $projects = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("SELECT id, title FROM work_orders WHERE company_id = ? ORDER BY title");
            $stmt->execute([$part['company_id']]);
            $workorders = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("SELECT id, name, asset_tag FROM assets WHERE company_id = ? ORDER BY name");
            $stmt->execute([$part['company_id']]);
            $assets = $stmt->fetchAll();
        }
        
        include 'views/modals/part_modal.php';
        exit;
    }
}

// Get companies for filter
$companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();

// Build WHERE clause for filters
$where_conditions = [];
$params = [];

if ($company_filter) {
    $where_conditions[] = "po.company_id = ?";
    $params[] = $company_filter;
}

if ($project_filter) {
    $where_conditions[] = "po.project_id = ?";
    $params[] = $project_filter;
}

if ($workorder_filter) {
    $where_conditions[] = "po.work_order_id = ?";
    $params[] = $workorder_filter;
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get parts orders with related info
$stmt = $pdo->prepare("
    SELECT po.*, 
           c.name as company_name,
           p.name as project_name,
           wo.title as workorder_title,
           a.name as asset_name,
           a.asset_tag
    FROM parts_orders po 
    JOIN companies c ON po.company_id = c.id 
    LEFT JOIN projects p ON po.project_id = p.id
    LEFT JOIN work_orders wo ON po.work_order_id = wo.id
    LEFT JOIN assets a ON po.asset_id = a.id
    $where_clause
    ORDER BY po.created_at DESC
");
$stmt->execute($params);
$parts = $stmt->fetchAll();

function getPartStatusColor($status) {
    switch (strtolower($status)) {
        case 'pending': return 'secondary';
        case 'ordered': return 'primary';
        case 'shipped': return 'info';
        case 'received': return 'warning';
        case 'installed': return 'success';
        default: return 'secondary';
    }
}

renderPage(
    'Parts & Equipment - SupportTracker',
    'parts.php',
    compact('parts', 'companies', 'company_filter', 'project_filter', 'workorder_filter')
);
?>