<!-- Invoices Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($invoices): ?>
                        <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($invoice['invoice_number']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($invoice['customer_name']) ?></td>
                            <td><?= date('M j, Y', strtotime($invoice['issue_date'])) ?></td>
                            <td><?= date('M j, Y', strtotime($invoice['due_date'])) ?></td>
                            <td>$<?= number_format($invoice['total'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= getInvoiceStatusColor($invoice['status']) ?>">
                                    <?= ucfirst($invoice['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewInvoice(<?= $invoice['id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="downloadInvoice(<?= $invoice['id'] ?>)">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-receipt fs-1"></i>
                                <p class="mt-2">No invoices found</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function createInvoice() {
    window.location.href = '/SupporTracker/invoices/create';
}

function viewInvoice(id) {
    window.location.href = '/SupporTracker/invoices/' + id;
}

function downloadInvoice(id) {
    window.open('/SupporTracker/invoices/' + id + '/pdf', '_blank');
}
</script>

<?php
function getInvoiceStatusColor($status) {
    switch ($status) {
        case 'draft': return 'secondary';
        case 'sent': return 'primary';
        case 'paid': return 'success';
        case 'overdue': return 'danger';
        case 'cancelled': return 'dark';
        default: return 'secondary';
    }
}
?>