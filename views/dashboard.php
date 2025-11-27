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
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Technician</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_tickets as $ticket): ?>
                                <tr>
                                    <td>
                                        <a href="/SupporTracker/ticket-detail?id=<?= $ticket['id'] ?>">
                                            <?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?>
                                        </a>
                                        <?php if ($ticket['unread_messages'] > 0): ?>
                                        <span class="badge bg-danger ms-1"><?= $ticket['unread_messages'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($ticket['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($ticket['title']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $ticket['priority'] === 'urgent' ? 'danger' : 
                                            ($ticket['priority'] === 'high' ? 'warning' : 
                                            ($ticket['priority'] === 'medium' ? 'info' : 'success')) 
                                        ?>">
                                            <?= ucfirst($ticket['priority']) ?>
                                        </span>
                                        <br>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            Response time varies
                                        </small>

                                    </td>
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
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?= htmlspecialchars($ticket['customer_name']) ?></strong>
                                <?php if ($ticket['unread_messages'] > 0): ?>
                                <span class="badge bg-danger ms-1"><?= $ticket['unread_messages'] ?> msg</span>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-<?= 
                                $ticket['priority'] === 'urgent' ? 'danger' : 
                                ($ticket['priority'] === 'high' ? 'warning' : 
                                ($ticket['priority'] === 'medium' ? 'info' : 'success')) 
                            ?>">
                                <?= ucfirst($ticket['priority']) ?>
                            </span>
                        </div>
                        <small class="text-muted">
                            <?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?><br>
                            <?= htmlspecialchars($ticket['title']) ?><br>
                            Created: <?= date('M j, Y', strtotime($ticket['created_at'])) ?><br>

                        </small>
                        <div class="mt-1">
                            <a href="/SupporTracker/ticket-detail?id=<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-danger">View</a>
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