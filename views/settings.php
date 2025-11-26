<?php if (isset($success)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    <?= $success ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Initial System Setup</h5>
                <small class="text-muted">Configure your company information and default settings</small>
            </div>
            <div class="card-body">
                <?php if ($current_settings['company_name'] === 'Your MSP Company'): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Welcome to SupportTracker!</strong> Please configure your company settings below to get started.
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6>Business Settings</h6>
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($current_settings['company_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Company Logo URL</label>
                                <input type="url" name="company_logo_url" class="form-control" value="<?= htmlspecialchars($current_settings['company_logo_url']) ?>" placeholder="https://example.com/logo.png">
                                <div class="form-text">Enter a URL to your company logo image</div>
                                <?php if ($current_settings['company_logo_url']): ?>
                                <div class="mt-2">
                                    <img src="<?= htmlspecialchars($current_settings['company_logo_url']) ?>" alt="Company Logo" style="max-height: 60px; max-width: 200px;" class="border rounded">
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Default Hourly Rate ($)</label>
                                <input type="number" name="default_hourly_rate" class="form-control" value="<?= $current_settings['default_hourly_rate'] ?>" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Default Tax Rate (%)</label>
                                <input type="number" name="default_tax_rate" class="form-control" value="<?= $current_settings['default_tax_rate'] ?>" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <h6>Invoice Settings</h6>
                            <div class="mb-3">
                                <label class="form-label">Invoice Due Days</label>
                                <input type="number" name="invoice_due_days" class="form-control" value="<?= $current_settings['invoice_due_days'] ?>" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Next Invoice Number</label>
                                <input type="text" class="form-control" value="INV-000001" readonly>
                                <div class="form-text">Auto-generated based on database records</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Next Ticket Number</label>
                                <input type="text" class="form-control" value="TKT000001" readonly>
                                <div class="form-text">Auto-generated based on database records</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" name="save_settings" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-1"></i>
                            Save Settings & Continue
                        </button>
                        
                        <?php if ($current_settings['company_name'] !== 'Your MSP Company'): ?>
                        <div class="text-muted">
                            <small>Next: <a href="/SupporTracker/locations" class="text-decoration-none">Set up your locations</a></small>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>