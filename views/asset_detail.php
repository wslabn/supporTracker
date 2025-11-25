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
                                    <th>Username</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($credentials as $cred): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cred['type']) ?></td>
                                    <td><?= htmlspecialchars($cred['username']) ?></td>
                                    <td><?= htmlspecialchars($cred['description']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editCredential(<?= $cred['id'] ?>)">
                                            <i class="fas fa-edit"></i>
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
    fetch(`/SupporTracker/credentials?action=add&asset_id=${assetId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            new bootstrap.Modal(document.querySelector('#credentialModal')).show();
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
    alert('Work order functionality coming soon');
}

function editWorkOrder(id) {
    alert('Work order functionality coming soon');
}
</script>