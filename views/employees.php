<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Employees - <?= htmlspecialchars($company['name']) ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/SupporTracker/companies">Companies</a></li>
                        <li class="breadcrumb-item"><a href="/SupporTracker/company?id=<?= $company['id'] ?>"><?= htmlspecialchars($company['name']) ?></a></li>
                        <li class="breadcrumb-item active">Employees</li>
                    </ol>
                </nav>
            </div>
            <button class="btn btn-success" onclick="openEmployeeModal()">
                <i class="fas fa-plus"></i> Add Employee
            </button>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Department/Position</th>
                            <th>Contact</th>
                            <th>Assets</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td>
                                <a href="/SupporTracker/employee?id=<?= $employee['id'] ?>" class="fw-bold text-decoration-none">
                                    <?= htmlspecialchars($employee['name']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($employee['department']): ?>
                                    <div><?= htmlspecialchars($employee['department']) ?></div>
                                <?php endif; ?>
                                <?php if ($employee['position']): ?>
                                    <small class="text-muted"><?= htmlspecialchars($employee['position']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($employee['email']): ?>
                                    <div><a href="mailto:<?= htmlspecialchars($employee['email']) ?>"><?= htmlspecialchars($employee['email']) ?></a></div>
                                <?php endif; ?>
                                <?php if ($employee['phone']): ?>
                                    <div><a href="tel:<?= htmlspecialchars($employee['phone']) ?>"><?= htmlspecialchars($employee['phone']) ?></a></div>
                                <?php endif; ?>
                            </td>
                            <td><?= $employee['asset_count'] ?> assets</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editEmployee(<?= $employee['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="/SupporTracker/employee?id=<?= $employee['id'] ?>" class="btn btn-sm btn-info">
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
function openEmployeeModal() {
    fetch('/SupporTracker/employees?action=add&company_id=<?= $company_id ?>')
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#employeeModal'));
            modal.show();
            setupEmployeeForm();
        });
}

function editEmployee(id) {
    fetch(`/SupporTracker/employees?action=edit&id=${id}&company_id=<?= $company_id ?>`)
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
            
            const isEdit = document.querySelector('input[name="employee_id"]');
            if (isEdit) {
                formData.append('update_employee', '1');
            } else {
                formData.append('add_employee', '1');
            }
            
            fetch('/SupporTracker/employees', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.querySelector('#employeeModal')).hide();
                        location.reload();
                    } else {
                        console.log('ERROR:', data.error);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.log('Raw response:', text);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        });
    }
}
</script>