<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Parts & Equipment</h2>
            <button class="btn btn-success" onclick="openPartModal()">
                <i class="fas fa-plus"></i> Order Parts
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">Parts Orders</h6>
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
                            <th>Description</th>
                            <th>Company</th>
                            <th>Project/Work Order</th>
                            <th>Quantity</th>
                            <th>Cost</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parts as $part): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($part['description']) ?></strong>
                                <?php if ($part['part_number']): ?>
                                    <br><small class="text-muted">P/N: <?= htmlspecialchars($part['part_number']) ?></small>
                                <?php endif; ?>
                                <?php if ($part['vendor']): ?>
                                    <br><small class="text-muted">Vendor: <?= htmlspecialchars($part['vendor']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="company?id=<?= $part['company_id'] ?>"><?= htmlspecialchars($part['company_name']) ?></a>
                            </td>
                            <td>
                                <?php if ($part['project_name']): ?>
                                    <a href="project?id=<?= $part['project_id'] ?>"><?= htmlspecialchars($part['project_name']) ?></a>
                                <?php elseif ($part['workorder_title']): ?>
                                    <a href="workorder?id=<?= $part['work_order_id'] ?>">#<?= $part['work_order_id'] ?> - <?= htmlspecialchars($part['workorder_title']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">General</span>
                                <?php endif; ?>
                                <?php if ($part['asset_name']): ?>
                                    <br><small class="text-muted">Asset: <?= htmlspecialchars($part['asset_name']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= $part['quantity'] ?></td>
                            <td>
                                $<?= number_format($part['total_cost'], 2) ?>
                                <br><small class="text-muted">@$<?= number_format($part['unit_cost'], 2) ?></small>
                            </td>
                            <td>
                                <?php if ($part['billable']): ?>
                                    $<?= number_format($part['total_price'], 2) ?>
                                    <br><small class="text-muted">@$<?= number_format($part['unit_price'], 2) ?></small>
                                    <?php if ($part['markup_percent'] > 0): ?>
                                        <br><small class="text-success">+<?= $part['markup_percent'] ?>%</small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Not billable</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= getPartStatusColor($part['status']) ?>">
                                    <?= ucfirst($part['status']) ?>
                                </span>
                                <?php if ($part['order_date']): ?>
                                    <br><small class="text-muted">Ordered: <?= date('M j', strtotime($part['order_date'])) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editPart(<?= $part['id'] ?>)">
                                    <i class="fas fa-edit"></i>
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
function filterByCompany(companyId) {
    window.location.href = companyId ? `/SupporTracker/parts?company_id=${companyId}` : '/SupporTracker/parts';
}

function openPartModal() {
    const urlParams = new URLSearchParams(window.location.search);
    const companyId = urlParams.get('company_id');
    const projectId = urlParams.get('project_id');
    const workorderId = urlParams.get('workorder_id');
    
    let url = '/SupporTracker/parts?action=add';
    if (companyId) url += `&company_id=${companyId}`;
    if (projectId) url += `&project_id=${projectId}`;
    if (workorderId) url += `&workorder_id=${workorderId}`;
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#partModal'));
            modal.show();
            setupPartForm();
        });
}

function editPart(id) {
    fetch(`/SupporTracker/parts?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#partModal'));
            modal.show();
            setupPartForm();
        });
}

function setupPartForm() {
    const form = document.getElementById('partForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            
            const isEdit = document.querySelector('input[name="part_id"]');
            if (isEdit) {
                formData.append('update_part', '1');
            } else {
                formData.append('add_part', '1');
            }
            
            fetch('/SupporTracker/parts', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.querySelector('#partModal')).hide();
                    location.reload();
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.querySelector('#partModal')).hide();
                location.reload();
            });
        });
    }
}
</script>