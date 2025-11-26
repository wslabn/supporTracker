<!-- Date Filter -->
<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" class="d-flex gap-2">
            <input type="date" class="form-control" name="date" value="<?= $date ?>" onchange="this.form.submit()">
            <button type="submit" class="btn btn-primary">View</button>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="/SupporTracker/reports?date=<?= date('Y-m-d') ?>" class="btn btn-outline-secondary">Today</a>
        <a href="/SupporTracker/reports?date=<?= date('Y-m-d', strtotime('-1 day')) ?>" class="btn btn-outline-secondary">Yesterday</a>
    </div>
</div>

<!-- Technician Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-people me-2"></i>Technician Summary</h5>
            </div>
            <div class="card-body">
                <?php if ($summary): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Technician</th>
                                    <th>Tickets Worked</th>
                                    <th>Parts Sold</th>
                                    <th>Parts Revenue</th>
                                    <th>Hours Logged</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summary as $tech): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($tech['technician_name']) ?></strong></td>
                                    <td><?= $tech['tickets_worked'] ?></td>
                                    <td><?= $tech['parts_sold'] ?></td>
                                    <td class="text-success"><strong>$<?= number_format($tech['parts_revenue'], 2) ?></strong></td>
                                    <td><?= $tech['hours_logged'] ?> hrs</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No activity for this date.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ticket Activity -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-ticket me-2"></i>Ticket Activity</h5>
            </div>
            <div class="card-body">
                <?php if ($tickets): ?>
                    <?php foreach ($tickets as $ticket): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                                - <?= htmlspecialchars($ticket['customer_name']) ?>
                                <br>
                                <span class="text-muted"><?= htmlspecialchars($ticket['title']) ?></span>
                                <br>
                                <span class="badge bg-<?= 
                                    $ticket['status'] === 'resolved' ? 'success' : 
                                    ($ticket['status'] === 'in_progress' ? 'warning' : 
                                    ($ticket['status'] === 'waiting' ? 'info' : 'secondary')) 
                                ?>"><?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?></span>
                                <?php if ($ticket['update_count'] > 0): ?>
                                <span class="badge bg-primary"><?= $ticket['update_count'] ?> updates</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">
                                    <?= htmlspecialchars($ticket['technician_name'] ?? 'Unassigned') ?>
                                </small>
                                <br>
                                <a href="/SupporTracker/ticket-detail?id=<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No ticket activity for this date.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Parts Sales -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear me-2"></i>Parts Sales</h5>
            </div>
            <div class="card-body">
                <?php if ($parts): ?>
                    <?php foreach ($parts as $part): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <strong><?= htmlspecialchars($part['description']) ?></strong>
                        <br>
                        <small class="text-muted">
                            <?= htmlspecialchars($part['customer_name']) ?>
                            <br>
                            <?= $part['ticket_number'] ?? 'TKT-' . str_pad($part['ticket_id'], 6, '0', STR_PAD_LEFT) ?>
                        </small>
                        <br>
                        <span class="text-success fw-bold">$<?= number_format($part['sell_price'], 2) ?></span>
                        <span class="badge bg-<?= 
                            $part['status'] === 'installed' ? 'success' : 
                            ($part['status'] === 'received' ? 'info' : 
                            ($part['status'] === 'ordered' ? 'warning' : 'secondary')) 
                        ?>"><?= ucfirst($part['status']) ?></span>
                        <br>
                        <small class="text-muted"><?= htmlspecialchars($part['technician_name']) ?></small>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="mt-3 pt-2 border-top">
                        <strong>Total: $<?= number_format(array_sum(array_column($parts, 'sell_price')), 2) ?></strong>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No parts sales for this date.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>