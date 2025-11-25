<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-wrench"></i> Work Order #<?= $workorder['id'] ?></h5>
                <div>
                    <button class="btn btn-sm btn-primary me-1" onclick="editWorkOrder(<?= $workorder['id'] ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <?php if ($workorder['billable'] && !$workorder['invoiced'] && in_array($workorder['status'], ['completed', 'closed'])): ?>
                        <button class="btn btn-sm btn-success" onclick="createInvoice(<?= $workorder['id'] ?>, <?= $workorder['company_id'] ?>)">
                            <i class="fas fa-file-invoice-dollar"></i> Invoice
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td><strong>Title:</strong></td><td><?= htmlspecialchars($workorder['title']) ?></td></tr>
                    <tr><td><strong>Company:</strong></td><td>
                        <a href="/SupporTracker/company?id=<?= $workorder['company_id'] ?>"><?= htmlspecialchars($workorder['company_name']) ?></a>
                    </td></tr>
                    <?php if ($workorder['asset_id']): ?>
                    <tr><td><strong>Asset:</strong></td><td>
                        <a href="/SupporTracker/asset?id=<?= $workorder['asset_id'] ?>"><?= htmlspecialchars($workorder['asset_name']) ?></a>
                        <?php if ($workorder['asset_tag']): ?>
                            <br><small class="text-muted"><?= htmlspecialchars($workorder['asset_tag']) ?></small>
                        <?php endif; ?>
                    </td></tr>
                    <?php endif; ?>
                    <?php if ($workorder['project_id']): ?>
                    <tr><td><strong>Project:</strong></td><td>
                        <a href="/SupporTracker/project?id=<?= $workorder['project_id'] ?>"><?= htmlspecialchars($workorder['project_name']) ?></a>
                    </td></tr>
                    <?php endif; ?>
                    <tr><td><strong>Status:</strong></td><td>
                        <span class="badge bg-<?= getStatusColor($workorder['status']) ?>">
                            <?= ucfirst(str_replace('_', ' ', $workorder['status'])) ?>
                        </span>
                    </td></tr>
                    <tr><td><strong>Priority:</strong></td><td>
                        <span class="badge bg-<?= getPriorityColor($workorder['priority']) ?>">
                            <?= ucfirst($workorder['priority']) ?>
                        </span>
                    </td></tr>
                    <tr><td><strong>Billable:</strong></td><td>
                        <?php if ($workorder['billable']): ?>
                            <i class="fas fa-dollar-sign text-success"></i> Billable
                        <?php else: ?>
                            <i class="fas fa-handshake text-primary"></i> Contract
                        <?php endif; ?>
                    </td></tr>
                    <?php if ($workorder['estimated_hours']): ?>
                    <tr><td><strong>Estimated:</strong></td><td><?= $workorder['estimated_hours'] ?> hours</td></tr>
                    <?php endif; ?>
                    <?php if ($workorder['actual_hours']): ?>
                    <tr><td><strong>Actual:</strong></td><td><?= $workorder['actual_hours'] ?> hours</td></tr>
                    <?php endif; ?>
                    <tr><td><strong>Rate:</strong></td><td>$<?= number_format($workorder['hourly_rate'], 2) ?>/hour</td></tr>
                    <tr><td><strong>Created:</strong></td><td><?= date('M j, Y g:i A', strtotime($workorder['created_at'])) ?></td></tr>
                </table>
                
                <?php if ($workorder['description']): ?>
                <div class="mt-3">
                    <strong>Internal Description:</strong>
                    <div class="border p-2 mt-1 bg-light"><?= nl2br(htmlspecialchars($workorder['description'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($workorder['customer_summary']): ?>
                <div class="mt-3">
                    <strong>Customer Summary:</strong>
                    <div class="border p-2 mt-1 border-primary"><?= nl2br(htmlspecialchars($workorder['customer_summary'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($workorder['customer_notes']): ?>
                <div class="mt-3">
                    <strong>Customer Notes:</strong>
                    <div class="border p-2 mt-1 border-primary"><?= nl2br(htmlspecialchars($workorder['customer_notes'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <ul class="nav nav-tabs" id="workorderTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tasks">
                    <i class="fas fa-tasks"></i> Tasks (<?= count($tasks) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#parts">
                    <i class="fas fa-cogs"></i> Parts & Equipment (<?= count($parts) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#checklist">
                    <i class="fas fa-clipboard-check"></i> Checklist
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#timeline">
                    <i class="fas fa-clock"></i> Timeline
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tasks">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6>Work Order Tasks</h6>
                        <button class="btn btn-sm btn-success" onclick="addTask()">
                            <i class="fas fa-plus"></i> Add Task
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if ($tasks): ?>
                            <div class="task-list">
                                <?php foreach ($tasks as $task): ?>
                                <div class="task-item d-flex align-items-center mb-2 p-2 border rounded <?= $task['completed'] ? 'bg-light' : '' ?>">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" 
                                               id="task_<?= $task['id'] ?>" 
                                               <?= $task['completed'] ? 'checked' : '' ?>
                                               onchange="toggleTask(<?= $task['id'] ?>, this.checked)">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="<?= $task['completed'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                            <strong><?= htmlspecialchars($task['task_name']) ?></strong>
                                            <?php if ($task['customer_visible']): ?>
                                                <i class="fas fa-eye text-primary ms-2" title="Visible to customer"></i>
                                            <?php else: ?>
                                                <i class="fas fa-eye-slash text-muted ms-2" title="Internal only"></i>
                                            <?php endif; ?>
                                            <?php if ($task['notes']): ?>
                                                <br><small class="text-muted"><?= nl2br(htmlspecialchars($task['notes'])) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($task['completed_at']): ?>
                                            <small class="text-success">
                                                <i class="fas fa-check"></i> Completed <?= date('M j, g:i A', strtotime($task['completed_at'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="task-actions">
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editTask(<?= $task['id'] ?>, '<?= addslashes($task['task_name']) ?>', '<?= addslashes($task['notes']) ?>', <?= $task['customer_visible'] ? 'true' : 'false' ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(<?= $task['id'] ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php 
                            $completed_count = array_sum(array_column($tasks, 'completed'));
                            $total_count = count($tasks);
                            $progress = $total_count > 0 ? ($completed_count / $total_count) * 100 : 0;
                            ?>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Progress: <?= $completed_count ?> of <?= $total_count ?> tasks completed</small>
                                    <small><?= round($progress) ?>%</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No tasks added yet. Click "Add Task" to create actionable items for this work order.</p>
                        <?php endif; ?>
                        
                        <!-- Quick Add Task Form -->
                        <div class="mt-3" id="quick-add-task" style="display: none;">
                            <div class="mb-2">
                                <input type="text" class="form-control" id="new-task-name" placeholder="Task name..." required>
                            </div>
                            <div class="mb-2">
                                <textarea class="form-control" id="new-task-notes" rows="2" placeholder="Notes (optional)..."></textarea>
                            </div>
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="new-task-customer-visible">
                                    <label class="form-check-label" for="new-task-customer-visible">
                                        <i class="fas fa-eye text-primary"></i> Visible to customer
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="saveNewTask()">
                                    <i class="fas fa-plus"></i> Add Task
                                </button>
                                <button class="btn btn-secondary" onclick="cancelAddTask()">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="parts">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6>Parts & Equipment</h6>
                        <button class="btn btn-sm btn-success" onclick="addPart(<?= $workorder_id ?>, <?= $workorder['company_id'] ?>)">
                            <i class="fas fa-plus"></i> Add Parts
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($parts): ?>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Cost</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parts as $part): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($part['item_name']) ?></strong>
                                        <?php if ($part['description']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($part['description']) ?></small>
                                        <?php endif; ?>
                                        <?php if ($part['part_number']): ?>
                                            <br><small class="text-muted">P/N: <?= htmlspecialchars($part['part_number']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $part['quantity'] ?></td>
                                    <td>$<?= number_format($part['total_cost'], 2) ?></td>
                                    <td>
                                        <?php if ($part['billable']): ?>
                                            $<?= number_format($part['total_price'], 2) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not billable</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= ucfirst($part['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editPart(<?= $part['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-muted p-3">No parts ordered for this work order.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="checklist">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6>Technician Checklist</h6>
                        <button class="btn btn-sm btn-success" onclick="addChecklist(<?= $workorder_id ?>)">
                            <i class="fas fa-plus"></i> Add Checklist
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Checklist functionality coming soon...</p>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="timeline">
                <div class="card">
                    <div class="card-header">
                        <h6>Work Order Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php
                            // Collect all timeline events
                            $timeline_events = [];
                            
                            // Work order created
                            $timeline_events[] = [
                                'date' => $workorder['created_at'],
                                'title' => 'Work Order Created',
                                'color' => 'primary',
                                'icon' => 'fas fa-plus'
                            ];
                            
                            // Task events
                            foreach ($tasks as $task) {
                                $timeline_events[] = [
                                    'date' => $task['created_at'],
                                    'title' => 'Task Added: ' . $task['task_name'],
                                    'color' => 'info',
                                    'icon' => 'fas fa-tasks'
                                ];
                                
                                if ($task['completed_at']) {
                                    $timeline_events[] = [
                                        'date' => $task['completed_at'],
                                        'title' => 'Task Completed: ' . $task['task_name'],
                                        'color' => 'success',
                                        'icon' => 'fas fa-check'
                                    ];
                                }
                            }
                            
                            // Parts events
                            foreach ($parts as $part) {
                                $timeline_events[] = [
                                    'date' => $part['created_at'],
                                    'title' => 'Part Added: ' . $part['item_name'],
                                    'color' => 'secondary',
                                    'icon' => 'fas fa-cogs'
                                ];
                                
                                if ($part['status'] == 'received') {
                                    $timeline_events[] = [
                                        'date' => $part['updated_at'] ?? $part['created_at'],
                                        'title' => 'Part Received: ' . $part['item_name'],
                                        'color' => 'warning',
                                        'icon' => 'fas fa-box'
                                    ];
                                }
                                
                                if ($part['status'] == 'installed') {
                                    $timeline_events[] = [
                                        'date' => $part['updated_at'] ?? $part['created_at'],
                                        'title' => 'Part Installed: ' . $part['item_name'],
                                        'color' => 'success',
                                        'icon' => 'fas fa-wrench'
                                    ];
                                }
                            }
                            
                            // Status change (if not new)
                            if ($workorder['status'] !== 'new') {
                                $timeline_events[] = [
                                    'date' => $workorder['updated_at'],
                                    'title' => 'Status: ' . ucfirst(str_replace('_', ' ', $workorder['status'])),
                                    'color' => 'warning',
                                    'icon' => 'fas fa-edit'
                                ];
                            }
                            
                            // Sort by date (newest first)
                            usort($timeline_events, function($a, $b) {
                                return strtotime($b['date']) - strtotime($a['date']);
                            });
                            ?>
                            
                            <?php foreach ($timeline_events as $event): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-<?= $event['color'] ?>">
                                    <i class="<?= $event['icon'] ?> text-white" style="font-size: 8px;"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6><?= htmlspecialchars($event['title']) ?></h6>
                                    <p class="text-muted"><?= date('M j, Y g:i A', strtotime($event['date'])) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="modalContainer"></div>

<!-- Task Edit Modal -->
<div class="modal fade" id="taskEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="taskEditForm">
                    <input type="hidden" id="edit-task-id">
                    <div class="mb-3">
                        <label class="form-label">Task Name</label>
                        <input type="text" class="form-control" id="edit-task-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="edit-task-notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit-task-customer-visible">
                            <label class="form-check-label">
                                <i class="fas fa-eye text-primary"></i> Visible to customer
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTaskEdit()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global function for part modal price calculation
function calculatePrice() {
    console.log('calculatePrice called');
    
    const unitCostEl = document.getElementById('unit_cost');
    const markupEl = document.getElementById('markup_percent');
    const quantityEl = document.getElementById('quantity');
    const unitPriceEl = document.getElementById('unit_price');
    const totalPriceEl = document.getElementById('total_price');
    
    console.log('Elements found:', {
        unitCost: !!unitCostEl,
        markup: !!markupEl,
        quantity: !!quantityEl,
        unitPrice: !!unitPriceEl,
        totalPrice: !!totalPriceEl
    });
    
    if (!unitCostEl || !unitPriceEl || !totalPriceEl) {
        console.log('Missing required elements');
        return;
    }
    
    const unitCost = parseFloat(unitCostEl.value) || 0;
    const markupPercent = parseFloat(markupEl.value) || 0;
    const quantity = parseFloat(quantityEl.value) || 1;
    
    console.log('Values:', { unitCost, markupPercent, quantity });
    
    const unitPrice = unitCost * (1 + (markupPercent / 100));
    const totalPrice = unitPrice * quantity;
    
    console.log('Calculated:', { unitPrice, totalPrice });
    
    unitPriceEl.value = unitPrice.toFixed(2);
    totalPriceEl.value = totalPrice.toFixed(2);
    
    console.log('Updated fields');
}

function addChecklist(workorderId) {
    alert('Checklist functionality will be implemented next');
}

function createInvoice(workorderId, companyId) {
    if (confirm('Create invoice for this work order?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/SupporTracker/invoices';
        
        const companyInput = document.createElement('input');
        companyInput.type = 'hidden';
        companyInput.name = 'company_id';
        companyInput.value = companyId;
        
        const woInput = document.createElement('input');
        woInput.type = 'hidden';
        woInput.name = 'work_order_ids[]';
        woInput.value = workorderId;
        
        const submitInput = document.createElement('input');
        submitInput.type = 'hidden';
        submitInput.name = 'generate_invoice';
        submitInput.value = '1';
        
        form.appendChild(companyInput);
        form.appendChild(woInput);
        form.appendChild(submitInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function submitPartForm() {
    console.log('submitPartForm called');
    alert('Form submission started - check console');
    
    const form = document.getElementById('partForm');
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('add_part', '1');
    
    console.log('Form data entries:', [...formData.entries()]);
    
    fetch('/SupporTracker/parts', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
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
            alert('Server error - Response was: ' + text.substring(0, 200));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Network error: ' + error.message);
    });
}

function editWorkOrder(id) {
    fetch(`/SupporTracker/workorders?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#workorderModal'));
            modal.show();
            setupWorkOrderForm();
        });
}

function addPart(workorderId, companyId) {
    fetch(`/SupporTracker/parts?action=add&workorder_id=${workorderId}&company_id=${companyId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#partModal'));
            modal.show();
        });
}

function editPart(id) {
    fetch(`/SupporTracker/parts?action=edit&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.querySelector('#partModal'));
            modal.show();
        });
}

function setupWorkOrderForm() {
    // Same form setup as workorders page
    const form = document.getElementById('workorderForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('update_workorder', '1');
            
            fetch('/SupporTracker/workorders', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.querySelector('#workorderModal')).hide();
                    location.reload();
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.querySelector('#workorderModal')).hide();
                location.reload();
            });
        });
    }
}

function toggleTask(taskId, completed) {
    const formData = new FormData();
    formData.append('toggle_task', '1');
    formData.append('task_id', taskId);
    formData.append('completed', completed);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error updating task:', error);
        location.reload();
    });
}

function addTask() {
    document.getElementById('quick-add-task').style.display = 'block';
    document.getElementById('new-task-name').focus();
}

function cancelAddTask() {
    document.getElementById('quick-add-task').style.display = 'none';
    document.getElementById('new-task-name').value = '';
    document.getElementById('new-task-notes').value = '';
    document.getElementById('new-task-customer-visible').checked = false;
}

function saveNewTask() {
    const taskName = document.getElementById('new-task-name').value.trim();
    const taskNotes = document.getElementById('new-task-notes').value.trim();
    const customerVisible = document.getElementById('new-task-customer-visible').checked;
    if (!taskName) {
        alert('Please enter a task name');
        return;
    }
    
    const formData = new FormData();
    formData.append('add_task', '1');
    formData.append('task_name', taskName);
    formData.append('notes', taskNotes);
    formData.append('customer_visible', customerVisible ? '1' : '0');
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        location.reload();
    });
}

function editTask(taskId, taskName, notes, customerVisible) {
    document.getElementById('edit-task-id').value = taskId;
    document.getElementById('edit-task-name').value = taskName;
    document.getElementById('edit-task-notes').value = notes;
    document.getElementById('edit-task-customer-visible').checked = customerVisible;
    
    const modal = new bootstrap.Modal(document.getElementById('taskEditModal'));
    modal.show();
}

function saveTaskEdit() {
    const taskId = document.getElementById('edit-task-id').value;
    const taskName = document.getElementById('edit-task-name').value.trim();
    const notes = document.getElementById('edit-task-notes').value.trim();
    const customerVisible = document.getElementById('edit-task-customer-visible').checked;
    
    if (!taskName) {
        alert('Please enter a task name');
        return;
    }
    
    const formData = new FormData();
    formData.append('edit_task', '1');
    formData.append('task_id', taskId);
    formData.append('task_name', taskName);
    formData.append('notes', notes);
    formData.append('customer_visible', customerVisible ? '1' : '0');
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('taskEditModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        location.reload();
    });
}

function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        const formData = new FormData();
        formData.append('delete_task', '1');
        formData.append('task_id', taskId);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }
}

// Allow Enter key to save new task
document.addEventListener('DOMContentLoaded', function() {
    const taskInput = document.getElementById('new-task-description');
    if (taskInput) {
        taskInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                saveNewTask();
            }
        });
    }
});
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
</style>