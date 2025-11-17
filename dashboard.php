<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

// Get dashboard stats
$stats = [];

// Recent work orders
$stmt = $pdo->query("
    SELECT wo.id, wo.title, wo.status, wo.priority, c.name as company_name, wo.created_at
    FROM work_orders wo 
    JOIN companies c ON wo.company_id = c.id 
    ORDER BY wo.created_at DESC 
    LIMIT 5
");
$recent_orders = $stmt->fetchAll();

// Pending invoices
$stmt = $pdo->query("
    SELECT i.id, i.invoice_number, i.total_amount, i.balance, c.name as company_name, i.due_date
    FROM invoices i 
    JOIN companies c ON i.company_id = c.id 
    WHERE i.status IN ('sent', 'partial', 'overdue') 
    ORDER BY i.due_date ASC 
    LIMIT 5
");
$pending_invoices = $stmt->fetchAll();

// Quick stats
$stmt = $pdo->query("SELECT COUNT(*) as count FROM companies WHERE status = 'active'");
$stats['active_companies'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM work_orders WHERE status IN ('new', 'in_progress')");
$stats['open_orders'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT SUM(balance) as total FROM invoices WHERE status IN ('sent', 'partial', 'overdue')");
$stats['outstanding_balance'] = $stmt->fetch()['total'] ?: 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .header { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px; border-radius: 3px; }
        .nav a:hover { background: #005a87; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 5px; flex: 1; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #007cba; }
        .content { display: flex; gap: 20px; }
        .panel { background: white; padding: 20px; border-radius: 5px; flex: 1; }
        .panel h3 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .status-new { color: #dc3545; }
        .status-in_progress { color: #ffc107; }
        .status-completed { color: #28a745; }
        .priority-high { color: #dc3545; font-weight: bold; }
        .priority-urgent { color: #dc3545; font-weight: bold; background: #fff5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo APP_NAME; ?> Dashboard</h1>
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="companies.php">Companies</a>
            <a href="assets.php">Assets</a>
            <a href="work_orders.php">Work Orders</a>
            <a href="invoices.php">Invoices</a>
            <a href="payments.php">Payments</a>
            <a href="logout.php">Logout</a>
            <div style="float: right;">
                <form method="GET" action="search.php" style="display: inline;">
                    <input type="text" name="q" placeholder="Search..." style="padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <button type="submit" style="padding: 8px 12px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">🔍</button>
                </form>
            </div>
        </div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['active_companies']; ?></div>
            <div>Active Companies</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['open_orders']; ?></div>
            <div>Open Work Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo formatCurrency($stats['outstanding_balance']); ?></div>
            <div>Outstanding Balance</div>
        </div>
    </div>

    <div class="content">
        <div class="panel">
            <h3>Recent Work Orders</h3>
            <?php if (empty($recent_orders)): ?>
                <p>No work orders yet. <a href="work_orders.php">Create your first work order</a></p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                    </tr>
                    <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['title']); ?></td>
                        <td><?php echo htmlspecialchars($order['company_name']); ?></td>
                        <td class="status-<?php echo $order['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?></td>
                        <td class="priority-<?php echo $order['priority']; ?>"><?php echo ucfirst($order['priority']); ?></td>
                        <td><?php echo formatDate($order['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

        <div class="panel">
            <h3>Pending Invoices</h3>
            <?php if (empty($pending_invoices)): ?>
                <p>No pending invoices.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Invoice #</th>
                        <th>Company</th>
                        <th>Total</th>
                        <th>Balance</th>
                        <th>Due Date</th>
                    </tr>
                    <?php foreach ($pending_invoices as $invoice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['company_name']); ?></td>
                        <td><?php echo formatCurrency($invoice['total_amount']); ?></td>
                        <td><?php echo formatCurrency($invoice['balance']); ?></td>
                        <td><?php echo formatDate($invoice['due_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>