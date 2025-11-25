<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-user"></i> Employee Details</h5>
                <button class="btn btn-sm btn-primary" onclick="editEmployee(<?= $employee['id'] ?>)">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td><?= htmlspecialchars($employee['name']) ?></td></tr>
                    <tr><td><strong>Company:</strong></td><td>
                        <a href="/SupporTracker/company?id=<?= $employee['company_id'] ?>"><?= htmlspecialchars($employee['company_name']) ?></a>
                    </td></tr>
                    <tr><td><strong>Email:</strong></td><td>
                        <?php if ($employee['email']): ?>
                            <a href="mailto:<?= htmlspecialchars($employee['email']) ?>"><?= htmlspecialchars($employee['email']) ?></a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </td></tr>
                    <tr><td><strong>Office Phone:</strong></td><td>
                        <?php if ($employee['phone']): ?>
                            <a href="tel:<?= htmlspecialchars($employee['phone']) ?>"><?= htmlspecialchars($employee['phone']) ?></a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </td></tr>
                    <?php if (isset($employee['cell_phone'])): ?>
                    <tr><td><strong>Cell Phone:</strong></td><td>
                        <?php if ($employee['cell_phone']): ?>
                            <a href="tel:<?= htmlspecialchars($employee['cell_phone']) ?>"><?= htmlspecialchars($employee['cell_phone']) ?></a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </td></tr>
                    <?php endif; ?>
                    <tr><td><strong>Department:</strong></td><td><?= htmlspecialchars($employee['department'] ?? 'Not specified') ?></td></tr>
                    <tr><td><strong>Position:</strong></td><td><?= htmlspecialchars($employee['title'] ?? 'Not specified') ?></td></tr>
                    <tr><td><strong>Status:</strong></td><td>
                        <span class="badge bg-<?= $employee['status'] == 'active' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($employee['status']) ?>
                        </span>
                    </td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <ul class="nav nav-tabs" id="employeeTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#assets">
                    <i class="fas fa-desktop"></i> Assets (<?= count($assets) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#credentials">
                    <i class="fas fa-key"></i> Credentials (<?= count($credentials) ?>)
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="assets">
                <div class="card">
                    <div class="card-header">
                        <h6>Assigned Assets</h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($assets): ?>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Tag</th>
                                    <th>Category</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assets as $asset): ?>
                                <tr>
                                    <td><a href="/SupporTracker/asset?id=<?= $asset['id'] ?>"><?= htmlspecialchars($asset['name']) ?></a></td>
                                    <td><?= htmlspecialchars($asset['asset_tag']) ?></td>
                                    <td><?= htmlspecialchars($asset['category_name'] ?? 'Computers') ?></td>
                                    <td><?= htmlspecialchars($asset['location']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusColor($asset['status']) ?>">
                                            <?= ucfirst($asset['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/SupporTracker/asset?id=<?= $asset['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-muted p-3">No assets assigned to this employee.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="credentials">
                <div class="card">
                    <div class="card-header">
                        <h6>Asset Credentials</h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($credentials): ?>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th>Type</th>
                                    <th>Username</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($credentials as $cred): ?>
                                <tr>
                                    <td><a href="/SupporTracker/asset?id=<?= $cred['asset_id'] ?>"><?= htmlspecialchars($cred['asset_name']) ?></a></td>
                                    <td><?= htmlspecialchars($cred['credential_type']) ?></td>
                                    <td><?= htmlspecialchars($cred['username']) ?></td>
                                    <td>
                                        <a href="/SupporTracker/asset?id=<?= $cred['asset_id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-muted p-3">No credentials found for this employee's assets.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editEmployee(id) {
    fetch(`/SupporTracker/employees?action=edit&id=${id}&company_id=<?= $employee['company_id'] ?>`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#employeeModal'));
            modal.show();
            setupEmployeeForm();
        });
}

function setupEmployeeForm() {
    const form = document.getElementById('employeeForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('update_employee', '1');
            
            fetch('/SupporTracker/employees', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.querySelector('#employeeModal')).hide();
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                bootstrap.Modal.getInstance(document.querySelector('#employeeModal')).hide();
                location.reload();
            });
        });
    }
}
</script>