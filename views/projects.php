<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Projects</h2>
            <button class="btn btn-success" onclick="openProjectModal()">
                <i class="fas fa-plus"></i> Create Project
            </button>
        </div>

        <?php if ($company_filter): ?>
        <div class="alert alert-info">
            Showing projects for: <strong><?= htmlspecialchars($company_name) ?></strong>
            <a href="projects" class="btn btn-sm btn-outline-primary ms-2">Show All</a>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">Project List</h6>
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
                            <th>Project</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Work Orders</th>
                            <th>Budget</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <a href="project?id=<?= $project['id'] ?>" class="fw-bold text-decoration-none">
                                    <?= htmlspecialchars($project['name']) ?>
                                </a>
                                <?php if ($project['description']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(substr($project['description'], 0, 50)) ?><?= strlen($project['description']) > 50 ? '...' : '' ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="company?id=<?= $project['company_id'] ?>"><?= htmlspecialchars($project['company_name']) ?></a>
                            </td>
                            <td>
                                <span class="badge bg-<?= getProjectStatusColor($project['status']) ?>">
                                    <?= ucfirst($project['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= getProjectPriorityColor($project['priority']) ?>">
                                    <?= ucfirst($project['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="workorders?project_id=<?= $project['id'] ?>" class="text-decoration-none">
                                    <?= $project['work_order_count'] ?> work orders
                                </a>
                            </td>
                            <td>
                                <?php if ($project['budget'] > 0): ?>
                                    $<?= number_format($project['budget'], 2) ?>
                                    <br><small class="text-muted">Spent: $<?= number_format($project['total_parts_cost'], 2) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">No budget set</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($project['estimated_hours'] > 0): ?>
                                    <?= $project['total_hours'] ?> / <?= $project['estimated_hours'] ?> hrs
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" style="width: <?= min(100, ($project['total_hours'] / $project['estimated_hours']) * 100) ?>%"></div>
                                    </div>
                                <?php else: ?>
                                    <?= $project['total_hours'] ?> hrs
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editProject(<?= $project['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="project?id=<?= $project['id'] ?>" class="btn btn-sm btn-info">
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
    window.location.href = companyId ? `/SupporTracker/projects?company_id=${companyId}` : '/SupporTracker/projects';
}

function openProjectModal() {
    const urlParams = new URLSearchParams(window.location.search);
    const companyId = urlParams.get('company_id');
    const url = companyId ? `/SupporTracker/projects?action=add&company_id=${companyId}` : '/SupporTracker/projects?action=add';
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#projectModal'));
            modal.show();
            setupProjectForm();
        });
}

function editProject(id) {
    fetch(`/SupporTracker/projects?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#projectModal'));
            modal.show();
            setupProjectForm();
        });
}

function setupProjectForm() {
    const form = document.getElementById('projectForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            
            const isEdit = document.querySelector('input[name="project_id"]');
            if (isEdit) {
                formData.append('update_project', '1');
            } else {
                formData.append('add_project', '1');
            }
            
            fetch('/SupporTracker/projects', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.querySelector('#projectModal')).hide();
                    location.reload();
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.querySelector('#projectModal')).hide();
                location.reload();
            });
        });
    }
}
</script>