<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<!-- Invoice detail controller loaded -->\n";

$invoice_id = $_GET['id'] ?? 0;
echo "<!-- Invoice ID: $invoice_id -->\n";

// Get invoice details
try {
    echo "<!-- Getting invoice details -->\n";
    $stmt = $pdo->prepare("
        SELECT i.*, c.name as company_name, c.email as company_email, c.address as company_address
        FROM invoices i
        JOIN companies c ON i.company_id = c.id
        WHERE i.id = ?
    ");
    $stmt->execute([$invoice_id]);
    $invoice = $stmt->fetch();
    echo "<!-- Invoice found: " . ($invoice ? 'yes' : 'no') . " -->\n";
} catch (Exception $e) {
    echo "<!-- Error getting invoice: " . $e->getMessage() . " -->\n";
    die("Database error: " . $e->getMessage());
}

if (!$invoice) {
    header('Location: /SupporTracker/invoices');
    exit;
}

// Get invoice items
$stmt = $pdo->prepare("
    SELECT ii.*, wo.title as work_order_title
    FROM invoice_items ii
    LEFT JOIN work_orders wo ON ii.work_order_id = wo.id
    WHERE ii.invoice_id = ?
    ORDER BY ii.id
");
$stmt->execute([$invoice_id]);
$invoice_items = $stmt->fetchAll();

// Get payments for this invoice (payments table may not have invoice_id)
$payments = []; // Temporarily empty until payment structure is confirmed
// TODO: Fix payment structure later

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

renderPage(
    'Invoice ' . $invoice['invoice_number'] . ' - SupportTracker',
    'invoice_detail.php',
    compact('invoice', 'invoice_items', 'payments', 'invoice_id')
);
?>