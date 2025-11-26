<div class="modal fade" id="workorderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= isset($workorder) ? 'Edit Work Order' : 'Create Work Order' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="workorderForm" method="POST" action="/SupporTracker/workorders">
                <div class="modal-body">
                    <?php if (isset($workorder)): ?>
                        <input type="hidden" name="workorder_id" value="<?= $workorder['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer *</label>
                                <select class="form-select" id="customer_id" name="customer_id" required onchange="loadAssets()">
                                    <option value="">Select Customer</option>
                                    <optgroup label="Business Customers">
                                        <?php foreach ($customers as $customer): ?>
                                            <?php if (($customer['customer_type'] ?? 'business') === 'business'): ?>
                                                <option value="<?= $customer['id'] ?>" <?= (isset($workorder) && $workorder['customer_id'] == $customer['id']) || (!isset($workorder) && isset($selected_customer_id) && $selected_customer_id == $customer['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($customer['name']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Individual Customers">
                                        <?php foreach ($customers as $customer): ?>
                                            <?php if (($customer['customer_type'] ?? 'business') === 'individual'): ?>
                                                <option value="<?= $customer['id'] ?>" <?= (isset($workorder) && $workorder['customer_id'] == $customer['id']) || (!isset($workorder) && isset($selected_customer_id) && $selected_customer_id == $customer['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($customer['name']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <small class="text-muted">Don't see the customer? <a href="/SupporTracker/customers">Add new customer</a></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Asset/Device</label>
                                <select class="form-select" id="asset_id" name="asset_id">
                                    <option value="">Select Asset/Device (Optional)</option>
                                    <?php foreach ($assets as $asset): ?>
                                        <option value="<?= $asset['id'] ?>" <?= (isset($workorder) && $workorder['asset_id'] == $asset['id']) || (!isset($workorder) && isset($selected_asset_id) && $selected_asset_id == $asset['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($asset['name']) ?><?= $asset['asset_tag'] ? ' (' . htmlspecialchars($asset['asset_tag']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Assets/devices can be added via customer profile</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($workorder['title'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($workorder['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <?php if (isset($workorder)): ?>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="new" <?= (isset($workorder) && $workorder['status'] == 'new') ? 'selected' : '' ?>>New</option>
                                    <option value="in_progress" <?= (isset($workorder) && $workorder['status'] == 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                                    <option value="waiting_parts" <?= (isset($workorder) && $workorder['status'] == 'waiting_parts') ? 'selected' : '' ?>>Waiting Parts</option>
                                    <option value="waiting_customer" <?= (isset($workorder) && $workorder['status'] == 'waiting_customer') ? 'selected' : '' ?>>Waiting Customer</option>
                                    <option value="completed" <?= (isset($workorder) && $workorder['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="closed" <?= (isset($workorder) && $workorder['status'] == 'closed') ? 'selected' : '' ?>>Closed</option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="low" <?= (isset($workorder) && $workorder['priority'] == 'low') ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= (isset($workorder) && $workorder['priority'] == 'medium') || !isset($workorder) ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= (isset($workorder) && $workorder['priority'] == 'high') ? 'selected' : '' ?>>High</option>
                                    <option value="urgent" <?= (isset($workorder) && $workorder['priority'] == 'urgent') ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" name="estimated_hours" value="<?= htmlspecialchars($workorder['estimated_hours'] ?? '') ?>" step="0.25" min="0">
                            </div>
                        </div>
                        <?php if (isset($workorder)): ?>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Actual Hours</label>
                                <input type="number" class="form-control" name="actual_hours" value="<?= htmlspecialchars($workorder['actual_hours'] ?? '') ?>" step="0.25" min="0">
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hourly Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="hourly_rate" value="<?= htmlspecialchars($workorder['hourly_rate'] ?? DEFAULT_HOURLY_RATE) ?>" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="billable" id="billable" <?= (isset($workorder) && $workorder['billable']) || !isset($workorder) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="billable">
                                        Billable Work Order
                                    </label>
                                    <div class="form-text">Uncheck if covered by monthly contract</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="<?= isset($workorder) ? 'update_workorder' : 'add_workorder' ?>" class="btn btn-primary">
                        <?= isset($workorder) ? 'Update' : 'Create' ?> Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>