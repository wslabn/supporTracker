<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5><i class="fas fa-file-invoice"></i> Invoice <?= $invoice['invoice_number'] ?></h5>
                <div>
                    <span class="badge bg-<?= getInvoiceStatusColor($invoice['status']) ?> me-2">
                        <?= ucfirst($invoice['status']) ?>
                    </span>
                    <button class="btn btn-sm btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">

                        <h6>From:</h6>
                        <strong><?= htmlspecialchars($locationInfo['name'] ?? 'SupportTracker') ?></strong><br>
                        <?php if (!empty($locationInfo['address'])): ?>
                            <?= nl2br(htmlspecialchars($locationInfo['address'])) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($locationInfo['phone'])): ?>
                            <?= htmlspecialchars($locationInfo['phone']) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($locationInfo['email'])): ?>
                            <?= htmlspecialchars($locationInfo['email']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <h6>Bill To:</h6>
                        <strong><?= htmlspecialchars($invoice['customer_name']) ?></strong><br>
                        <?php if ($invoice['customer_address']): ?>
                            <?= nl2br(htmlspecialchars($invoice['customer_address'])) ?><br>
                        <?php endif; ?>
                        <?php if ($invoice['customer_email']): ?>
                            <?= htmlspecialchars($invoice['customer_email']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <table class="table table-sm table-borderless">
                            <tr><td><strong>Invoice Date:</strong></td><td><?= date('M j, Y', strtotime($invoice['issue_date'])) ?></td></tr>
                            <tr><td><strong>Due Date:</strong></td><td><?= date('M j, Y', strtotime($invoice['due_date'])) ?></td></tr>
                            <tr><td><strong>Amount Due:</strong></td><td><strong>$<?= number_format($invoice['total'] ?? 0, 2) ?></strong></td></tr>
                        </table>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoice_items as $item): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($item['description']) ?>
                                </td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['unit_price'], 2) ?></td>
                                <td>$<?= number_format($item['total_price'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th>$<?= number_format($invoice['subtotal'], 2) ?></th>
                            </tr>
                            <?php if ($invoice['tax_amount'] > 0): ?>
                            <tr>
                                <th colspan="3" class="text-end">Tax:</th>
                                <th>$<?= number_format($invoice['tax_amount'], 2) ?></th>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th>$<?= number_format($invoice['total'] ?? 0, 2) ?></th>
                            </tr>
                            <?php if (($invoice['paid_amount'] ?? 0) > 0): ?>
                            <tr>
                                <td colspan="3" class="text-end">Paid:</td>
                                <td>$<?= number_format($invoice['paid_amount'], 2) ?></td>
                            </tr>
                            <tr class="table-warning">
                                <th colspan="3" class="text-end">Balance Due:</th>
                                <th>$<?= number_format($invoice['balance'] ?? $invoice['balance_due'] ?? 0, 2) ?></th>
                            </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                </div>

                <?php if (!empty($invoice['notes'])): ?>
                <div class="mt-3">
                    <h6>Notes:</h6>
                    <p><?= nl2br(htmlspecialchars($invoice['notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-credit-card"></i> Payments</h6>
            </div>
            <div class="card-body">
                <?php if ($payments): ?>
                    <?php foreach ($payments as $payment): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <strong>$<?= number_format($payment['amount'], 2) ?></strong>
                        <br><small class="text-muted">
                            <?= date('M j, Y', strtotime($payment['payment_date'])) ?>
                            - <?= ucfirst($payment['payment_method']) ?>
                            <?php if ($payment['reference_number']): ?>
                                <br>Ref: <?= htmlspecialchars($payment['reference_number']) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No payments recorded</p>
                <?php endif; ?>
                
                <button class="btn btn-success btn-sm mt-2" onclick="recordPayment()">
                    <i class="fas fa-plus"></i> Record Payment
                </button>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6><i class="fas fa-cog"></i> Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="editInvoice()">
                        <i class="fas fa-edit"></i> Edit Invoice
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="sendInvoice()">
                        <i class="fas fa-envelope"></i> Send Invoice
                    </button>
                    <a href="/SupporTracker/invoices" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Invoices
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .card:not(.card:first-child), .btn, .col-md-4 {
        display: none !important;
    }
    .col-md-8 {
        width: 100% !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<script>
function recordPayment() {
    alert('Payment recording functionality coming soon');
}

function editInvoice() {
    alert('Invoice editing functionality coming soon');
}

function sendInvoice() {
    alert('Email sending functionality coming soon');
}
</script>

