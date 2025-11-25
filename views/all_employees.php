<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>All Employees</h2>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
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
                                <a href="/SupporTracker/company?id=<?= $employee['company_id'] ?>"><?= htmlspecialchars($employee['company_name']) ?></a>
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
                                <a href="/SupporTracker/employee?id=<?= $employee['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/SupporTracker/employees?company_id=<?= $employee['company_id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-building"></i>
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