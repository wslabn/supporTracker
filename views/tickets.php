<!-- Filters -->
<div class="row mb-3">
    <div class="col-md-3">
        <select class="form-select" onchange="filterTickets('status', this.value)">
            <option value="">All Status</option>
            <option value="open" <?= ($_GET['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
            <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="waiting_customer" <?= ($_GET['status'] ?? '') === 'waiting_customer' ? 'selected' : '' ?>>Waiting Customer</option>
            <option value="resolved" <?= ($_GET['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" onchange="filterTickets('priority', this.value)">
            <option value="">All Priority</option>
            <option value="urgent" <?= ($_GET['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
            <option value="high" <?= ($_GET['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
            <option value="medium" <?= ($_GET['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
            <option value="low" <?= ($_GET['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
        </select>
    </div>
</div>

<!-- Tickets Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ticket</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($ticket['ticket_number']) ?></div>
                            <div class="small text-muted"><?= htmlspecialchars($ticket['title']) ?></div>
                        </td>
                        <td>
                            <?= htmlspecialchars($ticket['customer_name']) ?>
                            <?php if ($ticket['contact_name']): ?>
                                <div class="small text-muted"><?= htmlspecialchars($ticket['contact_name']) ?></div>
                            <?php endif; ?>
                        </td>
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
                        <td><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewTicket(<?= $ticket['id'] ?>)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer *</label>
                                <select class="form-select" name="customer_id" required onchange="loadCustomerData(this.value)">
                                    <option value="">Select Customer</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact</label>
                                <select class="form-select" name="contact_id" id="contact_id">
                                    <option value="">Select Contact</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Asset</label>
                                <select class="form-select" name="asset_id" id="asset_id">
                                    <option value="">Select Asset</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Assign To</label>
                                <select class="form-select" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($technicians as $tech): ?>
                                        <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category">
                                    <option value="hardware">Hardware</option>
                                    <option value="software">Software</option>
                                    <option value="network">Network</option>
                                    <option value="security">Security</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="other" selected>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" name="estimated_hours" step="0.25" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="billable" id="billable" checked>
                        <label class="form-check-label" for="billable">
                            Billable
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_ticket" class="btn btn-primary">Create Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterTickets(type, value) {
    const url = new URL(window.location);
    if (value) {
        url.searchParams.set(type, value);
    } else {
        url.searchParams.delete(type);
    }
    window.location = url;
}

function loadCustomerData(customerId) {
    if (!customerId) return;
    
    // Load contacts
    fetch(`/SupporTracker/api/customer-contacts?customer_id=${customerId}`)
        .then(response => response.json())
        .then(contacts => {
            const contactSelect = document.getElementById('contact_id');
            contactSelect.innerHTML = '<option value="">Select Contact</option>';
            contacts.forEach(contact => {
                contactSelect.innerHTML += `<option value="${contact.id}">${contact.name}</option>`;
            });
        });
    
    // Load assets
    fetch(`/SupporTracker/api/customer-assets?customer_id=${customerId}`)
        .then(response => response.json())
        .then(assets => {
            const assetSelect = document.getElementById('asset_id');
            assetSelect.innerHTML = '<option value="">Select Asset</option>';
            assets.forEach(asset => {
                assetSelect.innerHTML += `<option value="${asset.id}">${asset.name}</option>`;
            });
        });
}

function viewTicket(id) {
    window.location.href = `/SupporTracker/tickets/${id}`;
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