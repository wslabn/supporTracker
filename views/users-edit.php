<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit User</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                            <div class="form-text">Username cannot be changed</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-select" required>
                                <option value="technician" <?= $user['role'] === 'technician' ? 'selected' : '' ?>>Technician</option>
                                <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Default Location</label>
                        <select name="location_id" class="form-select">
                            <option value="">No default location</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?= $location['id'] ?>" <?= $user['location_id'] == $location['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($location['name']) ?>
                                </option>
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
                                    <input class="form-check-input" type="checkbox" name="accessible_locations[]" value="<?= $location['id'] ?>" id="editloc<?= $location['id'] ?>" <?= in_array($location['id'], $userAccessibleLocations) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="editloc<?= $location['id'] ?>">
                                        <?= htmlspecialchars($location['name']) ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text">If none selected, user only has access to their default location</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-text mb-2">Customize user permissions:</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_customers" id="perm1" <?= in_array('can_manage_customers', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm1">Manage Customers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_edit_customer_billing" id="perm2" <?= in_array('can_edit_customer_billing', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm2">Edit Customer Billing</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_assets" id="perm3" <?= in_array('can_manage_assets', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm3">Manage Assets</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_create_tickets" id="perm4" <?= in_array('can_create_tickets', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm4">Create Tickets</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_view_all_tickets" id="perm5" <?= in_array('can_view_all_tickets', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm5">View All Tickets</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_projects" id="perm6" <?= in_array('can_manage_projects', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm6">Manage Projects</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_create_invoices" id="perm7" <?= in_array('can_create_invoices', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm7">Create Invoices</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_record_payments" id="perm8" <?= in_array('can_record_payments', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm8">Record Payments</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_view_reports" id="perm9" <?= in_array('can_view_reports', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm9">View Reports</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_users" id="perm10" <?= in_array('can_manage_users', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm10">Manage Users</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_manage_locations" id="perm11" <?= in_array('can_manage_locations', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm11">Manage Locations</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_change_settings" id="perm12" <?= in_array('can_change_settings', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm12">Change System Settings</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="can_delete_data" id="perm13" <?= in_array('can_delete_data', $currentPermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm13">Delete Records</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/SupporTracker/users" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>