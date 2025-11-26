<!-- Projects Kanban Board -->
<div class="row">
    <?php 
    $statuses = ['planning' => 'Planning', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed'];
    foreach ($statuses as $status => $label): 
        $statusProjects = array_filter($projects, fn($p) => $p['status'] === $status);
    ?>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-<?= getStatusColor($status) ?> text-white">
                <h6 class="mb-0"><?= $label ?> (<?= count($statusProjects) ?>)</h6>
            </div>
            <div class="card-body p-2" style="min-height: 400px;">
                <?php foreach ($statusProjects as $project): ?>
                <div class="card mb-2 project-card" onclick="viewProject(<?= $project['id'] ?>)">
                    <div class="card-body p-3">
                        <h6 class="card-title mb-1"><?= htmlspecialchars($project['name']) ?></h6>
                        <p class="card-text small text-muted mb-2"><?= htmlspecialchars($project['customer_name']) ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?= getPriorityColor($project['priority']) ?> small">
                                <?= ucfirst($project['priority']) ?>
                            </span>
                            <small class="text-muted"><?= $project['ticket_count'] ?> tickets</small>
                        </div>
                        
                        <?php if ($project['due_date']): ?>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i>
                                <?= date('M j', strtotime($project['due_date'])) ?>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- New Project Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Project Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Customer *</label>
                                <select class="form-select" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="planning" selected>Planning</option>
                                    <option value="active">Active</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="due_date">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="budget" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Project Manager</label>
                                <select class="form-select" name="project_manager_id">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($technicians as $tech): ?>
                                        <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_project" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.project-card {
    cursor: pointer;
    transition: transform 0.2s;
}
.project-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<script>
function viewProject(id) {
    window.location.href = `/SupporTracker/projects/${id}`;
}
</script>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'planning': return 'secondary';
        case 'active': return 'primary';
        case 'on_hold': return 'warning';
        case 'completed': return 'success';
        default: return 'secondary';
    }
}

function getPriorityColor($priority) {
    switch ($priority) {
        case 'urgent': return 'danger';
        case 'high': return 'warning';
        case 'medium': return 'info';
        case 'low': return 'success';
        default: return 'secondary';
    }
}
?>