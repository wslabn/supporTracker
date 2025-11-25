<div class="modal fade" id="partModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= isset($part) ? 'Edit' : 'Add' ?> Part/Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="partForm">
                <div class="modal-body">
                    <?php if (isset($part)): ?>
                        <input type="hidden" name="part_id" value="<?= $part['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company *</label>
                                <select class="form-select" name="company_id" required>
                                    <option value="">Select Company</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?= $company['id'] ?>" <?= ($selected_company_id == $company['id'] || (isset($part) && $part['company_id'] == $company['id'])) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($company['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Item Name *</label>
                                <input type="text" class="form-control" name="item_name" value="<?= isset($part) ? htmlspecialchars($part['item_name']) : '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description (Optional)</label>
                                <textarea class="form-control" name="description" rows="2"><?= isset($part) ? htmlspecialchars($part['description']) : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Part Number</label>
                                <input type="text" class="form-control" name="part_number" value="<?= isset($part) ? htmlspecialchars($part['part_number']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantity" id="quantity" value="<?= isset($part) ? $part['quantity'] : '1' ?>" min="1" required oninput="calculatePrice()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Unit Cost *</label>
                                <input type="number" class="form-control" name="unit_cost" id="unit_cost" value="<?= isset($part) ? $part['unit_cost'] : '' ?>" step="0.01" min="0" required oninput="calculatePrice()">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Markup %</label>
                                <input type="number" class="form-control" name="markup_percent" id="markup_percent" value="<?= isset($part) ? $part['markup_percent'] : '0' ?>" step="0.01" min="0" oninput="calculatePrice()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Unit Price</label>
                                <input type="number" class="form-control" id="unit_price" step="0.01" readonly style="background-color: #f8f9fa;">
                                <small class="text-muted">Calculated from cost + markup</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Total Price</label>
                                <input type="number" class="form-control" id="total_price" step="0.01" readonly style="background-color: #f8f9fa;">
                                <small class="text-muted">Unit price × quantity</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Vendor</label>
                                <input type="text" class="form-control" name="vendor" value="<?= isset($part) ? htmlspecialchars($part['vendor']) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Vendor URL</label>
                                <input type="url" class="form-control" name="vendor_url" value="<?= isset($part) ? htmlspecialchars($part['vendor_url']) : '' ?>" placeholder="https://vendor.com/product-page">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="pending" <?= (isset($part) && $part['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="ordered" <?= (isset($part) && $part['status'] == 'ordered') ? 'selected' : '' ?>>Ordered</option>
                                    <option value="shipped" <?= (isset($part) && $part['status'] == 'shipped') ? 'selected' : '' ?>>Shipped</option>
                                    <option value="received" <?= (isset($part) && $part['status'] == 'received') ? 'selected' : '' ?>>Received</option>
                                    <option value="installed" <?= (isset($part) && $part['status'] == 'installed') ? 'selected' : '' ?>>Installed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Work Order</label>
                                <select class="form-select" name="work_order_id">
                                    <option value="">Select Work Order</option>
                                    <?php foreach ($workorders as $wo): ?>
                                        <option value="<?= $wo['id'] ?>" <?= ($selected_workorder_id == $wo['id'] || (isset($part) && $part['work_order_id'] == $wo['id'])) ? 'selected' : '' ?>>
                                            #<?= $wo['id'] ?> - <?= htmlspecialchars($wo['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="billable" <?= (isset($part) && $part['billable']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Billable to customer</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><?= isset($part) ? 'Update' : 'Add' ?> Part</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Calculate on load
setTimeout(calculatePrice, 100);

document.getElementById('partForm').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Form submitted');
    
    const formData = new FormData(this);
    formData.append('ajax', '1');
    formData.append(<?= isset($part) ? "'update_part'" : "'add_part'" ?>, '1');
    
    console.log('Form data entries:', [...formData.entries()]);
    
    fetch('/SupporTracker/parts', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text();
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed response:', data);
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('partModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            console.log('Response was not JSON:', text);
            alert('Server error - check console');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Network error: ' + error.message);
    });
});
</script>