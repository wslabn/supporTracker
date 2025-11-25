<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><?= htmlspecialchars($company['name']) ?></h1>
                <p class="text-muted">Monthly Rate: <?= formatCurrency($company['monthly_rate']) ?></p>
            </div>
            <button class="btn btn-primary" onclick="editCompany(<?= $company['id'] ?>)">
                <i class="fas fa-edit"></i> Edit Company
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $stats['employees'] ?></h3>
                        <p class="card-text">Employees</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success"><?= $stats['assets'] ?></h3>
                        <p class="card-text">Assets</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Company Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($company['email']): ?>
                            <p><strong>Email:</strong> <?= htmlspecialchars($company['email']) ?></p>
                        <?php endif; ?>
                        <?php if ($company['phone']): ?>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($company['phone']) ?></p>
                        <?php endif; ?>
                        <?php if ($company['address']): ?>
                            <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($company['address'])) ?></p>
                        <?php endif; ?>
                        <?php if ($company['notes']): ?>
                            <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($company['notes'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="/SupporTracker/assets?company_id=<?= $company['id'] ?>" class="btn btn-success mb-2 w-100">
                            <i class="fas fa-desktop"></i> View Assets
                        </a>
                        <a href="/SupporTracker/employees?company_id=<?= $company['id'] ?>" class="btn btn-info mb-2 w-100">
                            <i class="fas fa-users"></i> View Employees
                        </a>
                        <button class="btn btn-warning mb-2 w-100" onclick="alert('Coming soon')">
                            <i class="fas fa-wrench"></i> Work Orders
                        </button>
                        <button class="btn btn-primary w-100" onclick="alert('Coming soon')">
                            <i class="fas fa-file-invoice"></i> Invoices
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editCompany(id) {
    fetch(`/SupporTracker/companies?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            new bootstrap.Modal(document.querySelector('#companyModal')).show();
        });
}
</script>