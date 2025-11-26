<?php if (empty($customers)): ?>
<div class="text-center py-5">
    <i class="bi bi-people fs-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No Customers Yet</h5>
    <p class="text-muted">Add your first customer to start tracking tickets and assets</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
        <i class="bi bi-plus-lg me-1"></i>
        Add First Customer
    </button>
</div>
<?php else: ?>

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
                
                <?php if ($customer['primary_contact']): ?>
                <p class="card-text small mb-1">
                    <i class="bi bi-person me-1"></i>
                    <strong><?= htmlspecialchars($customer['primary_contact']) ?></strong>
                </p>
                <?php endif; ?>
                
                <?php if ($customer['contact_email'] ?: $customer['email']): ?>
                <p class="card-text small text-muted mb-1">
                    <i class="bi bi-envelope me-1"></i>
                    <?= htmlspecialchars($customer['contact_email'] ?: $customer['email']) ?>
                </p>
                <?php endif; ?>
                
                <?php if ($customer['contact_phone'] ?: $customer['phone']): ?>
                <p class="card-text small text-muted mb-2">
                    <i class="bi bi-telephone me-1"></i>
                    <?= htmlspecialchars($customer['contact_phone'] ?: $customer['phone']) ?>
                </p>
                <?php endif; ?>
                
                <?php if ($customer['location_name']): ?>
                <p class="card-text small text-muted mb-2">
                    <i class="bi bi-geo-alt me-1"></i>
                    <?= htmlspecialchars($customer['location_name']) ?>
                </p>
                <?php endif; ?>
                
                <div class="row text-center mt-3">
                    <div class="col-4">
                        <div class="fw-bold text-primary"><?= $customer['ticket_count'] ?></div>
                        <div class="small text-muted">Tickets</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-success"><?= $customer['asset_count'] ?></div>
                        <div class="small text-muted">Assets</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-<?= $customer['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <i class="bi bi-<?= $customer['status'] === 'active' ? 'check-circle' : 'pause-circle' ?>"></i>
                        </div>
                        <div class="small text-muted"><?= ucfirst($customer['status']) ?></div>
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
                    <button class="btn btn-outline-info btn-sm" onclick="addAsset(<?= $customer['id'] ?>)">
                        <i class="bi bi-laptop"></i> Asset
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<!-- New Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="create_customer" value="1">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Customer Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="type" id="customerType" onchange="toggleBusinessFields()">
                                    <option value="business">Business</option>
                                    <option value="individual">Individual</option>
                                </select>
                            </div>
                        </div>
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
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3" id="billingAddressField">
                        <label class="form-label">Billing Address (if different)</label>
                        <textarea class="form-control" name="billing_address" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3" id="taxIdField">
                        <label class="form-label">Tax ID / Business License</label>
                        <input type="text" class="form-control" name="tax_id">
                    </div>
                    
                    <hr>
                    <h6>Primary Contact</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact Name</label>
                                <input type="text" class="form-control" name="contact_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="contact_title">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="contact_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" name="contact_phone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_customer" value="1" class="btn btn-primary">Create Customer</button>
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

function addAsset(customerId) {
    window.location.href = `/SupporTracker/assets/create?customer_id=${customerId}`;
}

function toggleBusinessFields() {
    const type = document.getElementById('customerType').value;
    const billingField = document.getElementById('billingAddressField');
    const taxField = document.getElementById('taxIdField');
    
    if (type === 'business') {
        billingField.style.display = 'block';
        taxField.style.display = 'block';
    } else {
        billingField.style.display = 'none';
        taxField.style.display = 'none';
    }
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    toggleBusinessFields();
});
</script>