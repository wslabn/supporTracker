<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Work Orders</h2>
            <button class="btn btn-success" onclick="openWorkOrderModal()">
                <i class="fas fa-plus"></i> Create Work Order
            </button>
        </div>

        <?php if ($company_filter || $asset_filter): ?>
        <div class="alert alert-info">
            Showing work orders for: 
            <?php if ($company_name): ?>
                <strong><?= htmlspecialchars($company_name) ?></strong>
            <?php endif; ?>
            <?php if ($asset_name): ?>
                <?= $company_name ? ' > ' : '' ?><strong><?= htmlspecialchars($asset_name) ?></strong>
            <?php endif; ?>
            <a href="workorders" class="btn btn-sm btn-outline-primary ms-2">Show All</a>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">Work Order List</h6>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select form-select-sm" onchange="filterByCompany(this.value)">
                            <option value="">All Companies</option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?= $company['id'] ?>" <?= $company_filter == $company['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($company['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Asset</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Billable</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workorders as $wo): ?>
                        <tr>
                            <td>
                                <a href="workorder?id=<?= $wo['id'] ?>" class="fw-bold text-decoration-none">
                                    #<?= $wo['id'] ?>
                                </a>
                            </td>
                            <td>
                                <a href="workorder?id=<?= $wo['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($wo['title']) ?>
                                </a>
                                <?php if ($wo['description']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(substr($wo['description'], 0, 50)) ?><?= strlen($wo['description']) > 50 ? '...' : '' ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($wo['company_id']): ?>
                                    <a href="company?id=<?= $wo['company_id'] ?>"><?= htmlspecialchars($wo['company_name']) ?></a>
                                <?php else: ?>
                                    <strong><?= htmlspecialchars($wo['customer_name']) ?></strong>
                                    <?php if ($wo['customer_phone']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($wo['customer_phone']) ?></small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($wo['asset_id']): ?>
                                    <a href="asset?id=<?= $wo['asset_id'] ?>"><?= htmlspecialchars($wo['asset_name']) ?></a>
                                    <?php if ($wo['asset_tag']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($wo['asset_tag']) ?></small>
                                    <?php endif; ?>
                                <?php elseif ($wo['device_type']): ?>
                                    <strong><?= htmlspecialchars($wo['device_type']) ?></strong>
                                    <?php if ($wo['device_make'] || $wo['device_model']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars(trim($wo['device_make'] . ' ' . $wo['device_model'])) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">General</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= getStatusColor($wo['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $wo['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= getPriorityColor($wo['priority']) ?>">
                                    <?= ucfirst($wo['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($wo['billable']): ?>
                                    <i class="fas fa-dollar-sign text-success" title="Billable"></i>
                                <?php else: ?>
                                    <i class="fas fa-handshake text-primary" title="Contract"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($wo['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editWorkOrder(<?= $wo['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="workorder?id=<?= $wo['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteWorkOrder(<?= $wo['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCustomerType() {
    const type = document.getElementById('customer_type').value;
    const companySection = document.getElementById('company_section');
    const individualSection = document.getElementById('individual_section');
    const companySelect = document.getElementById('company_id');
    const customerName = document.getElementById('customer_name');
    const deviceType = document.getElementById('device_type');
    
    if (type === 'company') {
        companySection.style.display = 'block';
        individualSection.style.display = 'none';
        companySelect.required = true;
        customerName.required = false;
        if (deviceType) deviceType.required = false;
    } else if (type === 'individual') {
        companySection.style.display = 'none';
        individualSection.style.display = 'block';
        companySelect.required = false;
        customerName.required = true;
        if (deviceType) deviceType.required = true;
    } else {
        companySection.style.display = 'none';
        individualSection.style.display = 'none';
        companySelect.required = false;
        customerName.required = false;
        if (deviceType) deviceType.required = false;
    }
}
function filterByCompany(companyId) {
    window.location.href = companyId ? `/SupporTracker/workorders?company_id=${companyId}` : '/SupporTracker/workorders';
}

function openWorkOrderModal() {
    const urlParams = new URLSearchParams(window.location.search);
    const companyId = urlParams.get('company_id');
    const assetId = urlParams.get('asset_id');
    let url = '/SupporTracker/workorders?action=add';
    if (companyId) url += `&company_id=${companyId}`;
    if (assetId) url += `&asset_id=${assetId}`;
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#workorderModal'));
            modal.show();
            setupWorkOrderForm();
        });
}

function editWorkOrder(id) {
    fetch(`/SupporTracker/workorders?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#workorderModal'));
            modal.show();
            setupWorkOrderForm();
        });
}

function setupWorkOrderForm() {
    const form = document.getElementById('workorderForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            
            const isEdit = document.querySelector('input[name="workorder_id"]');
            if (isEdit) {
                formData.append('update_workorder', '1');
            } else {
                formData.append('add_workorder', '1');
            }
            
            fetch('/SupporTracker/workorders', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);
                    if (data.success) {
                        console.log('Success - closing modal');
                        bootstrap.Modal.getInstance(document.querySelector('#workorderModal')).hide();
                        location.reload();
                    } else {
                        console.log('Error in response:', data.error);
                        alert('Error: ' + data.error);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.log('Response text:', text);
                    alert('Server error - check console');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                bootstrap.Modal.getInstance(document.querySelector('#workorderModal')).hide();
                location.reload();
            });
        });
        
        // Handle company change to load assets
        const companySelect = document.getElementById('company_id');
        if (companySelect) {
            companySelect.addEventListener('change', function() {
                loadAssetsForCompany(this.value);
            });
        }
    }
}

function loadAssetsForCompany(companyId) {
    const assetSelect = document.getElementById('asset_id');
    if (!assetSelect) return;
    
    // Clear current options
    assetSelect.innerHTML = '<option value="">Select Asset (Optional)</option>';
    
    if (!companyId) return;
    
    fetch(`/SupporTracker/assets?company_id=${companyId}&format=json`)
        .then(response => response.json())
        .then(assets => {
            assets.forEach(asset => {
                const option = document.createElement('option');
                option.value = asset.id;
                option.textContent = `${asset.name}${asset.asset_tag ? ' (' + asset.asset_tag + ')' : ''}`;
                assetSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading assets:', error));
}

// Auto-open modal if URL parameter is set
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_open') === '1') {
        openWorkOrderModal();
        // Clean up URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

</script>

<?php
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'new': return 'primary';
        case 'in_progress': return 'warning';
        case 'waiting_parts': return 'info';
        case 'waiting_customer': return 'secondary';
        case 'completed': return 'success';
        case 'closed': return 'dark';
        default: return 'secondary';
    }
}

function getPriorityColor($priority) {
    switch (strtolower($priority)) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'danger';
        default: return 'secondary';
    }
}
?>