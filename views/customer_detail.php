<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-person"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
                <p><strong>Type:</strong> <?= ucfirst($customer['type']) ?></p>
                <?php if ($customer['email']): ?>
                <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                <?php endif; ?>
                <?php if ($customer['phone']): ?>
                <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                <?php endif; ?>
                <?php if ($customer['address']): ?>
                <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($customer['address'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-ticket-perforated"></i> Tickets</h5>
            </div>
            <div class="card-body">
                <?php if ($tickets): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Technician</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td>
                                        <a href="/SupporTracker/ticket-detail?id=<?= $ticket['id'] ?>">
                                            <?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($ticket['title']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $ticket['status'] === 'resolved' ? 'success' : ($ticket['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $ticket['priority'] === 'urgent' ? 'danger' : 
                                            ($ticket['priority'] === 'high' ? 'warning' : 
                                            ($ticket['priority'] === 'medium' ? 'info' : 'success')) 
                                        ?>">
                                            <?= ucfirst($ticket['priority']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($ticket['technician_name'] ?? 'Unassigned') ?></td>
                                    <td><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No tickets found</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="bi bi-laptop"></i> Assets</h5>
            </div>
            <div class="card-body">
                <?php if ($assets): ?>
                    <div class="row">
                        <?php foreach ($assets as $asset): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6><?= htmlspecialchars($asset['name']) ?></h6>
                                    <p class="small text-muted mb-1">
                                        <?= ucfirst($asset['type']) ?>
                                        <?php if ($asset['make'] || $asset['model']): ?>
                                            - <?= htmlspecialchars($asset['make'] . ' ' . $asset['model']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($asset['serial_number']): ?>
                                    <p class="small text-muted mb-0">S/N: <?= htmlspecialchars($asset['serial_number']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No assets found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>