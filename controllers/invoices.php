<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<!-- Invoices controller loaded -->\n";

// Handle form submissions
echo "<!-- POST data: " . print_r($_POST, true) . " -->\n";
if ($_POST) {
    echo "<!-- Processing POST request -->\n";
    if (isset($_POST['generate_invoice'])) {
        try {
            echo "<!-- Starting invoice generation -->\n";
            $company_id = $_POST['company_id'];
            $work_order_ids = $_POST['work_order_ids'] ?? [];
            echo "<!-- Company ID: $company_id, Work Orders: " . print_r($work_order_ids, true) . " -->\n";
        
        if (empty($work_order_ids)) {
            $error = "Please select at least one work order to invoice.";
        } else {
            // Generate invoice number
            $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED)) as max_num FROM invoices WHERE invoice_number LIKE 'INV-%'");
            $result = $stmt->fetch();
            $next_num = ($result['max_num'] ?? 0) + 1;
            $invoice_number = 'INV-' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
            
            // Create invoice
            $invoice_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+30 days'));
            
            $stmt = $pdo->prepare("INSERT INTO invoices (company_id, invoice_number, invoice_date, due_date, status) VALUES (?, ?, ?, ?, 'draft')");
            $stmt->execute([$company_id, $invoice_number, $invoice_date, $due_date]);
            $invoice_id = $pdo->lastInsertId();
            
            $subtotal = 0;
            
            // Add work order items
            foreach ($work_order_ids as $wo_id) {
                $stmt = $pdo->prepare("SELECT * FROM work_orders WHERE id = ?");
                $stmt->execute([$wo_id]);
                $wo = $stmt->fetch();
                
                if ($wo && $wo['billable']) {
                    // Add labor
                    $labor_total = $wo['actual_hours'] * $wo['hourly_rate'];
                    if ($labor_total > 0) {
                        $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, work_order_id, description, quantity, unit_price, total_price, item_type) VALUES (?, ?, ?, ?, ?, ?, 'labor')");
                        $stmt->execute([$invoice_id, $wo_id, "Labor: " . $wo['title'], $wo['actual_hours'], $wo['hourly_rate'], $labor_total]);
                        $subtotal += $labor_total;
                    }
                    
                    // Add billable parts
                    $stmt = $pdo->prepare("SELECT * FROM parts_orders WHERE work_order_id = ? AND billable = 1");
                    $stmt->execute([$wo_id]);
                    $parts = $stmt->fetchAll();
                    
                    foreach ($parts as $part) {
                        $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, work_order_id, description, quantity, unit_price, total_price, item_type) VALUES (?, ?, ?, ?, ?, ?, 'parts')");
                        $stmt->execute([$invoice_id, $wo_id, "Parts: " . $part['item_name'], $part['quantity'], $part['unit_price'], $part['total_price']]);
                        $subtotal += $part['total_price'];
                    }
                    
                    // Mark work order as invoiced
                    $stmt = $pdo->prepare("UPDATE work_orders SET invoiced = 1, invoice_id = ? WHERE id = ?");
                    $stmt->execute([$invoice_id, $wo_id]);
                }
            }
            
            // Update invoice totals
            $tax_amount = $subtotal * 0.00; // No tax by default
            $total = $subtotal + $tax_amount;
            
            $stmt = $pdo->prepare("UPDATE invoices SET subtotal = ?, tax_amount = ?, total_amount = ?, balance = ? WHERE id = ?");
            $stmt->execute([$subtotal, $tax_amount, $total, $total, $invoice_id]);
            
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'invoice_id' => $invoice_id]);
                exit;
            }
            
            header("Location: /SupporTracker/invoice?id=$invoice_id");
            exit;
        }
        } catch (Exception $e) {
            echo "<!-- Invoice generation error: " . $e->getMessage() . " -->\n";
            echo "<!-- Stack trace: " . $e->getTraceAsString() . " -->\n";
            $error = "Error generating invoice: " . $e->getMessage();
        }
    }
}

// Get companies for filter
echo "<!-- Getting companies -->\n";
$companies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name")->fetchAll();
echo "<!-- Found " . count($companies) . " companies -->\n";

// Get company filter
$company_filter = $_GET['company_id'] ?? null;
$error = null; // Initialize error variable
echo "<!-- Company filter: " . ($company_filter ?? 'none') . " -->\n";

// Get unbilled work orders
$where_clause = "WHERE wo.billable = 1 AND wo.invoiced = 0 AND wo.status IN ('completed', 'closed')";
$params = [];

if ($company_filter) {
    $where_clause .= " AND wo.company_id = ?";
    $params[] = $company_filter;
}

$stmt = $pdo->prepare("
    SELECT wo.*, c.name as company_name,
           (wo.actual_hours * wo.hourly_rate) as labor_total,
           COALESCE(parts_total.total, 0) as parts_total
    FROM work_orders wo
    JOIN companies c ON wo.company_id = c.id
    LEFT JOIN (
        SELECT work_order_id, SUM(total_price) as total 
        FROM parts_orders 
        WHERE billable = 1 
        GROUP BY work_order_id
    ) parts_total ON wo.id = parts_total.work_order_id
    $where_clause
    ORDER BY wo.created_at DESC
");
echo "<!-- Executing query with params: " . print_r($params, true) . " -->\n";
$stmt->execute($params);
$unbilled_work_orders = $stmt->fetchAll();
echo "<!-- Found " . count($unbilled_work_orders) . " unbilled work orders -->\n";
echo "<!-- Query: " . str_replace('\n', ' ', $stmt->queryString) . " -->\n";
if (count($unbilled_work_orders) > 0) {
    echo "<!-- First work order: " . print_r($unbilled_work_orders[0], true) . " -->\n";
}

// Get recent invoices
$stmt = $pdo->prepare("
    SELECT i.*, c.name as company_name
    FROM invoices i
    JOIN companies c ON i.company_id = c.id
    ORDER BY i.created_at DESC
    LIMIT 20
");
$stmt->execute();
$recent_invoices = $stmt->fetchAll();
echo "<!-- Found " . count($recent_invoices) . " recent invoices -->\n";

// Debug: Check all work orders to see what's available
$debug_stmt = $pdo->query("SELECT id, title, status, billable, invoiced, company_id FROM work_orders ORDER BY id DESC LIMIT 5");
$debug_wos = $debug_stmt->fetchAll();
echo "<!-- Debug - Recent work orders: " . print_r($debug_wos, true) . " -->\n";

function getInvoiceStatusColor($status) {
    switch ($status) {
        case 'draft': return 'secondary';
        case 'sent': return 'primary';
        case 'paid': return 'success';
        case 'partial': return 'warning';
        case 'overdue': return 'danger';
        case 'cancelled': return 'dark';
        default: return 'secondary';
    }
}

try {
    echo "<!-- About to render page -->\n";
    renderPage(
        'Invoices - SupportTracker',
        'invoices.php',
        compact('unbilled_work_orders', 'recent_invoices', 'companies', 'company_filter', 'error')
    );
    echo "<!-- Page rendered successfully -->\n";
} catch (Exception $e) {
    echo "Error rendering page: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>