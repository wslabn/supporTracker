<div class="row">
    <!-- Ticket Details -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5><?= htmlspecialchars($ticket['title']) ?></h5>
                        <div class="d-flex gap-2 mt-2">
                            <span class="badge bg-<?= 
                                $ticket['priority'] === 'urgent' ? 'danger' : 
                                ($ticket['priority'] === 'high' ? 'warning' : 
                                ($ticket['priority'] === 'medium' ? 'info' : 'success')) 
                            ?>">
                                <?= ucfirst($ticket['priority']) ?> Priority
                            </span>
                            <span class="badge bg-<?= 
                                $ticket['status'] === 'resolved' ? 'success' : 
                                ($ticket['status'] === 'in_progress' ? 'warning' : 
                                ($ticket['status'] === 'waiting' ? 'info' : 'secondary')) 
                            ?>">
                                <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                            </span>
                            <span class="badge bg-info"><?= htmlspecialchars($ticket['service_category_name']) ?></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">SLA: <?= $ticket['sla_hours'] ?> hours</small><br>
                        <small class="text-muted">Created: <?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?></small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer:</strong> <?= htmlspecialchars($ticket['customer_name']) ?><br>
                        <small class="text-muted">
                            <?= htmlspecialchars($ticket['customer_phone']) ?> • 
                            <?= htmlspecialchars($ticket['customer_email']) ?>
                        </small>
                    </div>
                    <div class="col-md-6">
                        <?php if ($ticket['asset_name']): ?>
                        <strong>Asset:</strong> <?= htmlspecialchars($ticket['asset_name']) ?><br>
                        <small class="text-muted">
                            <?= htmlspecialchars($ticket['asset_model']) ?>
                            <?= $ticket['serial_number'] ? ' • S/N: ' . htmlspecialchars($ticket['serial_number']) : '' ?>
                        </small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($ticket['technician_name']): ?>
                <div class="mb-3">
                    <strong>Assigned to:</strong> <?= htmlspecialchars($ticket['technician_name']) ?>
                </div>
                <?php endif; ?>
                
                <?php if ($ticket['description']): ?>
                <div class="mb-3">
                    <strong>Description:</strong><br>
                    <p class="mt-2"><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Tabbed Interface -->
        <div class="card mt-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="ticketTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="worklog-tab" data-bs-toggle="tab" data-bs-target="#worklog" type="button" role="tab">
                            <i class="bi bi-clock-history me-1"></i>Work Log
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="parts-tab" data-bs-toggle="tab" data-bs-target="#parts" type="button" role="tab">
                            <i class="bi bi-gear me-1"></i>Parts
                        </button>
                    </li>
                    <?php if ($messagingEnabled): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab">
                            <i class="bi bi-chat me-1"></i>Messages
                        </button>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="actions-tab" data-bs-toggle="tab" data-bs-target="#actions" type="button" role="tab">
                            <i class="bi bi-lightning me-1"></i>Actions
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="ticketTabContent">
                    <!-- Work Log Tab -->
                    <div class="tab-pane fade show active" id="worklog" role="tabpanel">
                        <?php if ($updates): ?>
                            <div class="work-log-container" style="max-height: 400px; overflow-y: auto; border: 1px solid var(--bs-border-color); border-radius: 0.375rem; padding: 1rem; background-color: var(--bs-body-bg);">
                            <?php foreach ($updates as $update): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong><?= htmlspecialchars($update['technician_name']) ?></strong>
                                        <span class="badge bg-<?= 
                                            $update['update_type'] === 'time_log' ? 'primary' : 
                                            ($update['update_type'] === 'status_change' ? 'warning' : 'secondary') 
                                        ?> ms-2">
                                            <?= ucfirst(str_replace('_', ' ', $update['update_type'])) ?>
                                        </span>
                                        <?php if ($update['is_internal']): ?>
                                        <span class="badge bg-danger ms-1">Internal</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted"><?= date('M j, Y g:i A', strtotime($update['created_at'])) ?></small>
                                        <br>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this work update?')">
                                            <input type="hidden" name="delete_update" value="<?= $update['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($update['content'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No work updates yet. Add your first update using the sidebar.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Parts Tab -->
                    <div class="tab-pane fade" id="parts" role="tabpanel">
                        <!-- Add Part Collapsible -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#addPartCollapse" style="cursor: pointer;">
                                <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Part/Material</h6>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="collapse" id="addPartCollapse">
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="add_part" value="1">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description *</label>
                                            <input type="text" class="form-control" name="description" required placeholder="1TB SSD Drive">
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Vendor</label>
                                                    <input type="text" class="form-control" name="vendor" placeholder="Best Buy, Amazon, etc.">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Part URL</label>
                                                    <input type="url" class="form-control" name="part_url" placeholder="https://...">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Cost Paid</label>
                                                    <input type="number" class="form-control" name="cost_paid" id="new_cost" step="0.01" placeholder="89.99" onchange="calculateNewSellPrice()">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Markup %</label>
                                                    <input type="number" class="form-control" id="new_markup" placeholder="35" onchange="calculateNewSellPrice()">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Sell Price</label>
                                                    <input type="number" class="form-control" name="sell_price" id="new_sell" step="0.01" placeholder="120.00">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="quoted">Quoted</option>
                                                        <option value="ordered">Ordered</option>
                                                        <option value="received">Received</option>
                                                        <option value="installed">Installed</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Order Number</label>
                                                    <input type="text" class="form-control" name="order_number" placeholder="Order/tracking number">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea class="form-control" name="notes" rows="2" placeholder="Additional notes..."></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-plus-lg me-1"></i>Add Part
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($parts): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Vendor</th>
                                            <th>Cost</th>
                                            <th>Sell Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($parts as $part): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($part['description']) ?>
                                                <?php if ($part['part_url']): ?>
                                                <br><a href="<?= htmlspecialchars($part['part_url']) ?>" target="_blank" class="small text-primary">
                                                    <i class="bi bi-link-45deg"></i>View Part
                                                </a>
                                                <?php endif; ?>
                                                <?php if ($part['order_number']): ?>
                                                <br><small class="text-muted">Order: <?= htmlspecialchars($part['order_number']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($part['vendor']) ?></td>
                                            <td><?= $part['cost_paid'] ? '$' . number_format((float)$part['cost_paid'], 2) : '-' ?></td>
                                            <td><?= $part['sell_price'] ? '$' . number_format((float)$part['sell_price'], 2) : '-' ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_part_status" value="1">
                                                    <input type="hidden" name="part_id" value="<?= $part['id'] ?>">
                                                    <select name="new_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="quoted" <?= $part['status'] === 'quoted' ? 'selected' : '' ?>>Quoted</option>
                                                        <option value="ordered" <?= $part['status'] === 'ordered' ? 'selected' : '' ?>>Ordered</option>
                                                        <option value="received" <?= $part['status'] === 'received' ? 'selected' : '' ?>>Received</option>
                                                        <option value="installed" <?= $part['status'] === 'installed' ? 'selected' : '' ?>>Installed</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPart<?= $part['id'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <small class="text-muted d-block"><?= htmlspecialchars($part['added_by_name']) ?></small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No parts or materials added yet. Use the sidebar to add parts.</p>
                        <?php endif; ?>
                    </div>
                    

                    <!-- Messages Tab -->
                    <?php if ($messagingEnabled): ?>
                    <div class="tab-pane fade" id="messages" role="tabpanel">
                        <!-- Send Message Form -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="send_message" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Send Message to Customer</label>
                                        <textarea class="form-control" name="message" rows="3" required placeholder="Type your message to the customer..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i>Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Messages History -->
                        <?php if ($messages): ?>
                            <div class="work-log-container" style="max-height: 300px; overflow-y: auto; border: 1px solid var(--bs-border-color); border-radius: 0.375rem; padding: 1rem; background-color: var(--bs-body-bg);">
                            <?php foreach ($messages as $message): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong><?= htmlspecialchars($message['sender_name']) ?></strong>
                                        <span class="badge bg-<?= $message['sender_type'] === 'technician' ? 'primary' : 'success' ?> ms-2">
                                            <?= ucfirst($message['sender_type']) ?>
                                        </span>
                                        <?php if ($message['sender_type'] === 'technician' && $message['is_read']): ?>
                                        <span class="badge bg-secondary ms-1">Read</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></small>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No messages yet. Send a message to communicate with the customer.</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quick Actions Tab -->
                    <div class="tab-pane fade" id="actions" role="tabpanel">
                        <div class="d-grid gap-2">
                            <?php if ($ticket['status'] === 'open'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="status_change">
                                <input type="hidden" name="content" value="Started working on ticket">
                                <input type="hidden" name="new_status" value="in_progress">
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-play me-1"></i>Start Work
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($ticket['status'] === 'in_progress' && $ticket['service_category_name'] === 'Security & Compliance'): ?>
                            <!-- Virus Removal Quick Actions -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Started malware scan using Malwarebytes Anti-Malware">
                                <input type="hidden" name="hours_logged" value="0.25">
                                <button type="submit" class="btn btn-info btn-sm w-100">
                                    <i class="bi bi-search me-1"></i>Start Malware Scan
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Removed malware threats and cleaned registry entries">
                                <input type="hidden" name="hours_logged" value="1.0">
                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                    <i class="bi bi-shield-x me-1"></i>Remove Threats
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Updated antivirus definitions and ran full system scan">
                                <input type="hidden" name="hours_logged" value="0.5">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-shield-check me-1"></i>Update Antivirus
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="status_change">
                                <input type="hidden" name="content" value="Virus removal completed. System is clean and secure. Recommended regular scans and updated antivirus software.">
                                <input type="hidden" name="new_status" value="resolved">
                                <input type="hidden" name="hours_logged" value="0.25">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-1"></i>Mark Resolved
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($ticket['status'] === 'in_progress'): ?>
                            <!-- General Support Quick Actions -->
                            <hr class="my-3">
                            <small class="text-muted text-uppercase fw-bold">General Actions</small>
                            
                            <form method="POST" style="display: inline;" class="mt-2">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Created full system backup">
                                <input type="hidden" name="hours_logged" value="0.5">
                                <button type="submit" class="btn btn-outline-success btn-sm w-100 mb-1">
                                    <i class="bi bi-hdd me-1"></i>Backup System
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Installed Windows updates and security patches">
                                <input type="hidden" name="hours_logged" value="0.5">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100 mb-1">
                                    <i class="bi bi-download me-1"></i>Install OS Updates
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Performed clean OS installation and restored user data">
                                <input type="hidden" name="hours_logged" value="3.0">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 mb-1">
                                    <i class="bi bi-arrow-repeat me-1"></i>Reload OS
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Restarted system and verified all services running properly">
                                <input type="hidden" name="hours_logged" value="0.25">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100 mb-1">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Restart System
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Ran system diagnostics and hardware tests">
                                <input type="hidden" name="hours_logged" value="0.75">
                                <button type="submit" class="btn btn-outline-info btn-sm w-100 mb-1">
                                    <i class="bi bi-cpu me-1"></i>Run Diagnostics
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Updated device drivers to latest versions">
                                <input type="hidden" name="hours_logged" value="0.5">
                                <button type="submit" class="btn btn-outline-success btn-sm w-100 mb-1">
                                    <i class="bi bi-gear me-1"></i>Update Drivers
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Cleaned temporary files and optimized system performance">
                                <input type="hidden" name="hours_logged" value="0.25">
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100 mb-1">
                                    <i class="bi bi-trash me-1"></i>Clean System
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Tested network connectivity and internet access">
                                <input type="hidden" name="hours_logged" value="0.25">
                                <button type="submit" class="btn btn-outline-info btn-sm w-100 mb-1">
                                    <i class="bi bi-wifi me-1"></i>Test Network
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Provided user training and documented solution">
                                <input type="hidden" name="hours_logged" value="0.5">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100 mb-1">
                                    <i class="bi bi-person-check me-1"></i>User Training
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="add_update" value="1">
                                <input type="hidden" name="update_type" value="note">
                                <input type="hidden" name="content" value="Waiting for customer response or additional information">
                                <input type="hidden" name="new_status" value="waiting">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100 mb-1">
                                    <i class="bi bi-clock me-1"></i>Wait for Customer
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <a href="/SupporTracker/tickets" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Tickets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Part Modals -->
        <?php foreach ($parts as $part): ?>
        <div class="modal fade" id="editPart<?= $part['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Part</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="update_part" value="<?= $part['id'] ?>">
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Cost Paid</label>
                                        <input type="number" class="form-control" name="cost_paid" id="cost_<?= $part['id'] ?>" step="0.01" value="<?= $part['cost_paid'] ?>" onchange="calculateSellPrice(<?= $part['id'] ?>)">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Sell Price</label>
                                        <input type="number" class="form-control" name="sell_price" id="sell_<?= $part['id'] ?>" step="0.01" value="<?= $part['sell_price'] ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Or use markup %</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="markup_<?= $part['id'] ?>" placeholder="35" onchange="calculateSellPrice(<?= $part['id'] ?>)">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Enter markup % to auto-calculate sell price</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Order Number</label>
                                <input type="text" class="form-control" name="order_number" value="<?= htmlspecialchars($part['order_number']) ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Part</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Add Work Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-plus-circle me-2"></i>Add Work Update</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="add_update" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Update Type</label>
                        <select class="form-select" name="update_type" required>
                            <option value="note">Work Note</option>
                            <option value="time_log">Time Log</option>
                            <option value="status_change">Status Change</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Update Details</label>
                        <textarea class="form-control" name="content" rows="4" required placeholder="Describe what you did, found, or changed..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hours Worked</label>
                        <input type="number" class="form-control" name="hours_logged" step="0.25" placeholder="0.5">
                        <small class="text-muted">Optional - for billing purposes</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Change Status To</label>
                        <select class="form-select" name="new_status">
                            <option value="">Keep current status</option>
                            <option value="in_progress">In Progress</option>
                            <option value="waiting">Waiting (Customer/Parts)</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_internal" id="isInternal">
                        <label class="form-check-label" for="isInternal">
                            Internal note (not visible to customer)
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>Add Update
                    </button>
                </form>
            </div>
        </div>
        
        <a href="/SupporTracker/tickets" class="btn btn-outline-secondary w-100 mt-3">
            <i class="bi bi-arrow-left me-1"></i>Back to Tickets
        </a>
    </div>
</div>

<script>
function calculateSellPrice(partId) {
    const cost = parseFloat(document.getElementById('cost_' + partId).value) || 0;
    const markup = parseFloat(document.getElementById('markup_' + partId).value) || 0;
    
    if (cost > 0 && markup > 0) {
        const sellPrice = cost * (1 + markup / 100);
        document.getElementById('sell_' + partId).value = sellPrice.toFixed(2);
    }
}

function calculateNewSellPrice() {
    const cost = parseFloat(document.getElementById('new_cost').value) || 0;
    const markup = parseFloat(document.getElementById('new_markup').value) || 0;
    
    if (cost > 0 && markup > 0) {
        const sellPrice = cost * (1 + markup / 100);
        document.getElementById('new_sell').value = sellPrice.toFixed(2);
    }
}
</script>