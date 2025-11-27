<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<!-- Invoice detail controller loaded -->\n";

$invoice_id = $_GET['id'] ?? 0;
echo "<!-- Invoice ID: $invoice_id -->\n";

// Handle POST requests
if ($_POST) {
    if (isset($_POST['mark_sent'])) {
        $stmt = $pdo->prepare("UPDATE invoices SET status = 'sent' WHERE id = ?");
        $stmt->execute([$invoice_id]);
        header("Location: /SupporTracker/invoices/" . $invoice_id);
        exit;
    }
}

// Get invoice details
try {
    echo "<!-- Getting invoice details -->\n";
    $stmt = $pdo->prepare("
        SELECT i.*, c.name as customer_name, c.email as customer_email, c.address as customer_address
        FROM invoices i
        JOIN customers c ON i.customer_id = c.id
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

// Get invoice items with discount information from original sources
$stmt = $pdo->prepare("
    SELECT ii.description, ii.quantity, ii.rate as unit_price, ii.amount as total_price,
           COALESCE(tbi.discount, tp.discount, 0) as discount
    FROM invoice_items ii
    LEFT JOIN ticket_billing_items tbi ON ii.ticket_id = tbi.ticket_id AND ii.description = tbi.description
    LEFT JOIN ticket_parts tp ON ii.ticket_id = tp.ticket_id AND ii.description = tp.description
    WHERE ii.invoice_id = ?
    ORDER BY ii.id
");
$stmt->execute([$invoice_id]);
$invoice_items = $stmt->fetchAll();

// Get payments for this invoice (payments table may not have invoice_id)
$payments = []; // Temporarily empty until payment structure is confirmed
// TODO: Fix payment structure later

// Get location information for the invoice
$locationInfo = [];
try {
    // Get the customer's location or default location
    $stmt = $pdo->prepare("SELECT l.* FROM locations l LEFT JOIN customers c ON c.location_id = l.id WHERE c.id = ? LIMIT 1");
    $stmt->execute([$invoice['customer_id']]);
    $locationInfo = $stmt->fetch();
    
    // If no location found, get default location
    if (!$locationInfo) {
        $locationInfo = $pdo->query("SELECT * FROM locations WHERE is_default = 1 LIMIT 1")->fetch();
    }
    
    // Get company logo from settings
    $logoStmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'company_logo_url'");
    $logoResult = $logoStmt->fetch();
    if ($logoResult && $logoResult['setting_value']) {
        $locationInfo['logo_url'] = $logoResult['setting_value'];
    }
} catch (Exception $e) {
    // Default values if no location found
    $locationInfo = [
        'name' => 'SupportTracker',
        'address' => '123 Business Street\nMonaca, PA 15061',
        'phone' => '(724) 555-0123',
        'email' => 'support@supporttracker.com'
    ];
}



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

renderModernPage(
    'Invoice ' . $invoice['invoice_number'] . ' - SupportTracker',
    'Invoice ' . $invoice['invoice_number'],
    'invoice_detail.php',
    compact('invoice', 'invoice_items', 'payments', 'invoice_id', 'locationInfo'),
    ''
);
?>