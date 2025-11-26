<!-- Assets Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Asset Name</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Serial Number</th>
                        <th>Model</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Tickets</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($assets): ?>
                        <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($asset['name']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($asset['customer_name']) ?></td>
                            <td><?= htmlspecialchars($asset['type']) ?></td>
                            <td><?= htmlspecialchars($asset['serial_number']) ?></td>
                            <td><?= htmlspecialchars($asset['model']) ?></td>
                            <td><?= htmlspecialchars($asset['location']) ?></td>
                            <td>
                                <span class="badge bg-<?= $asset['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($asset['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= $asset['ticket_count'] ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editAsset(<?= $asset['id'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="createTicket(<?= $asset['id'] ?>)">
                                    <i class="bi bi-ticket"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-laptop fs-1"></i>
                                <p class="mt-2">No assets found</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Asset Modal -->
<div class="modal fade" id="assetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Asset Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="Computer">Computer</option>
                                <option value="Server">Server</option>
                                <option value="Printer">Printer</option>
                                <option value="Phone">Phone</option>
                                <option value="Network">Network Equipment</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_asset" class="btn btn-primary">Create Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAsset(id) {
    // TODO: Implement edit functionality
    alert('Edit asset functionality coming soon');
}

function createTicket(assetId) {
    window.location.href = '/SupporTracker/tickets?asset_id=' + assetId;
}
</script>