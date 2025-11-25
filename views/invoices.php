<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar"></i> Generate Invoice</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" id="invoiceForm" onsubmit="console.log('Form submitted'); return true;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <select class="form-select" name="company_id" id="company_filter" onchange="filterWorkOrders()">
                                <option value="">Select Company</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?= $company['id'] ?>" <?= $company_filter == $company['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($company['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" name="generate_invoice" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Generate Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($unbilled_work_orders): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="select_all" onchange="toggleAll(this)">
                                    </th>
                                    <th>Work Order</th>
                                    <th>Company</th>
                                    <th>Labor</th>
                                    <th>Parts</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unbilled_work_orders as $wo): ?>
                                    <?php if (!$company_filter || $wo['company_id'] == $company_filter): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="work_order_ids[]" value="<?= $wo['id'] ?>" class="wo-checkbox">
                                        </td>
                                        <td>
                                            <a href="/SupporTracker/workorder?id=<?= $wo['id'] ?>">#<?= $wo['id'] ?></a>
                                            <br><small class="text-muted"><?= htmlspecialchars($wo['title']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($wo['company_name']) ?></td>
                                        <td>$<?= number_format($wo['labor_total'] ?? 0, 2) ?></td>
                                        <td>$<?= number_format($wo['parts_total'] ?? 0, 2) ?></td>
                                        <td><strong>$<?= number_format(($wo['labor_total'] ?? 0) + ($wo['parts_total'] ?? 0), 2) ?></strong></td>
                                        <td><?= date('M j, Y', strtotime($wo['created_at'])) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No unbilled work orders found. 
                            <?php if ($company_filter): ?>
                                <a href="/SupporTracker/invoices">Show all companies</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-history"></i> Recent Invoices</h6>
            </div>
            <div class="card-body p-0">
                <?php if ($recent_invoices): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_invoices as $invoice): ?>
                    <a href="/SupporTracker/invoice?id=<?= $invoice['id'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?= $invoice['invoice_number'] ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($invoice['company_name']) ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?= getInvoiceStatusColor($invoice['status']) ?>">
                                    <?= ucfirst($invoice['status']) ?>
                                </span>
                                <br><small class="text-muted">$<?= number_format($invoice['total_amount'], 2) ?></small>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted p-3">No invoices created yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Invoices page JavaScript loaded');

function filterWorkOrders() {
    console.log('filterWorkOrders called');
    const companyId = document.getElementById('company_filter').value;
    if (companyId) {
        window.location.href = '/SupporTracker/invoices?company_id=' + companyId;
    } else {
        window.location.href = '/SupporTracker/invoices';
    }
}

function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.wo-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}
</script>