<?php
require_once 'config.php';

function getInvoiceStatusColor($status) {
    switch (strtolower($status)) {
        case 'paid': return 'success';
        case 'pending': return 'warning';
        case 'overdue': return 'danger';
        case 'draft': return 'secondary';
        default: return 'primary';
    }
}

// Get invoices
$invoices = $pdo->query("
    SELECT i.*, c.name as customer_name
    FROM invoices i
    LEFT JOIN customers c ON i.customer_id = c.id
    ORDER BY i.created_at DESC
")->fetchAll();

$headerActions = '
<button class="btn btn-primary" onclick="createInvoice()">
    <i class="bi bi-plus-lg me-1"></i>
    New Invoice
</button>';

renderModernPage(
    'Invoices - SupportTracker',
    'Invoices',
    'invoices.php',
    compact('invoices'),
    $headerActions
);
?>