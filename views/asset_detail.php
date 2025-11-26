<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-desktop"></i> Asset Details</h5>
                <button class="btn btn-sm btn-primary" onclick="editAsset(<?= $asset['id'] ?>)">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td><?= htmlspecialchars($asset['name']) ?></td></tr>
                    <tr><td><strong>Tag:</strong></td><td><?= htmlspecialchars($asset['asset_tag']) ?></td></tr>
                    <tr><td><strong>Category:</strong></td><td><?= htmlspecialchars($asset['category_name'] ?? 'Computers') ?></td></tr>
                    <tr><td><strong>Company:</strong></td><td>
                        <a href="/SupporTracker/company?id=<?= $asset['company_id'] ?>"><?= htmlspecialchars($asset['company_name']) ?></a>
                    </td></tr>
                    <tr><td><strong>Employee:</strong></td><td><?= htmlspecialchars($asset['employee_name'] ?? 'Unassigned') ?></td></tr>
                    <tr><td><strong>Location:</strong></td><td><?= htmlspecialchars($asset['location']) ?></td></tr>
                    <tr><td><strong>Serial:</strong></td><td><?= htmlspecialchars($asset['serial_number']) ?></td></tr>
                    <tr><td><strong>Model:</strong></td><td><?= htmlspecialchars($asset['model']) ?></td></tr>
                    <tr><td><strong>Status:</strong></td><td>
                        <span class="badge bg-<?= $asset['status'] == 'Active' ? 'success' : 'secondary' ?>">
                            <?= htmlspecialchars($asset['status']) ?>
                        </span>
                    </td></tr>
                    <?php if (!empty($asset['device_password'])): ?>
                    <tr><td><strong>Device Password:</strong></td><td>
                        <span class="text-success"><i class="bi bi-shield-check me-1"></i>Available</span>
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="showPassword(this, '<?= htmlspecialchars($asset['device_password']) ?>')">
                            <i class="bi bi-eye"></i> Show
                        </button>
                    </td></tr>
                    <?php endif; ?>
                </table>
                <?php if ($asset['notes']): ?>
                <div class="mt-3">
                    <strong>Notes:</strong>
                    <div class="border p-2 mt-1"><?= nl2br(htmlspecialchars($asset['notes'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <ul class="nav nav-tabs" id="assetTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#credentials">
                    <i class="fas fa-key"></i> Credentials (<?= count($credentials) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#workorders">
                    <i class="fas fa-wrench"></i> Work Orders (<?= count($work_orders) ?>)
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="credentials">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6>Credentials</h6>
                        <button class="btn btn-sm btn-success" onclick="addCredential(<?= $asset_id ?>)">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($credentials): ?>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Service</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($credentials as $cred): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= ucfirst($cred['credential_type']) ?></span></td>
                                    <td><?= htmlspecialchars($cred['service_name']) ?></td>
                                    <td><?= htmlspecialchars($cred['username']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="showCredentialPassword(this, '<?= htmlspecialchars($cred['password']) ?>')">
                                            <i class="bi bi-eye"></i> Show
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="deleteCredential(<?= $cred['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-muted p-3">No credentials found for this asset.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="workorders">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6>Work Orders</h6>
                        <button class="btn btn-sm btn-success" onclick="addWorkOrder(<?= $asset_id ?>)">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($work_orders): ?>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($work_orders as $wo): ?>
                                <tr>
                                    <td>#<?= $wo['id'] ?></td>
                                    <td><?= htmlspecialchars($wo['title']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusColor($wo['status']) ?>">
                                            <?= htmlspecialchars($wo['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getPriorityColor($wo['priority']) ?>">
                                            <?= htmlspecialchars($wo['priority']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($wo['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editWorkOrder(<?= $wo['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-muted p-3">No work orders found for this asset.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="modalContainer"></div>

<script>
function editAsset(id) {
    fetch(`/SupporTracker/assets?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#assetModal'));
            modal.show();
            setupAssetForm();
        });
}

function setupAssetForm() {
    const form = document.getElementById('assetForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('update_asset', '1');
            
            fetch('/SupporTracker/assets', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.querySelector('#assetModal')).hide();
                        location.reload();
                    }
                } catch (e) {
                    bootstrap.Modal.getInstance(document.querySelector('#assetModal')).hide();
                    location.reload();
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.querySelector('#assetModal')).hide();
                location.reload();
            });
        });
    }
}

function addCredential(assetId) {
    const modal = `
        <div class="modal fade" id="credentialModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Credential</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="credentialForm">
                        <div class="modal-body">
                            <input type="hidden" name="asset_id" value="${assetId}">
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="credential_type" required>
                                    <option value="device">Device Login</option>
                                    <option value="email">Email Account</option>
                                    <option value="software">Software/App</option>
                                    <option value="network">WiFi/Network</option>
                                    <option value="cloud">Cloud Service</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Service Name</label>
                                <input type="text" class="form-control" name="service_name" required placeholder="e.g., Gmail, Windows Login, WiFi">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" placeholder="Optional">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Credential</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    document.getElementById('modalContainer').innerHTML = modal;
    const modalEl = new bootstrap.Modal(document.querySelector('#credentialModal'));
    modalEl.show();
    
    document.getElementById('credentialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('add_credential', '1');
        
        fetch('/SupporTracker/credentials', {
            method: 'POST',
            body: formData
        })
        .then(() => {
            modalEl.hide();
            location.reload();
        });
    });
}

function editCredential(id) {
    fetch(`/SupporTracker/credentials?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            new bootstrap.Modal(document.querySelector('#credentialModal')).show();
        });
}

function addWorkOrder(assetId) {
    fetch(`/SupporTracker/workorders?action=add&asset_id=${assetId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#workorderModal'));
            modal.show();
            setupWorkOrderForm();
        });
}

function editWorkOrder(id) {
    window.location.href = `/SupporTracker/workorder?id=${id}`;
}

function setupWorkOrderForm() {
    const form = document.getElementById('workorderForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('add_workorder', '1');
            
            fetch('/SupporTracker/workorders', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.querySelector('#workorderModal')).hide();
                    location.reload();
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.querySelector('#workorderModal')).hide();
                location.reload();
            });
        });
    }
}

function showPassword(button, password) {
    const isShowing = button.innerHTML.includes('Hide');
    if (isShowing) {
        button.innerHTML = '<i class="bi bi-eye"></i> Show';
        button.previousElementSibling.innerHTML = '<i class="bi bi-shield-check me-1"></i>Available';
    } else {
        button.innerHTML = '<i class="bi bi-eye-slash"></i> Hide';
        button.previousElementSibling.innerHTML = '<code>' + password + '</code>';
    }
}

function showCredentialPassword(button, password) {
    const isShowing = button.innerHTML.includes('Hide');
    if (isShowing) {
        button.innerHTML = '<i class="bi bi-eye"></i> Show';
    } else {
        button.innerHTML = '<i class="bi bi-eye-slash"></i> Hide';
        button.setAttribute('title', password);
        alert('Password: ' + password);
        setTimeout(() => {
            button.innerHTML = '<i class="bi bi-eye"></i> Show';
        }, 3000);
    }
}

function deleteCredential(id) {
    if (confirm('Delete this credential?')) {
        fetch('/SupporTracker/credentials?action=delete&id=' + id, {method: 'POST'})
        .then(() => location.reload());
    }
}
</script>