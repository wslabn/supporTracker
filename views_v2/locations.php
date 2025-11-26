<!-- Locations Grid -->
<div class="row">
    <?php foreach ($locations as $location): ?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 <?= $location['is_default'] ? 'border-primary' : '' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0"><?= htmlspecialchars($location['name']) ?></h5>
                    <?php if ($location['is_default']): ?>
                        <span class="badge bg-primary">Default</span>
                    <?php endif; ?>
                </div>
                
                <?php if ($location['address']): ?>
                <p class="card-text small text-muted mb-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    <?= nl2br(htmlspecialchars($location['address'])) ?>
                </p>
                <?php endif; ?>
                
                <?php if ($location['phone']): ?>
                <p class="card-text small text-muted mb-1">
                    <i class="bi bi-telephone me-1"></i>
                    <?= htmlspecialchars($location['phone']) ?>
                </p>
                <?php endif; ?>
                
                <?php if ($location['email']): ?>
                <p class="card-text small text-muted mb-2">
                    <i class="bi bi-envelope me-1"></i>
                    <?= htmlspecialchars($location['email']) ?>
                </p>
                <?php endif; ?>
                
                <div class="row text-center mt-3">
                    <div class="col-4">
                        <div class="fw-bold text-primary"><?= $location['customer_count'] ?></div>
                        <div class="small text-muted">Customers</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-success"><?= $location['technician_count'] ?></div>
                        <div class="small text-muted">Staff</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-warning"><?= $location['ticket_count'] ?></div>
                        <div class="small text-muted">Tickets</div>
                    </div>
                </div>
                
                <?php if ($location['tax_rate']): ?>
                <div class="mt-2 text-center">
                    <small class="text-muted">Tax Rate: <?= $location['tax_rate'] ?>%</small>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <div class="btn-group w-100" role="group">
                    <button class="btn btn-outline-primary btn-sm" onclick="viewLocation(<?= $location['id'] ?>)">
                        <i class="bi bi-eye"></i> View
                    </button>
                    <?php if (!$location['is_default']): ?>
                    <button class="btn btn-outline-success btn-sm" onclick="setDefault(<?= $location['id'] ?>)">
                        <i class="bi bi-star"></i> Default
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- New Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Location Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tax Rate (%)</label>
                                <input type="number" class="form-control" name="tax_rate" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tax ID</label>
                                <input type="text" class="form-control" name="tax_id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_location" class="btn btn-primary">Create Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewLocation(id) {
    window.location.href = `/SupporTracker/locations/${id}`;
}

function setDefault(id) {
    if (confirm('Set this as the default location?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="set_default" value="1">
            <input type="hidden" name="location_id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>