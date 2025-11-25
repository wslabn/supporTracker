<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success">Settings saved successfully!</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5><i class="fas fa-map-marker-alt"></i> Store Locations</h5>
                <button class="btn btn-primary btn-sm" onclick="addLocation()">
                    <i class="fas fa-plus"></i> Add Location
                </button>
            </div>
            <div class="card-body">
                <?php if ($locations): ?>
                    <div class="row">
                        <?php foreach ($locations as $location): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card <?= $location['is_default'] ? 'border-primary' : '' ?>">
                                <div class="card-header d-flex justify-content-between">
                                    <h6>
                                        <?= htmlspecialchars($location['name']) ?>
                                        <?php if ($location['is_default']): ?>
                                            <span class="badge bg-primary">Default</span>
                                        <?php endif; ?>
                                    </h6>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editLocation(<?= $location['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <small>
                                        <?= nl2br(htmlspecialchars($location['address'])) ?><br>
                                        <?= htmlspecialchars($location['phone']) ?><br>
                                        <?= htmlspecialchars($location['email']) ?><br>
                                        Tax: <?= $location['tax_rate'] ?>% | Terms: <?= $location['payment_terms'] ?> days
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No locations configured. Add your first location to get started.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="/SupporTracker/settings" id="locationForm">
                <div class="modal-header">
                    <h5 class="modal-title">Location Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="location_id" id="location_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Location Name *</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" id="address" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" name="website" id="website">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tax Rate (%)</label>
                                <input type="number" step="0.01" class="form-control" name="tax_rate" id="tax_rate">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tax ID/EIN</label>
                                <input type="text" class="form-control" name="tax_id" id="tax_id">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Terms (days)</label>
                                <input type="number" class="form-control" name="payment_terms" id="payment_terms" value="30">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo URL</label>
                                <input type="url" class="form-control" name="logo_url" id="logo_url">
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="set_default" id="set_default">
                                <label class="form-check-label" for="set_default">Set as default location</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addLocation() {
    document.getElementById('locationForm').reset();
    document.getElementById('location_id').value = '';
    new bootstrap.Modal(document.getElementById('locationModal')).show();
}

// Handle form submission with AJAX
document.getElementById('locationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('ajax', '1');
    
    fetch('/SupporTracker/settings', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('locationModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

function editLocation(id) {
    // Get location data and populate form
    const locations = <?= json_encode($locations) ?>;
    const location = locations.find(l => l.id == id);
    
    document.getElementById('location_id').value = location.id;
    document.getElementById('name').value = location.name || '';
    document.getElementById('address').value = location.address || '';
    document.getElementById('phone').value = location.phone || '';
    document.getElementById('email').value = location.email || '';
    document.getElementById('website').value = location.website || '';
    document.getElementById('tax_rate').value = location.tax_rate || '';
    document.getElementById('tax_id').value = location.tax_id || '';
    document.getElementById('payment_terms').value = location.payment_terms || '30';
    document.getElementById('logo_url').value = location.logo_url || '';
    document.getElementById('set_default').checked = location.is_default == 1;
    
    new bootstrap.Modal(document.getElementById('locationModal')).show();
}
</script>