<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-list-task"></i> Service Categories</h5>
                <small class="text-muted">Define ticket types and project categories with SLA requirements</small>
            </div>
            <div class="card-body">
                <?php if ($categories): ?>
                    <div class="row">
                        <?php foreach ($categories as $category): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-start border-4" style="border-color: <?= htmlspecialchars($category['color']) ?> !important;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="<?= htmlspecialchars($category['icon']) ?> fs-4 me-2" style="color: <?= htmlspecialchars($category['color']) ?>;"></i>
                                        <h6 class="card-title mb-0"><?= htmlspecialchars($category['name']) ?></h6>
                                    </div>
                                    <p class="card-text text-muted small mb-2"><?= htmlspecialchars($category['description']) ?></p>
                                    
                                    <div class="row text-center mb-2">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Type</small>
                                            <span class="badge bg-secondary"><?= ucfirst($category['type']) ?></span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Priority</small>
                                            <span class="badge bg-<?= $category['default_priority'] === 'urgent' ? 'danger' : ($category['default_priority'] === 'high' ? 'warning' : ($category['default_priority'] === 'medium' ? 'info' : 'success')) ?>">
                                                <?= ucfirst($category['default_priority']) ?>
                                            </span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">SLA</small>
                                            <strong><?= $category['sla_hours'] ?>h</strong>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">
                                                <?= $category['ticket_count'] ?> tickets, <?= $category['project_count'] ?> projects
                                            </small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>', '<?= $category['type'] ?>', '<?= htmlspecialchars($category['description']) ?>', '<?= htmlspecialchars($category['icon']) ?>', '<?= htmlspecialchars($category['color']) ?>', '<?= $category['default_priority'] ?>', <?= $category['sla_hours'] ?>, <?= $category['billable_default'] ?>)">
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
                        <i class="bi bi-list-task fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No Service Categories</h5>
                        <p class="text-muted">Create categories to organize tickets and projects</p>
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

<!-- Service Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Service Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="categoryId">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Category Name</label>
                            <input type="text" class="form-control" name="name" id="categoryName" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Used For</label>
                            <select class="form-select" name="type" id="categoryType" required>
                                <option value="both">Tickets & Projects</option>
                                <option value="ticket">Tickets Only</option>
                                <option value="project">Projects Only</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="categoryDescription" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Icon</label>
                            <select class="form-select" name="icon" id="categoryIcon" required>
                                <option value="bi bi-cpu">Hardware</option>
                                <option value="bi bi-app-indicator">Software</option>
                                <option value="bi bi-wifi">Network</option>
                                <option value="bi bi-shield-check">Security</option>
                                <option value="bi bi-envelope">Email</option>
                                <option value="bi bi-globe">Web</option>
                                <option value="bi bi-hdd-stack">Storage</option>
                                <option value="bi bi-person-gear">Users</option>
                                <option value="bi bi-tools">Maintenance</option>
                                <option value="bi bi-chat-dots">Consultation</option>
                                <option value="bi bi-gear">General</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Color</label>
                            <select class="form-select" name="color" id="categoryColor" required>
                                <option value="#dc3545">Red (Urgent)</option>
                                <option value="#ffc107">Yellow (Important)</option>
                                <option value="#0d6efd">Blue (Standard)</option>
                                <option value="#198754">Green (Low Priority)</option>
                                <option value="#6f42c1">Purple (Special)</option>
                                <option value="#20c997">Teal (Services)</option>
                                <option value="#fd7e14">Orange (Projects)</option>
                                <option value="#6c757d">Gray (General)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Default Priority</label>
                            <select class="form-select" name="default_priority" id="categoryPriority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">SLA Response Time (Hours)</label>
                            <input type="number" class="form-control" name="sla_hours" id="categorySLA" min="1" max="168" required>
                            <small class="text-muted">Maximum response time for this category</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Billing</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="billable_default" id="categoryBillable" checked>
                                <label class="form-check-label" for="categoryBillable">
                                    Billable by default
                                </label>
                            </div>
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
function editCategory(id, name, type, description, icon, color, priority, sla, billable) {
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryType').value = type;
    document.getElementById('categoryDescription').value = description;
    document.getElementById('categoryIcon').value = icon;
    document.getElementById('categoryColor').value = color;
    document.getElementById('categoryPriority').value = priority;
    document.getElementById('categorySLA').value = sla;
    document.getElementById('categoryBillable').checked = billable == 1;
    
    document.getElementById('createBtn').style.display = 'none';
    document.getElementById('updateBtn').style.display = 'block';
    
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

// Reset modal when closed
document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryType').value = 'both';
    document.getElementById('categoryDescription').value = '';
    document.getElementById('categoryIcon').value = 'bi bi-gear';
    document.getElementById('categoryColor').value = '#6c757d';
    document.getElementById('categoryPriority').value = 'medium';
    document.getElementById('categorySLA').value = '24';
    document.getElementById('categoryBillable').checked = true;
    
    document.getElementById('createBtn').style.display = 'block';
    document.getElementById('updateBtn').style.display = 'none';
});
</script>