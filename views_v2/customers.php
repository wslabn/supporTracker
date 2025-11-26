<!-- Customers Grid -->
<div class="row">
    <?php foreach ($customers as $customer): ?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0"><?= htmlspecialchars($customer['name']) ?></h5>
                    <span class="badge bg-<?= $customer['type'] === 'business' ? 'primary' : 'info' ?>">
                        <?= ucfirst($customer['type']) ?>
                    </span>
                </div>
                
                <?php if ($customer['email']): ?>
                <p class="card-text small text-muted mb-1">
                    <i class="bi bi-envelope me-1"></i>
                    <?= htmlspecialchars($customer['email']) ?>
                </p>
                <?php endif; ?>
                
                <?php if ($customer['phone']): ?>
                <p class="card-text small text-muted mb-2">
                    <i class="bi bi-telephone me-1"></i>
                    <?= htmlspecialchars($customer['phone']) ?>
                </p>
                <?php endif; ?>
                
                <div class="row text-center mt-3">
                    <div class="col-6">
                        <div class="fw-bold text-primary"><?= $customer['ticket_count'] ?></div>
                        <div class="small text-muted">Tickets</div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold text-success"><?= $customer['asset_count'] ?></div>
                        <div class="small text-muted">Assets</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="btn-group w-100" role="group">
                    <button class="btn btn-outline-primary btn-sm" onclick="viewCustomer(<?= $customer['id'] ?>)">
                        <i class="bi bi-eye"></i> View
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="createTicket(<?= $customer['id'] ?>)">
                        <i class="bi bi-plus"></i> Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- New Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Customer Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="business">Business</option>
                            <option value="individual">Individual</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_customer" class="btn btn-primary">Create Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewCustomer(id) {
    window.location.href = `/SupporTracker/customers/${id}`;
}

function createTicket(customerId) {
    window.location.href = `/SupporTracker/tickets/create?customer_id=${customerId}`;
}
</script>