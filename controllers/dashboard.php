<?php
// Get user role (default to admin for now)
$user_role = 'admin'; // TODO: Get from session/login

// Get business metrics for admin dashboard
if ($user_role === 'admin') {
    $stats = [
        'open_workorders' => $pdo->query("SELECT COUNT(*) FROM work_orders WHERE status IN ('open', 'in_progress')")->fetchColumn(),
        'completed_this_month' => $pdo->query("SELECT COUNT(*) FROM work_orders WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn(),
        'outstanding_invoices' => $pdo->query("SELECT COUNT(*) FROM invoices WHERE status != 'paid'")->fetchColumn(),
        'monthly_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE MONTH(invoice_date) = MONTH(CURRENT_DATE()) AND YEAR(invoice_date) = YEAR(CURRENT_DATE())")->fetchColumn()
    ];
    
    // Recent activity
    $recent_workorders = $pdo->query("
        SELECT wo.*, c.name as company_name 
        FROM work_orders wo 
        LEFT JOIN companies c ON wo.company_id = c.id 
        ORDER BY wo.created_at DESC LIMIT 5
    ")->fetchAll();
    
    $overdue_invoices = $pdo->query("
        SELECT i.*, c.name as company_name 
        FROM invoices i 
        JOIN companies c ON i.company_id = c.id 
        WHERE i.due_date < CURRENT_DATE() AND i.status != 'paid'
        ORDER BY i.due_date ASC LIMIT 5
    ")->fetchAll();
} else {
    // Basic stats for other roles
    $stats = [
        'companies' => $pdo->query("SELECT COUNT(*) FROM companies WHERE status = 'active'")->fetchColumn(),
        'assets' => $pdo->query("SELECT COUNT(*) FROM assets WHERE status = 'active'")->fetchColumn(),
        'employees' => $pdo->query("SELECT COUNT(*) FROM employees WHERE status = 'active'")->fetchColumn()
    ];
    $recent_workorders = [];
    $overdue_invoices = [];
}

renderPage('Dashboard - SupportTracker', 'dashboard.php', compact('stats', 'user_role', 'recent_workorders', 'overdue_invoices'));
?>