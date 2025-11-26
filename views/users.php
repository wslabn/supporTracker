<?php if (isset($success)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    <?= $success ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                            <?php if ($user['must_change_password']): ?>
                                <span class="badge bg-secondary ms-1">Must Change Password</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= getRoleBadgeColor($user['role']) ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?= $user['id'] ?>)">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <?php if ($user['username'] !== 'admin'): ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="toggle_user" class="btn btn-sm btn-outline-<?= $user['is_active'] ? 'secondary' : 'success' ?>">
                                    <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-select" required>
                                <option value="technician">Technician</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Default Location</label>
                        <select name="location_id" class="form-select">
                            <option value="">No default location</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">User's primary/home location</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Location Access</label>
                        <div class="form-text mb-2">Select which locations this user can access:</div>
                        <div class="row">
                            <?php foreach ($locations as $location): ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="accessible_locations[]" value="<?= $location['id'] ?>" id="loc<?= $location['id'] ?>">
                                    <label class="form-check-label" for="loc<?= $location['id'] ?>">
                                        <?= htmlspecialchars($location['name']) ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text">If none selected, user only has access to their default location</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Temporary Password *</label>
                        <input type="password" name="password" class="form-control" required>
                        <div class="form-text">User will be forced to change on first login</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-text mb-2">Role defaults are pre-selected. Customize as needed:</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_customers" id="perm1">
                                    <label class="form-check-label" for="perm1">Manage Customers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_edit_customer_billing" id="perm2">
                                    <label class="form-check-label" for="perm2">Edit Customer Billing</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_assets" id="perm3">
                                    <label class="form-check-label" for="perm3">Manage Assets</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_create_tickets" id="perm4">
                                    <label class="form-check-label" for="perm4">Create Tickets</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_view_all_tickets" id="perm5">
                                    <label class="form-check-label" for="perm5">View All Tickets</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_projects" id="perm6">
                                    <label class="form-check-label" for="perm6">Manage Projects</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_create_invoices" id="perm7">
                                    <label class="form-check-label" for="perm7">Create Invoices</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_record_payments" id="perm8">
                                    <label class="form-check-label" for="perm8">Record Payments</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_view_reports" id="perm9">
                                    <label class="form-check-label" for="perm9">View Reports</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_users" id="perm10">
                                    <label class="form-check-label" for="perm10">Manage Users</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_locations" id="perm11">
                                    <label class="form-check-label" for="perm11">Manage Locations</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_change_settings" id="perm12">
                                    <label class="form-check-label" for="perm12">Change System Settings</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_delete_data" id="perm13">
                                    <label class="form-check-label" for="perm13">Delete Records</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                    document.querySelector('select[name="role"]').addEventListener('change', function() {
                        const role = this.value;
                        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
                        
                        // Clear all
                        checkboxes.forEach(cb => cb.checked = false);
                        
                        // Set defaults based on role
                        if (role === 'admin') {
                            checkboxes.forEach(cb => cb.checked = true);
                        } else if (role === 'manager') {
                            ['can_manage_customers', 'can_edit_customer_billing', 'can_manage_assets', 'can_create_tickets', 'can_view_all_tickets', 'can_manage_projects', 'can_create_invoices', 'can_record_payments', 'can_view_reports', 'can_manage_users', 'can_manage_locations'].forEach(perm => {
                                const cb = document.querySelector(`input[value="${perm}"]`);
                                if (cb) cb.checked = true;
                            });
                        } else if (role === 'technician') {
                            ['can_manage_customers', 'can_manage_assets', 'can_create_tickets', 'can_record_payments'].forEach(perm => {
                                const cb = document.querySelector(`input[value="${perm}"]`);
                                if (cb) cb.checked = true;
                            });
                        }
                    });
                    
                    // Trigger on page load
                    document.querySelector('select[name="role"]').dispatchEvent(new Event('change'));
                    </script>

<script>
function editUser(id) {
    window.location.href = '/SupporTracker/users-edit?id=' + id;
}
</script>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
function getRoleBadgeColor($role) {
    switch ($role) {
        case 'admin': return 'danger';
        case 'manager': return 'info';
        case 'technician': return 'primary';
        default: return 'secondary';
    }
}
?>