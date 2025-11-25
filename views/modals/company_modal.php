<div class="modal fade" id="companyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= isset($company) ? 'Edit Company' : 'Add Company' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="companyForm" method="POST" action="/SupporTracker/companies">
                <div class="modal-body">
                    <?php if (isset($company)): ?>
                        <input type="hidden" name="company_id" value="<?= $company['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Company Name *</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($company['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($company['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($company['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Monthly Rate</label>
                        <input type="number" class="form-control" name="monthly_rate" step="0.01" value="<?= $company['monthly_rate'] ?? '125.00' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3"><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="<?= isset($company) ? 'update_company' : 'add_company' ?>" class="btn btn-primary">
                        <?= isset($company) ? 'Update' : 'Add' ?> Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>