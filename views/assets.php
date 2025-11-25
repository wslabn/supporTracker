<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Assets</h2>
            <button class="btn btn-success" onclick="openAssetModal()">
                <i class="fas fa-plus"></i> Add Asset
            </button>
        </div>

        <?php if ($company_filter): ?>
        <div class="alert alert-info">
            Showing assets for: <strong><?= htmlspecialchars($company_name) ?></strong>
            <a href="assets" class="btn btn-sm btn-outline-primary ms-2">Show All</a>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">Asset List</h6>
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
                            <th>Asset</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Location/Employee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td>
                                <a href="asset?id=<?= $asset['id'] ?>" class="fw-bold text-decoration-none">
                                    <?= htmlspecialchars($asset['name']) ?>
                                </a>
                                <?php if ($asset['asset_tag']): ?>
                                    <br><small class="text-muted">Tag: <?= htmlspecialchars($asset['asset_tag']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="company?id=<?= $asset['company_id'] ?>"><?= htmlspecialchars($asset['company_name']) ?></a>
                            </td>
                            <td><?= htmlspecialchars($asset['category_name'] ?? 'Computers') ?></td>
                            <td>
                                <?php if ($asset['location']): ?>
                                    <div><?= htmlspecialchars($asset['location']) ?></div>
                                <?php endif; ?>
                                <?php if ($asset['employee_name']): ?>
                                    <small class="text-muted">Assigned: <?= htmlspecialchars($asset['employee_name']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $asset['status'] == 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($asset['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editAsset(<?= $asset['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="asset?id=<?= $asset['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
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
function filterByCompany(companyId) {
    window.location.href = companyId ? `/SupporTracker/assets?company_id=${companyId}` : '/SupporTracker/assets';
}

function openAssetModal() {
    const urlParams = new URLSearchParams(window.location.search);
    const companyId = urlParams.get('company_id');
    const url = companyId ? `/SupporTracker/assets?action=add&company_id=${companyId}` : '/SupporTracker/assets?action=add';
    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#assetModal'));
            modal.show();
            setupAssetForm();
        });
}

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
            
            const isEdit = document.querySelector('input[name="asset_id"]');
            if (isEdit) {
                formData.append('update_asset', '1');
            } else {
                formData.append('add_asset', '1');
            }
            
            fetch(this.action, {
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
</script>