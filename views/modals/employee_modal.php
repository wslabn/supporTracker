<div class="modal fade" id="employeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= isset($employee) ? 'Edit Employee' : 'Add Employee' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="employeeForm" method="POST" action="/SupporTracker/employees">
                <div class="modal-body">
                    <?php if (isset($employee)): ?>
                        <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="ajax" value="1">
                    <input type="hidden" name="company_id" value="<?= $company_id ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Employee Name *</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($employee['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($employee['email'] ?? '') ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Office Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($employee['phone'] ?? ($company['phone'] ?? '')) ?>" placeholder="<?= htmlspecialchars($company['phone'] ?? 'Office number') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cell Phone</label>
                                <input type="text" class="form-control" name="cell_phone" value="<?= htmlspecialchars($employee['cell_phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Department</label>
                                <input type="text" class="form-control" name="department" value="<?= htmlspecialchars($employee['department'] ?? '') ?>" placeholder="e.g., Accounting, Sales">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <input type="text" class="form-control" name="position" value="<?= htmlspecialchars($employee['title'] ?? '') ?>" placeholder="e.g., Manager, Receptionist">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?= (isset($employee) && $employee['status'] == 'active') || !isset($employee) ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= (isset($employee) && $employee['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="<?= isset($employee) ? 'update_employee' : 'add_employee' ?>" class="btn btn-primary">
                        <?= isset($employee) ? 'Update' : 'Add' ?> Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>