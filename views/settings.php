<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    <?= $success ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <?= $error ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'company' ? 'active' : '' ?>" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">
                            <i class="bi bi-building me-1"></i>Company
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'locations' ? 'active' : '' ?>" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations" type="button" role="tab">
                            <i class="bi bi-geo-alt me-1"></i>Locations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'users' ? 'active' : '' ?>" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                            <i class="bi bi-people me-1"></i>Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'asset-categories' ? 'active' : '' ?>" id="asset-categories-tab" data-bs-toggle="tab" data-bs-target="#asset-categories" type="button" role="tab">
                            <i class="bi bi-laptop me-1"></i>Asset Categories
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'service-categories' ? 'active' : '' ?>" id="service-categories-tab" data-bs-toggle="tab" data-bs-target="#service-categories" type="button" role="tab">
                            <i class="bi bi-tools me-1"></i>Service Categories
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'service-pricing' ? 'active' : '' ?>" id="service-pricing-tab" data-bs-toggle="tab" data-bs-target="#service-pricing" type="button" role="tab">
                            <i class="bi bi-currency-dollar me-1"></i>Service Pricing
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="settingsTabContent">
                    <!-- Company Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'company' ? 'show active' : '' ?>" id="company" role="tabpanel">
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
                                <label class="form-label">Default Tax Rate (%)</label>
                                <input type="number" name="default_tax_rate" class="form-control" value="<?= $current_settings['default_tax_rate'] ?>" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <h6>Customer Portal</h6>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="portal_enabled" id="portalEnabled" <?= $current_settings['portal_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="portalEnabled">
                                        <strong>Enable Customer Portal</strong>
                                    </label>
                                </div>
                                <div class="form-text">Allow customers to check repair status online</div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="customer_messaging_enabled" id="messagingEnabled" <?= $current_settings['customer_messaging_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="messagingEnabled">
                                        <strong>Enable Customer Messaging</strong>
                                    </label>
                                </div>
                                <div class="form-text">Allow two-way communication between customers and technicians (requires portal to be enabled)</div>
                            </div>
                            
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const portalToggle = document.getElementById('portalEnabled');
                                const messagingToggle = document.getElementById('messagingEnabled');
                                
                                function updateMessagingState() {
                                    if (!portalToggle.checked) {
                                        messagingToggle.checked = false;
                                        messagingToggle.disabled = true;
                                        messagingToggle.parentElement.style.opacity = '0.5';
                                    } else {
                                        messagingToggle.disabled = false;
                                        messagingToggle.parentElement.style.opacity = '1';
                                    }
                                }
                                
                                portalToggle.addEventListener('change', updateMessagingState);
                                updateMessagingState(); // Initial state
                            });
                            </script>
                            
                            <h6 class="mt-4">Priority Response Times</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Low Priority (hours)</label>
                                        <input type="number" name="priority_low_hours" class="form-control" value="<?= $current_settings['priority_low_hours'] ?>" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Medium Priority (hours)</label>
                                        <input type="number" name="priority_medium_hours" class="form-control" value="<?= $current_settings['priority_medium_hours'] ?>" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">High Priority (hours)</label>
                                        <input type="number" name="priority_high_hours" class="form-control" value="<?= $current_settings['priority_high_hours'] ?>" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Urgent Priority (hours)</label>
                                        <input type="number" name="priority_urgent_hours" class="form-control" value="<?= $current_settings['priority_urgent_hours'] ?>" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Default response times for all locations. Individual locations can override these settings.</small>
                            
                            <h6 class="mt-4">Invoice Settings</h6>
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
                                <button type="submit" name="save_company_settings" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>
                                    Save Company Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Locations Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'locations' ? 'show active' : '' ?>" id="locations" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Existing Locations</h6>
                                <?php if ($locations): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Tax Rate</th>
                                                    <th>Priority Overrides</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($locations as $location): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($location['name']) ?></td>
                                                    <td><?= htmlspecialchars($location['phone']) ?></td>
                                                    <td><?= $location['tax_rate'] ?>%</td>
                                                    <td>
                                                        <?php if ($location['priority_urgent_hours'] || $location['priority_high_hours'] || $location['priority_medium_hours'] || $location['priority_low_hours']): ?>
                                                            <small class="text-success">Custom</small>
                                                        <?php else: ?>
                                                            <small class="text-muted">Default</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="editLocation(<?= $location['id'] ?>)">Edit</button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No locations configured yet.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <h6>Add New Location</h6>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Location Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" name="phone" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tax Rate (%)</label>
                                        <input type="number" name="tax_rate" class="form-control" step="0.01" value="0">
                                    </div>
                                    
                                    <h6 class="mt-3">Priority Response Times (Optional)</h6>
                                    <small class="text-muted d-block mb-2">Leave blank to use company defaults</small>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <label class="form-label form-label-sm">Low (hrs)</label>
                                                <input type="number" name="priority_low_hours" class="form-control form-control-sm" placeholder="<?= $current_settings['priority_low_hours'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <label class="form-label form-label-sm">Medium (hrs)</label>
                                                <input type="number" name="priority_medium_hours" class="form-control form-control-sm" placeholder="<?= $current_settings['priority_medium_hours'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label form-label-sm">High (hrs)</label>
                                                <input type="number" name="priority_high_hours" class="form-control form-control-sm" placeholder="<?= $current_settings['priority_high_hours'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label form-label-sm">Urgent (hrs)</label>
                                                <input type="number" name="priority_urgent_hours" class="form-control form-control-sm" placeholder="<?= $current_settings['priority_urgent_hours'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" name="add_location" class="btn btn-success w-100">
                                        <i class="bi bi-plus me-1"></i>Add Location
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Users Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'users' ? 'show active' : '' ?>" id="users" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Existing Users</h6>
                                <?php if ($users): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><span class="badge bg-primary"><?= ucfirst($user['role']) ?></span></td>
                                                    <td>
                                                        <a href="/SupporTracker/users-edit?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No users found.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <h6>Add New User</h6>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-select" required>
                                            <option value="technician">Technician</option>
                                            <option value="manager">Manager</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="add_user" class="btn btn-success w-100">
                                        <i class="bi bi-plus me-1"></i>Add User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Asset Categories Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'asset-categories' ? 'show active' : '' ?>" id="asset-categories" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Existing Asset Categories</h6>
                                <?php if ($asset_categories): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($asset_categories as $category): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($category['name']) ?></td>
                                                    <td><?= htmlspecialchars($category['description']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No asset categories found.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <h6>Add Asset Category</h6>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Category Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3"></textarea>
                                    </div>
                                    <button type="submit" name="add_asset_category" class="btn btn-success w-100">
                                        <i class="bi bi-plus me-1"></i>Add Category
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Categories Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'service-categories' ? 'show active' : '' ?>" id="service-categories" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Existing Service Categories</h6>
                                <?php if ($service_categories): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>SLA Hours</th>
                                                    <th>Priority</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($service_categories as $category): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($category['name']) ?></td>
                                                    <td><?= $category['sla_hours'] ?> hours</td>
                                                    <td><span class="badge bg-<?= $category['priority'] === 'high' ? 'danger' : ($category['priority'] === 'medium' ? 'warning' : 'success') ?>"><?= ucfirst($category['priority']) ?></span></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No service categories found.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <h6>Add Service Category</h6>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Category Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">SLA Hours</label>
                                        <input type="number" name="sla_hours" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Priority</label>
                                        <select name="priority" class="form-select" required>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="add_service_category" class="btn btn-success w-100">
                                        <i class="bi bi-plus me-1"></i>Add Category
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Pricing Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'service-pricing' ? 'show active' : '' ?>" id="service-pricing" role="tabpanel">
                        <h6>Default Service Pricing</h6>
                        <p class="text-muted">Set default prices for common services. These will auto-populate in ticket billing.</p>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Virus/Malware Removal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price_virus_removal" class="form-control" value="<?= $service_prices['virus_removal'] ?? 125 ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">OS Installation</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price_os_install" class="form-control" value="<?= $service_prices['os_install'] ?? 150 ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Data Recovery</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price_data_recovery" class="form-control" value="<?= $service_prices['data_recovery'] ?? 200 ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Hardware Installation</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price_hardware_install" class="form-control" value="<?= $service_prices['hardware_install'] ?? 75 ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Software Installation</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price_software_install" class="form-control" value="<?= $service_prices['software_install'] ?? 50 ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Network Setup</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price_network_setup" class="form-control" value="<?= $service_prices['network_setup'] ?? 100 ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">System Tune-up</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price_tune_up" class="form-control" value="<?= $service_prices['tune_up'] ?? 85 ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Default Hourly Rate</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="default_hourly_rate" class="form-control" value="<?= $current_settings['default_hourly_rate'] ?>" step="0.01" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="save_service_pricing" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Save Service Pricing
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Activate correct tab based on server response
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = '<?= $activeTab ?>';
    if (activeTab) {
        const tabButton = document.getElementById(activeTab + '-tab');
        const tabPane = document.getElementById(activeTab);
        if (tabButton && tabPane) {
            // Remove active from all tabs
            document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Activate correct tab
            tabButton.classList.add('active');
            tabPane.classList.add('show', 'active');
        }
    }
});
</script>