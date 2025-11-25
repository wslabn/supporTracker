<div class="modal fade" id="assetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= isset($asset) ? 'Edit Asset' : 'Add Asset' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assetForm" method="POST" action="/SupporTracker/assets">
                <div class="modal-body">
                    <?php if (isset($asset)): ?>
                        <input type="hidden" name="asset_id" value="<?= $asset['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company *</label>
                                <select class="form-select" name="company_id" required>
                                    <option value="">Select Company</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?= $company['id'] ?>" <?= (isset($asset) && $asset['company_id'] == $company['id']) || (!isset($asset) && isset($selected_company_id) && $selected_company_id == $company['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($company['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Asset Tag</label>
                                <input type="text" class="form-control" name="asset_tag" value="<?= htmlspecialchars($asset['asset_tag'] ?? '') ?>" placeholder="Auto-generated if blank">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Asset Name *</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($asset['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id">
                                    <option value="1" <?= (isset($asset) && $asset['category_id'] == 1) ? 'selected' : '' ?>>Computers</option>
                                    <option value="2" <?= (isset($asset) && $asset['category_id'] == 2) ? 'selected' : '' ?>>Servers</option>
                                    <option value="3" <?= (isset($asset) && $asset['category_id'] == 3) ? 'selected' : '' ?>>Printers</option>
                                    <option value="4" <?= (isset($asset) && $asset['category_id'] == 4) ? 'selected' : '' ?>>Network Equipment</option>
                                    <option value="5" <?= (isset($asset) && $asset['category_id'] == 5) ? 'selected' : '' ?>>Phones</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Make</label>
                                <input type="text" class="form-control" name="make" value="<?= htmlspecialchars($asset['make'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" value="<?= htmlspecialchars($asset['model'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial Number</label>
                                <input type="text" class="form-control" name="serial_number" value="<?= htmlspecialchars($asset['serial_number'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($asset['location'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?= (isset($asset) && $asset['status'] == 'active') || !isset($asset) ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= (isset($asset) && $asset['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                    <option value="repair" <?= (isset($asset) && $asset['status'] == 'repair') ? 'selected' : '' ?>>Repair</option>
                                    <option value="retired" <?= (isset($asset) && $asset['status'] == 'retired') ? 'selected' : '' ?>>Retired</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assigned Employee</label>
                        <select class="form-select" name="employee_id">
                            <option value="">Unassigned</option>
                            <?php 
                            // Get employees for the selected company
                            $company_id = $asset['company_id'] ?? $selected_company_id ?? null;
                            if ($company_id) {
                                $emp_stmt = $pdo->prepare("SELECT id, name FROM employees WHERE company_id = ? AND status = 'active' ORDER BY name");
                                $emp_stmt->execute([$company_id]);
                                $employees = $emp_stmt->fetchAll();
                                foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>" <?= (isset($asset) && $asset['employee_id'] == $emp['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($emp['name']) ?>
                                    </option>
                                <?php endforeach;
                            } ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"><?= htmlspecialchars($asset['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="<?= isset($asset) ? 'update_asset' : 'add_asset' ?>" class="btn btn-primary">
                        <?= isset($asset) ? 'Update' : 'Add' ?> Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>