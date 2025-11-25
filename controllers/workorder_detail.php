<?php
$workorder_id = $_GET['id'] ?? 0;

// Get work order details with company and asset info
$stmt = $pdo->prepare("
    SELECT wo.*, c.name as company_name, a.name as asset_name, a.asset_tag,
           p.name as project_name
    FROM work_orders wo 
    LEFT JOIN companies c ON wo.company_id = c.id 
    LEFT JOIN assets a ON wo.asset_id = a.id
    LEFT JOIN projects p ON wo.project_id = p.id
    WHERE wo.id = ?
");
$stmt->execute([$workorder_id]);
$workorder = $stmt->fetch();

if (!$workorder) {
    header('Location: /SupporTracker/workorders');
    exit;
}

// Get parts orders for this work order
$stmt = $pdo->prepare("SELECT * FROM parts_orders WHERE work_order_id = ? ORDER BY created_at DESC");
$stmt->execute([$workorder_id]);
$parts = $stmt->fetchAll();

// Get tasks for this work order
$stmt = $pdo->prepare("SELECT * FROM work_order_tasks WHERE work_order_id = ? ORDER BY sort_order, id");
$stmt->execute([$workorder_id]);
$tasks = $stmt->fetchAll();

// Handle task updates
if ($_POST && isset($_POST['toggle_task'])) {
    $task_id = $_POST['task_id'];
    $completed = $_POST['completed'] === 'true' ? 1 : 0;
    $completed_at = $completed ? date('Y-m-d H:i:s') : null;
    
    $stmt = $pdo->prepare("UPDATE work_order_tasks SET completed = ?, completed_at = ? WHERE id = ?");
    $stmt->execute([$completed, $completed_at, $task_id]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Handle adding new task
if ($_POST && isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'];
    $notes = $_POST['notes'] ?? '';
    $customer_visible = $_POST['customer_visible'] === '1' ? 1 : 0;
    $stmt = $pdo->prepare("INSERT INTO work_order_tasks (work_order_id, task_name, notes, customer_visible) VALUES (?, ?, ?, ?)");
    $stmt->execute([$workorder_id, $task_name, $notes, $customer_visible]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Handle editing task
if ($_POST && isset($_POST['edit_task'])) {
    $task_id = $_POST['task_id'];
    $task_name = $_POST['task_name'];
    $notes = $_POST['notes'] ?? '';
    $customer_visible = $_POST['customer_visible'] === '1' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE work_order_tasks SET task_name = ?, notes = ?, customer_visible = ? WHERE id = ? AND work_order_id = ?");
    $stmt->execute([$task_name, $notes, $customer_visible, $task_id, $workorder_id]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Handle deleting task
if ($_POST && isset($_POST['delete_task'])) {
    $task_id = $_POST['task_id'];
    $stmt = $pdo->prepare("DELETE FROM work_order_tasks WHERE id = ? AND work_order_id = ?");
    $stmt->execute([$task_id, $workorder_id]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'new': return 'primary';
        case 'in_progress': return 'warning';
        case 'waiting_parts': return 'info';
        case 'waiting_customer': return 'secondary';
        case 'completed': return 'success';
        case 'closed': return 'dark';
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
    'Work Order #' . $workorder['id'] . ' - SupportTracker',
    'workorder_detail.php',
    compact('workorder', 'workorder_id', 'parts', 'tasks')
);
?>