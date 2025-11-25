<?php
$company_filter = $_GET['company_id'] ?? null;

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_project'])) {
        $stmt = $pdo->prepare("INSERT INTO projects (company_id, name, description, status, priority, budget, start_date, end_date, estimated_hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['company_id'],
            $_POST['name'],
            $_POST['description'],
            $_POST['status'] ?? 'planning',
            $_POST['priority'] ?? 'medium',
            $_POST['budget'] ?? 0,
            $_POST['start_date'] ?: null,
            $_POST['end_date'] ?: null,
            $_POST['estimated_hours'] ?? null
        ]);
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/projects');
        exit;
    }
    
    if (isset($_POST['update_project'])) {
        // Get old status for history
        $stmt = $pdo->prepare("SELECT status FROM projects WHERE id = ?");
        $stmt->execute([$_POST['project_id']]);
        $old_status = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("UPDATE projects SET company_id=?, name=?, description=?, status=?, priority=?, budget=?, start_date=?, end_date=?, estimated_hours=?, actual_hours=? WHERE id=?");
        $stmt->execute([
            $_POST['company_id'],
            $_POST['name'],
            $_POST['description'],
            $_POST['status'],
            $_POST['priority'],
            $_POST['budget'],
            $_POST['start_date'] ?: null,
            $_POST['end_date'] ?: null,
            $_POST['estimated_hours'] ?? null,
            $_POST['actual_hours'] ?? null,
            $_POST['project_id']
        ]);
        
        // Log status change if different
        if ($old_status !== $_POST['status']) {
            $stmt = $pdo->prepare("INSERT INTO project_status_history (project_id, old_status, new_status, notes, changed_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['project_id'],
                $old_status,
                $_POST['status'],
                $_POST['status_notes'] ?? null,
                'admin' // TODO: Replace with actual user
            ]);
        }
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Location: /SupporTracker/projects');
        exit;
    }
}

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        $selected_company_id = $_GET['company_id'] ?? null;
        include 'views/modals/project_modal.php';
        exit;
    }
    
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $project = $stmt->fetch();
        
        $companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
        include 'views/modals/project_modal.php';
        exit;
    }
}

// Get companies for filter
$companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();

// Build WHERE clause for filters
$where_clause = $company_filter ? "WHERE p.company_id = " . intval($company_filter) : "";

// Get projects with company info and calculated totals
$stmt = $pdo->prepare("
    SELECT p.*, 
           c.name as company_name,
           COUNT(DISTINCT wo.id) as work_order_count,
           COUNT(DISTINCT po.id) as parts_order_count,
           COALESCE(SUM(wo.actual_hours), 0) as total_hours,
           COALESCE(SUM(po.total_price), 0) as total_parts_cost
    FROM projects p 
    JOIN companies c ON p.company_id = c.id 
    LEFT JOIN work_orders wo ON p.id = wo.project_id
    LEFT JOIN parts_orders po ON p.id = po.project_id AND po.billable = 1
    $where_clause
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute();
$projects = $stmt->fetchAll();

// Get filter name
$company_name = '';
if ($company_filter) {
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$company_filter]);
    $company_name = $stmt->fetchColumn();
}

function getProjectStatusColor($status) {
    switch (strtolower($status)) {
        case 'planning': return 'secondary';
        case 'active': return 'primary';
        case 'on_hold': return 'warning';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getProjectPriorityColor($priority) {
    switch (strtolower($priority)) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'danger';
        default: return 'secondary';
    }
}

renderPage(
    'Projects - SupportTracker',
    'projects.php',
    compact('projects', 'companies', 'company_filter', 'company_name')
);
?>