<?php if ($user_role === 'admin'): ?>
<!-- Admin Dashboard -->
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-tachometer-alt"></i> Business Overview</h1>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-clipboard-list fa-2x text-warning mb-2"></i>
                <h2 class="text-warning"><?= $stats['open_workorders'] ?></h2>
                <p class="card-text">Open Work Orders</p>
                <a href="/SupporTracker/workorders" class="btn btn-warning btn-sm">Manage</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h2 class="text-success"><?= $stats['completed_this_month'] ?></h2>
                <p class="card-text">Completed This Month</p>
                <small class="text-muted">Work Orders</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <i class="fas fa-file-invoice-dollar fa-2x text-danger mb-2"></i>
                <h2 class="text-danger"><?= $stats['outstanding_invoices'] ?></h2>
                <p class="card-text">Outstanding Invoices</p>
                <a href="/SupporTracker/invoices" class="btn btn-danger btn-sm">Review</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-primary mb-2"></i>
                <h2 class="text-primary">$<?= number_format($stats['monthly_revenue'], 0) ?></h2>
                <p class="card-text">Monthly Revenue</p>
                <small class="text-muted"><?= date('F Y') ?></small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Recent Work Orders</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_workorders): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_workorders as $wo): ?>
                                <tr>
                                    <td>
                                        <?php if ($wo['company_name']): ?>
                                            <?= htmlspecialchars($wo['company_name']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($wo['customer_name']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($wo['title']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $wo['status'] === 'completed' ? 'success' : ($wo['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst($wo['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j', strtotime($wo['created_at'])) ?></td>
                                    <td>
                                        <a href="/SupporTracker/workorder?id=<?= $wo['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No recent work orders</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-exclamation-triangle"></i> Overdue Invoices</h5>
            </div>
            <div class="card-body">
                <?php if ($overdue_invoices): ?>
                    <?php foreach ($overdue_invoices as $invoice): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <strong><?= htmlspecialchars($invoice['company_name']) ?></strong><br>
                        <small class="text-muted">
                            Invoice #<?= $invoice['invoice_number'] ?><br>
                            Due: <?= date('M j, Y', strtotime($invoice['due_date'])) ?><br>
                            Amount: $<?= number_format($invoice['total_amount'], 2) ?>
                        </small>
                        <div class="mt-1">
                            <a href="/SupporTracker/invoice?id=<?= $invoice['id'] ?>" class="btn btn-xs btn-outline-danger">View</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">
                        <i class="fas fa-check-circle text-success"></i><br>
                        No overdue invoices
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" onclick="openWorkOrderModal()">
                        <i class="fas fa-plus"></i> New Work Order
                    </button>
                    <a href="/SupporTracker/companies" class="btn btn-success btn-sm">
                        <i class="fas fa-building"></i> Add Company
                    </a>
                    <a href="/SupporTracker/invoices" class="btn btn-info btn-sm">
                        <i class="fas fa-file-invoice"></i> Create Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Basic Dashboard for other roles -->
<div class="row">
    <div class="col-12">
        <h1>Dashboard</h1>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-primary"><?= $stats['companies'] ?></h2>
                        <p class="card-text">Active Companies</p>
                        <a href="/SupporTracker/companies" class="btn btn-primary btn-sm">View Companies</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-success"><?= $stats['assets'] ?></h2>
                        <p class="card-text">Total Assets</p>
                        <a href="/SupporTracker/assets" class="btn btn-success btn-sm">View Assets</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-info"><?= $stats['employees'] ?></h2>
                        <p class="card-text">Employees</p>
                        <a href="/SupporTracker/employees" class="btn btn-info btn-sm">View Employees</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>