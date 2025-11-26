<!-- Dashboard Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="bi bi-ticket-perforated fs-1 text-warning mb-2"></i>
                <h2 class="text-warning"><?= $stats['open_tickets'] ?></h2>
                <p class="card-text">Open Tickets</p>
                <a href="/SupporTracker/tickets" class="btn btn-warning btn-sm">Manage</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="bi bi-people fs-1 text-success mb-2"></i>
                <h2 class="text-success"><?= $stats['customers'] ?></h2>
                <p class="card-text">Total Customers</p>
                <a href="/SupporTracker/customers" class="btn btn-success btn-sm">View</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="bi bi-kanban fs-1 text-info mb-2"></i>
                <h2 class="text-info"><?= $stats['projects'] ?></h2>
                <p class="card-text">Active Projects</p>
                <a href="/SupporTracker/projects" class="btn btn-info btn-sm">View</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="bi bi-currency-dollar fs-1 text-primary mb-2"></i>
                <h2 class="text-primary">$<?= number_format($stats['monthly_revenue'], 0) ?></h2>
                <p class="card-text">Monthly Revenue</p>
                <small class="text-muted"><?= date('F Y') ?></small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clock"></i> Recent Tickets</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_tickets): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Customer</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Technician</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_tickets as $ticket): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ticket['ticket_number']) ?></td>
                                    <td><?= htmlspecialchars($ticket['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($ticket['subject']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $ticket['status'] === 'resolved' ? 'success' : ($ticket['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($ticket['technician_name'] ?? 'Unassigned') ?></td>
                                    <td><?= date('M j', strtotime($ticket['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No recent tickets</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-exclamation-triangle"></i> Overdue Tickets</h5>
            </div>
            <div class="card-body">
                <?php if ($overdue_tickets): ?>
                    <?php foreach ($overdue_tickets as $ticket): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <strong><?= htmlspecialchars($ticket['customer_name']) ?></strong><br>
                        <small class="text-muted">
                            <?= htmlspecialchars($ticket['ticket_number']) ?><br>
                            <?= htmlspecialchars($ticket['subject']) ?><br>
                            Created: <?= date('M j, Y', strtotime($ticket['created_at'])) ?>
                        </small>
                        <div class="mt-1">
                            <a href="/SupporTracker/tickets?id=<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-danger">View</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">
                        <i class="bi bi-check-circle text-success"></i><br>
                        No overdue tickets
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="bi bi-plus"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" onclick="createTicket()">
                        <i class="bi bi-plus"></i> New Ticket
                    </button>
                    <a href="/SupporTracker/customers" class="btn btn-success btn-sm">
                        <i class="bi bi-people"></i> Add Customer
                    </a>
                    <a href="/SupporTracker/invoices" class="btn btn-info btn-sm">
                        <i class="bi bi-receipt"></i> Create Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>