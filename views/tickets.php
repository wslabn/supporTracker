<?php if (empty($tickets)): ?>
<div class="text-center py-5">
    <i class="bi bi-ticket-perforated fs-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No Tickets Yet</h5>
    <p class="text-muted">Create your first support ticket to start tracking work</p>
    <a href="/SupporTracker/tickets/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Create First Ticket
    </a>
</div>
<?php else: ?>

<!-- Tickets List -->
<div class="row">
    <?php foreach ($tickets as $ticket): ?>
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1"><?= htmlspecialchars($ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT)) ?></h6>
                    <small class="text-muted"><?= htmlspecialchars($ticket['service_category_name'] ?? 'General') ?></small>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?= 
                        $ticket['priority'] === 'urgent' ? 'danger' : 
                        ($ticket['priority'] === 'high' ? 'warning' : 
                        ($ticket['priority'] === 'medium' ? 'info' : 'success')) 
                    ?>">
                        <?= ucfirst($ticket['priority']) ?>
                    </span>
                    <br>
                    <span class="badge bg-<?= 
                        $ticket['status'] === 'resolved' ? 'success' : 
                        ($ticket['status'] === 'in_progress' ? 'warning' : 
                        ($ticket['status'] === 'waiting' ? 'info' : 'secondary')) 
                    ?> mt-1">
                        <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <h6 class="card-title"><?= htmlspecialchars($ticket['title']) ?></h6>
                
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-person me-1"></i>
                        <strong><?= htmlspecialchars($ticket['customer_name']) ?></strong>
                    </small>
                </div>
                
                <?php if ($ticket['asset_name']): ?>
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-laptop me-1"></i>
                        <?= htmlspecialchars($ticket['asset_name']) ?>
                    </small>
                </div>
                <?php endif; ?>
                
                <?php if ($ticket['technician_name']): ?>
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-person-gear me-1"></i>
                        Assigned to: <strong><?= htmlspecialchars($ticket['technician_name']) ?></strong>
                    </small>
                </div>
                <?php else: ?>
                <div class="mb-2">
                    <small class="text-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Unassigned
                    </small>
                </div>
                <?php endif; ?>
                
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Created: <?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?>
                    </small>
                </div>
                
                <?php if ($ticket['sla_hours']): ?>
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-stopwatch me-1"></i>
                        SLA: <?= $ticket['sla_hours'] ?> hours
                    </small>
                </div>
                <?php endif; ?>
                
                <?php if ($ticket['description']): ?>
                <p class="card-text small text-muted mt-2">
                    <?= nl2br(htmlspecialchars(substr($ticket['description'], 0, 100))) ?>
                    <?= strlen($ticket['description']) > 100 ? '...' : '' ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <div class="btn-group w-100" role="group">
                    <?php if (!$ticket['technician_name']): ?>
                    <button class="btn btn-outline-primary btn-sm" onclick="assignTicket(<?= $ticket['id'] ?>)">
                        <i class="bi bi-person-plus"></i> Assign
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($ticket['status'] === 'open'): ?>
                    <button class="btn btn-outline-warning btn-sm" onclick="updateStatus(<?= $ticket['id'] ?>, 'in_progress')">
                        <i class="bi bi-play"></i> Start Work
                    </button>
                    <?php elseif ($ticket['status'] === 'in_progress'): ?>
                    <button class="btn btn-outline-success btn-sm" onclick="updateStatus(<?= $ticket['id'] ?>, 'resolved')">
                        <i class="bi bi-check"></i> Resolve
                    </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline-info btn-sm" onclick="viewTicket(<?= $ticket['id'] ?>)">
                        <i class="bi bi-eye"></i> View
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<!-- Assignment Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="assign_ticket" value="1">
                    <input type="hidden" name="ticket_id" id="assignTicketId">
                    
                    <div class="mb-3">
                        <label class="form-label">Assign to Technician</label>
                        <select class="form-select" name="technician_id" required>
                            <option value="">Select Technician</option>
                            <?php foreach ($technicians as $tech): ?>
                            <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Form (hidden) -->
<form method="POST" id="statusForm" style="display: none;">
    <input type="hidden" name="update_status" value="1">
    <input type="hidden" name="ticket_id" id="statusTicketId">
    <input type="hidden" name="status" id="statusValue">
</form>

<script>
function assignTicket(ticketId) {
    document.getElementById('assignTicketId').value = ticketId;
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}

function updateStatus(ticketId, status) {
    document.getElementById('statusTicketId').value = ticketId;
    document.getElementById('statusValue').value = status;
    document.getElementById('statusForm').submit();
}

function viewTicket(ticketId) {
    window.location.href = `/SupporTracker/ticket-detail?id=${ticketId}`;
}
</script>