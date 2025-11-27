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
                        <?php if (!empty($locationInfo['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($locationInfo['logo_url']) ?>" alt="Company Logo" style="max-height: 60px; margin-bottom: 10px; display: block;">
                        <?php endif; ?>
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
                            <tr><td><strong>Invoice #:</strong></td><td><strong><?= $invoice['invoice_number'] ?></strong></td></tr>
                            <tr><td><strong>Invoice Date:</strong></td><td><?= date('m/d/Y', strtotime($invoice['issue_date'])) ?></td></tr>
                            <tr><td><strong>Due Date:</strong></td><td><?= date('m/d/Y', strtotime($invoice['due_date'])) ?></td></tr>
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
                                    <?php if (isset($item['discount']) && $item['discount'] > 0): ?>
                                        <?php 
                                        // The unit_price is already the discounted price, so calculate original price
                                        $discountedPrice = $item['unit_price'];
                                        $originalPrice = $discountedPrice / (1 - $item['discount'] / 100);
                                        $savings = ($originalPrice - $discountedPrice) * $item['quantity'];
                                        ?>
                                        <br><small class="text-success">
                                            <i class="bi bi-tag"></i> <?= $item['discount'] ?>% discount - You saved $<?= number_format($savings, 2) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['unit_price'], 2) ?></td>
                                <td>$<?= number_format($item['total_price'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <?php 
                            // Calculate total discount savings
                            $totalSavings = 0;
                            foreach ($invoice_items as $item) {
                                if (isset($item['discount']) && $item['discount'] > 0) {
                                    // Calculate savings the same way as individual items
                                    $discountedPrice = $item['unit_price'];
                                    $originalPrice = $discountedPrice / (1 - $item['discount'] / 100);
                                    $savings = ($originalPrice - $discountedPrice) * $item['quantity'];
                                    $totalSavings += $savings;
                                }
                            }
                            ?>
                            <tr>
                                <th colspan="2" class="text-end">Subtotal:</th>
                                <th class="text-center">$<?= number_format($invoice['subtotal'] ?? 0, 2) ?></th>
                                <?php if (($invoice['tax_amount'] ?? 0) > 0): ?>
                                <th class="text-center">Tax: $<?= number_format($invoice['tax_amount'], 2) ?></th>
                                <?php else: ?>
                                <th></th>
                                <?php endif; ?>
                            </tr>
                            <tr class="table-success">
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
                
                <!-- Payment Instructions (Print Only) -->
                <div class="d-print-block d-none mt-2">
                    <h6><i class="bi bi-credit-card me-2"></i>Payment Instructions</h6>
                    <p class="mb-2"><strong>Pay Online:</strong></p>
                    <ol class="mb-3">
                        <li>Visit: <strong>appligeeks.com/portal</strong></li>
                        <li>Enter your ticket number: <strong><?= $invoice['invoice_number'] ?></strong></li>
                        <li>Enter your phone number for verification</li>
                        <li>Click "Pay Invoice" and follow the secure payment process</li>
                    </ol>
                    <p class="mb-1"><strong>Other Payment Options:</strong></p>
                    <ul class="mb-0">
                        <li>Call us at <strong>724-774-0754</strong> to pay by phone</li>
                        <li>Mail check to: 351 Center Grange Rd, Monaca PA 15061</li>
                        <li>Visit our office during business hours</li>
                    </ul>
                </div>
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
                    <?php if ($invoice['status'] === 'draft'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="mark_sent" value="1">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-paper-plane"></i> Mark as Sent
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline-info btn-sm" onclick="emailInvoice()">
                        <i class="fas fa-envelope"></i> Email Invoice
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
    /* Hide browser's default print headers/footers */
    @page {
        margin: 0.5in;
        size: auto;
    }
    
    /* Hide navigation and header elements */
    .sidebar, .navbar, .btn, .badge, .card-header,
    .d-flex.justify-content-between, main > .d-flex,
    main .border-bottom {
        display: none !important;
    }
    
    /* Hide payments sidebar */
    .col-md-4:nth-child(2) {
        display: none !important;
    }
    
    /* Full width for invoice content */
    .col-md-8 {
        width: 100% !important;
        max-width: 100% !important;
    }
    
    /* Maintain 3-column layout for invoice header */
    .card-body .row .col-md-4 {
        width: 33.333% !important;
        float: left !important;
        display: block !important;
    }
    
    /* Clean styling */
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    body {
        margin: 0 !important;
        padding: 20px !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    /* Show payment instructions only on print */
    .d-print-block {
        display: block !important;
    }
    
    /* Prevent unnecessary page breaks */
    .card-body {
        page-break-inside: avoid;
    }
    
    /* Remove any trailing margins that might cause page breaks */
    .card-body > *:last-child {
        margin-bottom: 0 !important;
    }
    
    /* Make payment instructions compact */
    .d-print-block {
        font-size: 0.8rem !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0 !important;
        padding: 0 !important;
    }
    
    .d-print-block h6 {
        font-size: 0.85rem !important;
        margin-bottom: 0.25rem !important;
    }
    
    .d-print-block p {
        margin-bottom: 0.25rem !important;
    }
    
    .d-print-block ol, .d-print-block ul {
        margin-bottom: 0.25rem !important;
        padding-left: 1rem !important;
    }
    
    .d-print-block li {
        margin-bottom: 0.1rem !important;
        line-height: 1.2 !important;
    }
    
    /* Reduce overall spacing */
    .table {
        margin-bottom: 0.5rem !important;
    }
}
</style>

<script>
function recordPayment() {
    alert('Payment recording functionality coming soon');
}

function emailInvoice() {
    // This will open email client with invoice details
    const subject = encodeURIComponent('Invoice <?= $invoice['invoice_number'] ?> from AppliGeeks');
    const body = encodeURIComponent('Please find your invoice attached. You can also view and pay online at: appligeeks.com/portal\n\nEnter ticket number: <?= $invoice['invoice_number'] ?>\nEnter your phone number for verification.');
    window.location.href = `mailto:<?= htmlspecialchars($invoice['customer_email']) ?>?subject=${subject}&body=${body}`;
}
</script>

