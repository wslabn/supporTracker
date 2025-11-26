<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-tags"></i> Asset Categories</h5>
            </div>
            <div class="card-body">
                <?php if ($categories): ?>
                    <div class="row">
                        <?php foreach ($categories as $category): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border-start border-4" style="border-color: <?= htmlspecialchars($category['color']) ?> !important;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="<?= htmlspecialchars($category['icon']) ?> fs-4 me-2" style="color: <?= htmlspecialchars($category['color']) ?>;"></i>
                                        <h6 class="card-title mb-0"><?= htmlspecialchars($category['name']) ?></h6>
                                    </div>
                                    <p class="card-text text-muted small"><?= htmlspecialchars($category['description']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-secondary"><?= $category['asset_count'] ?> assets</span>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>', '<?= htmlspecialchars($category['description']) ?>', '<?= htmlspecialchars($category['icon']) ?>', '<?= htmlspecialchars($category['color']) ?>')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-tags fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No Asset Categories</h5>
                        <p class="text-muted">Create categories to organize your customer assets</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                            <i class="bi bi-plus-lg me-1"></i>
                            Create First Category
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Asset Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="categoryId">
                    
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" id="categoryName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="categoryDescription" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Icon</label>
                            <select class="form-select" name="icon" id="categoryIcon" required>
                                <option value="bi bi-laptop">Laptop</option>
                                <option value="bi bi-pc-display">Desktop</option>
                                <option value="bi bi-server">Server</option>
                                <option value="bi bi-printer">Printer</option>
                                <option value="bi bi-phone">Phone</option>
                                <option value="bi bi-tablet">Tablet</option>
                                <option value="bi bi-router">Network</option>
                                <option value="bi bi-camera">Camera</option>
                                <option value="bi bi-headset">Headset</option>
                                <option value="bi bi-tools">Tools</option>
                                <option value="bi bi-gear">Equipment</option>
                                <option value="bi bi-box">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <select class="form-select" name="color" id="categoryColor" required>
                                <option value="#0d6efd">Blue</option>
                                <option value="#198754">Green</option>
                                <option value="#dc3545">Red</option>
                                <option value="#ffc107">Yellow</option>
                                <option value="#fd7e14">Orange</option>
                                <option value="#6f42c1">Purple</option>
                                <option value="#20c997">Teal</option>
                                <option value="#6c757d">Gray</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_category" id="createBtn" class="btn btn-primary">Create Category</button>
                    <button type="submit" name="update_category" id="updateBtn" class="btn btn-primary" style="display: none;">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, description, icon, color) {
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryDescription').value = description;
    document.getElementById('categoryIcon').value = icon;
    document.getElementById('categoryColor').value = color;
    
    document.getElementById('createBtn').style.display = 'none';
    document.getElementById('updateBtn').style.display = 'block';
    
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

// Reset modal when closed
document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryDescription').value = '';
    document.getElementById('categoryIcon').value = 'bi bi-laptop';
    document.getElementById('categoryColor').value = '#0d6efd';
    
    document.getElementById('createBtn').style.display = 'block';
    document.getElementById('updateBtn').style.display = 'none';
});
</script>