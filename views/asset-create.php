<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-laptop me-2"></i>Add New Asset</h5>
                <?php if ($customer): ?>
                <small class="text-muted">For customer: <strong><?= htmlspecialchars($customer['name']) ?></strong></small>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="create_asset" value="1">
                    <input type="hidden" name="customer_id" value="<?= $customer['id'] ?? '' ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Asset Name *</label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g., John's Dell Laptop">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Make/Brand</label>
                                <input type="text" class="form-control" name="vendor" placeholder="e.g., Dell, HP, Apple">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" placeholder="e.g., Inspiron 15 3000">
                            </div>
                        </div>

                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial Number</label>
                                <input type="text" class="form-control" name="serial_number" placeholder="e.g., ABC123456789">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Version/OS</label>
                                <input type="text" class="form-control" name="version" placeholder="e.g., Windows 11, macOS 14">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" placeholder="e.g., Customer Office, Main Desk">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="retired">Retired</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/SupporTracker/customers" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Customers
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Create Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>