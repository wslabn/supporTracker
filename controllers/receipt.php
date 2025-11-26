<?php
require_once 'config.php';

$ticketId = $_GET['id'] ?? null;
if (!$ticketId) {
    header('Location: /SupporTracker/tickets');
    exit;
}

// Get ticket details
$stmt = $pdo->prepare("
    SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
           a.name as asset_name, a.model as asset_model, a.serial_number,
           u.name as technician_name,
           sc.name as service_category_name, sc.sla_hours
    FROM tickets t
    LEFT JOIN customers c ON t.customer_id = c.id
    LEFT JOIN assets a ON t.asset_id = a.id
    LEFT JOIN users u ON t.assigned_to = u.id
    LEFT JOIN service_categories sc ON t.service_category_id = sc.id
    WHERE t.id = ?
");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header('Location: /SupporTracker/tickets');
    exit;
}

// Check portal settings
$portalEnabled = false;
try {
    $settings = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'portal_enabled'")->fetch();
    $portalEnabled = $settings && $settings['setting_value'] == '1';
} catch (Exception $e) {
    // Settings table doesn't exist or no portal setting
}

// Generate ticket number if not exists
$ticketNumber = $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Work Order Receipt - <?= $ticketNumber ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .receipt { max-width: none !important; }
        }
        .receipt {
            max-width: 600px;
            margin: 0 auto;
            font-family: 'Courier New', monospace;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .receipt-section {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #ccc;
        }
        .receipt-footer {
            text-align: center;
            font-size: 0.9rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="receipt bg-white p-4 shadow">
            <div class="receipt-header">
                <h2><strong>WORK ORDER RECEIPT</strong></h2>
                <p class="mb-0">SupportTracker MSP Services</p>
                <p class="mb-0">(555) 123-4567 • support@yourcompany.com</p>
            </div>
            
            <div class="receipt-section">
                <div class="row">
                    <div class="col-6">
                        <strong>TICKET NUMBER:</strong><br>
                        <span class="fs-5"><?= $ticketNumber ?></span>
                    </div>
                    <div class="col-6 text-end">
                        <strong>DATE:</strong><br>
                        <?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?>
                    </div>
                </div>
            </div>
            
            <div class="receipt-section">
                <strong>CUSTOMER INFORMATION:</strong><br>
                Name: <?= htmlspecialchars($ticket['customer_name']) ?><br>
                Phone: <?= htmlspecialchars($ticket['customer_phone']) ?><br>
                <?php if ($ticket['customer_email']): ?>
                Email: <?= htmlspecialchars($ticket['customer_email']) ?><br>
                <?php endif; ?>
            </div>
            
            <?php if ($ticket['asset_name']): ?>
            <div class="receipt-section">
                <strong>DEVICE INFORMATION:</strong><br>
                Device: <?= htmlspecialchars($ticket['asset_name']) ?><br>
                Model: <?= htmlspecialchars($ticket['asset_model']) ?><br>
                <?php if ($ticket['serial_number']): ?>
                Serial: <?= htmlspecialchars($ticket['serial_number']) ?><br>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="receipt-section">
                <strong>SERVICE DETAILS:</strong><br>
                Issue: <?= htmlspecialchars($ticket['title']) ?><br>
                <?php if ($ticket['description']): ?>
                Description: <?= htmlspecialchars($ticket['description']) ?><br>
                <?php endif; ?>
                Category: <?= htmlspecialchars($ticket['service_category_name']) ?><br>
                Priority: <?= ucfirst($ticket['priority']) ?><br>
                Status: <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
            </div>
            
            <div class="receipt-footer">
                <?php if ($portalEnabled): ?>
                <p><strong>CHECK YOUR REPAIR STATUS ONLINE:</strong></p>
                <p class="mb-1">Visit: <strong>yourcompany.com/SupporTracker/portal</strong></p>
                <p class="mb-3">Enter: Ticket Number + Phone Number</p>
                <?php endif; ?>
                
                <p class="small text-muted">
                    Keep this receipt for your records.<br>
                    <?php if ($portalEnabled): ?>
                    You will need the ticket number to check status online.<br>
                    <?php endif; ?>
                    Call (555) 123-4567 for questions or updates.
                </p>
            </div>
        </div>
        
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="bi bi-printer"></i> Print Receipt
            </button>
            <a href="/SupporTracker/tickets" class="btn btn-secondary">
                Back to Tickets
            </a>
        </div>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>