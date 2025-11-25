<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Companies</h2>
            <button class="btn btn-success" onclick="openCompanyModal()">
                <i class="fas fa-plus"></i> Add Company
            </button>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Monthly Rate</th>
                            <th>Contact</th>
                            <th>Employees</th>
                            <th>Assets</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                        <tr>
                            <td>
                                <a href="/SupporTracker/company?id=<?= $company['id'] ?>" class="fw-bold text-decoration-none">
                                    <?= htmlspecialchars($company['name']) ?>
                                </a>
                                <?php if ($company['status'] != 'active'): ?>
                                    <span class="badge bg-secondary ms-2"><?= ucfirst($company['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= formatCurrency($company['monthly_rate']) ?></td>
                            <td>
                                <?php if ($company['email']): ?>
                                    <div><?= htmlspecialchars($company['email']) ?></div>
                                <?php endif; ?>
                                <?php if ($company['phone']): ?>
                                    <div><?= htmlspecialchars($company['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= $company['employee_count'] ?></td>
                            <td><?= $company['asset_count'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editCompany(<?= $company['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="/SupporTracker/company?id=<?= $company['id'] ?>" class="btn btn-sm btn-info">
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
function openCompanyModal() {
    fetch('/SupporTracker/companies?action=add')
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#companyModal'));
            modal.show();
            setupCompanyForm();
        });
}

function editCompany(id) {
    fetch(`/SupporTracker/companies?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#companyModal'));
            modal.show();
            setupCompanyForm();
        });
}

function setupCompanyForm() {
    const form = document.getElementById('companyForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            
            // Check if this is edit or add mode
            const isEdit = document.querySelector('input[name="company_id"]');
            if (isEdit) {
                formData.append('update_company', '1');
            } else {
                formData.append('add_company', '1');
            }
            
            fetch('/SupporTracker/companies', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response.status, response.statusText);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.querySelector('#companyModal')).hide();
                        location.reload();
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    bootstrap.Modal.getInstance(document.querySelector('#companyModal')).hide();
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                bootstrap.Modal.getInstance(document.querySelector('#companyModal')).hide();
                location.reload();
            });
        });
    } else {
        console.error('Company form not found!');
    }
}
</script>