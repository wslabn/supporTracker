<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Open Tickets</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['open_tickets'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-ticket-perforated fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Active Customers</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['customers'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Active Projects</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['projects'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-kanban fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Monthly Revenue</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">$<?= number_format($stats['monthly_revenue'], 0) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Tickets -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Recent Tickets</h6>
                <a href="/SupporTracker/tickets" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if ($recent_tickets): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Assigned</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_tickets as $ticket): ?>
                            <tr>
                                <td>
                                    <a href="/SupporTracker/tickets/<?= $ticket['id'] ?>" class="text-decoration-none fw-bold">
                                        <?= htmlspecialchars($ticket['ticket_number']) ?>
                                    </a>
                                    <div class="small text-muted"><?= htmlspecialchars($ticket['title']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($ticket['customer_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatusColor($ticket['status']) ?> status-badge">
                                        <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-<?= $ticket['priority'] ?>">
                                        <i class="bi bi-circle-fill"></i> <?= ucfirst($ticket['priority']) ?>
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
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-ticket-perforated fs-1"></i>
                    <p class="mt-2">No recent tickets</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Overdue Tickets -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-danger">Overdue Tickets</h6>
            </div>
            <div class="card-body">
                <?php if ($overdue_tickets): ?>
                    <?php foreach ($overdue_tickets as $ticket): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <a href="/SupporTracker/tickets/<?= $ticket['id'] ?>" class="text-decoration-none fw-bold">
                                <?= htmlspecialchars($ticket['ticket_number']) ?>
                            </a>
                            <div class="small text-muted"><?= htmlspecialchars($ticket['customer_name']) ?></div>
                            <div class="small text-danger">
                                <?= date('M j, Y', strtotime($ticket['created_at'])) ?>
                            </div>
                        </div>
                        <span class="badge bg-danger">
                            <?= floor((time() - strtotime($ticket['created_at'])) / 86400) ?> days
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center text-muted">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                    <p class="mt-2">No overdue tickets</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function createTicket() {
    window.location.href = '/SupporTracker/tickets/create';
}
</script>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'open': return 'primary';
        case 'in_progress': return 'warning';
        case 'waiting_customer': return 'info';
        case 'waiting_parts': return 'secondary';
        case 'resolved': return 'success';
        case 'closed': return 'dark';
        default: return 'secondary';
    }
}
?>