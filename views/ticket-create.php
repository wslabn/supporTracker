<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-ticket-perforated me-2"></i>Create New Ticket</h5>
                <?php if ($customer): ?>
                <small class="text-muted">For customer: <strong><?= htmlspecialchars($customer['name']) ?></strong></small>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="create_ticket" value="1">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Customer *</label>
                                <select class="form-select" name="customer_id" id="customerId" required onchange="loadCustomerAssets()">
                                    <option value="">Select Customer</option>
                                    <?php foreach ($customers as $cust): ?>
                                    <option value="<?= $cust['id'] ?>" <?= ($customer && $customer['id'] == $cust['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cust['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Related Asset</label>
                                <select class="form-select" name="asset_id" id="assetId">
                                    <option value="">No specific asset</option>
                                    <?php foreach ($customerAssets as $asset): ?>
                                    <option value="<?= $asset['id'] ?>">
                                        <?= htmlspecialchars($asset['name']) ?> (<?= htmlspecialchars($asset['category_name']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Service Category *</label>
                        <select class="form-select" name="service_category_id" id="serviceCategory" required onchange="updatePriority()">
                            <option value="">Select Service Category</option>
                            <?php foreach ($serviceCategories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                    data-priority="<?= $category['default_priority'] ?>"
                                    data-sla="<?= $category['sla_hours'] ?>"
                                    data-billable="<?= $category['billable_default'] ?>">
                                <?= htmlspecialchars($category['name']) ?> (<?= $category['sla_hours'] ?>h SLA)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ticket Title *</label>
                        <input type="text" class="form-control" name="title" required placeholder="Brief description of the issue">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Detailed description of the problem, symptoms, and any troubleshooting already attempted"></textarea>
                    </div>
                    
                    <div id="passwordSection" class="mb-3" style="display: none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-key me-2"></i>Device Password Update</h6>
                                <p class="text-muted small mb-2">If the device password has changed since last service, update it here:</p>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" name="updated_device_password" placeholder="New device password or PIN">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="password_changed" id="passwordChanged">
                                            <label class="form-check-label" for="passwordChanged">
                                                Password changed
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority" id="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Billing</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="billable" id="billable" checked>
                                    <label class="form-check-label" for="billable">
                                        Billable ticket
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/SupporTracker/customers" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Customers
                        </a>
                        <div>
                            <button type="submit" class="btn btn-success me-2" name="print_receipt" value="1">
                                <i class="bi bi-printer me-1"></i>Create & Print Receipt
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Create Ticket
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updatePriority() {
    const categorySelect = document.getElementById('serviceCategory');
    const prioritySelect = document.getElementById('priority');
    const billableCheck = document.getElementById('billable');
    
    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
    if (selectedOption.value) {
        const priority = selectedOption.getAttribute('data-priority');
        const billable = selectedOption.getAttribute('data-billable');
        
        prioritySelect.value = priority;
        billableCheck.checked = billable == '1';
    }
}

function loadCustomerAssets() {
    const customerId = document.getElementById('customerId').value;
    const assetSelect = document.getElementById('assetId');
    
    // Clear current assets
    assetSelect.innerHTML = '<option value="">No specific asset</option>';
    
    if (customerId) {
        // In a real implementation, this would make an AJAX call
        // For now, we'll reload the page with the customer selected
        if (!<?= $customer ? 'true' : 'false' ?>) {
            window.location.href = `/SupporTracker/tickets/create?customer_id=${customerId}`;
        }
    }
}

// Show/hide password section when asset is selected
document.getElementById('assetId').addEventListener('change', function() {
    const passwordSection = document.getElementById('passwordSection');
    if (this.value) {
        passwordSection.style.display = 'block';
    } else {
        passwordSection.style.display = 'none';
    }
});
</script>