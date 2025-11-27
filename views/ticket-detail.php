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
                                ($ticket['status'] === 'waiting' ? 'info' : 
                                ($ticket['status'] === 'waiting_payment' ? 'primary' : 'secondary'))) 
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
                    <?php if ($ticket['asset_id']): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="credentials-tab" data-bs-toggle="tab" data-bs-target="#credentials" type="button" role="tab">
                            <i class="bi bi-key me-1"></i>Credentials
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="asset-update-tab" data-bs-toggle="tab" data-bs-target="#asset-update" type="button" role="tab">
                            <i class="bi bi-pc-display me-1"></i>Update Asset
                        </button>
                    </li>
                    <?php endif; ?>
                    <?php if ($messagingEnabled): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab">
                            <i class="bi bi-chat me-1"></i>Messages
                        </button>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#billing" type="button" role="tab">
                            <i class="bi bi-receipt me-1"></i>Billing
                        </button>
                    </li>
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
                                            ($update['update_type'] === 'status_change' ? 'warning' : 
                                            ($update['update_type'] === 'priority_change' ? 'info' : 'secondary')) 
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
                    
                    <!-- Credentials Tab -->
                    <?php if ($ticket['asset_id']): ?>
                    <div class="tab-pane fade" id="credentials" role="tabpanel">
                        <!-- Add Credential Form -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#addCredentialCollapse" style="cursor: pointer;">
                                <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Credential</h6>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="collapse" id="addCredentialCollapse">
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="add_ticket_credential" value="1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <select class="form-select" name="credential_type" required>
                                                    <option value="device">Device Login</option>
                                                    <option value="email">Email Account</option>
                                                    <option value="software">Software/App</option>
                                                    <option value="network">WiFi/Network</option>
                                                    <option value="cloud">Cloud Service</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="service_name" placeholder="Service name" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" name="username" placeholder="Username">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-plus me-1"></i>Add
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Credentials List -->
                        <?php if ($assetCredentials): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Service</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assetCredentials as $cred): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= ucfirst($cred['credential_type']) ?></span></td>
                                        <td><?= htmlspecialchars($cred['service_name']) ?></td>
                                        <td><?= htmlspecialchars($cred['username']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="showTicketPassword(this, '<?= htmlspecialchars($cred['password']) ?>')">
                                                <i class="bi bi-eye"></i> Show
                                            </button>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this credential?')">
                                                <input type="hidden" name="delete_credential" value="<?= $cred['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No credentials stored for this asset. Add credentials above.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Update Asset Tab -->
                    <div class="tab-pane fade" id="asset-update" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-pc-display me-2"></i>Update Asset Specifications</h6>
                                <small class="text-muted">Update asset specs when installing upgrades or replacements</small>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="update_asset_specs" value="1">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Asset Name</label>
                                                <input type="text" class="form-control" name="asset_name" value="<?= htmlspecialchars($ticket['asset_name']) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Model</label>
                                                <input type="text" class="form-control" name="model" value="<?= htmlspecialchars($ticket['asset_model']) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Serial Number</label>
                                                <input type="text" class="form-control" name="serial_number" value="<?= htmlspecialchars($ticket['serial_number']) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Operating System</label>
                                                <input type="text" class="form-control" name="operating_system" value="<?= htmlspecialchars($assetDetails['operating_system'] ?? '') ?>" placeholder="Windows 11 Pro">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">CPU</label>
                                                <input type="text" class="form-control" name="cpu" value="<?= htmlspecialchars($assetDetails['cpu'] ?? '') ?>" placeholder="Intel i7-12700K">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">RAM (GB)</label>
                                                <input type="number" class="form-control" name="ram_gb" value="<?= $assetDetails['ram_gb'] ?? '' ?>" placeholder="16">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Storage (GB)</label>
                                                <input type="number" class="form-control" name="storage_gb" value="<?= $assetDetails['storage_gb'] ?? '' ?>" placeholder="1000">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Graphics Card</label>
                                                <input type="text" class="form-control" name="graphics_card" value="<?= htmlspecialchars($assetDetails['graphics_card'] ?? '') ?>" placeholder="NVIDIA RTX 4060">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Network Card</label>
                                                <input type="text" class="form-control" name="network_card" value="<?= htmlspecialchars($assetDetails['network_card'] ?? '') ?>" placeholder="Realtek PCIe GbE">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Update Notes</label>
                                        <textarea class="form-control" name="update_notes" rows="3" placeholder="Describe what was upgraded or changed..." required></textarea>
                                        <small class="text-muted">This will be added as a work log entry</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Hours Worked</label>
                                        <input type="number" class="form-control" name="hours_logged" step="0.25" placeholder="1.0">
                                        <small class="text-muted">Time spent on the upgrade</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i>Update Asset Specs
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

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
                    
                    <!-- Billing Tab -->
                    <div class="tab-pane fade" id="billing" role="tabpanel">
                        <!-- Add Items Card -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#addItemsCollapse" style="cursor: pointer;">
                                <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Services & Labor</h6>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="collapse" id="addItemsCollapse">
                                <div class="card-body">
                                    <!-- Predefined Services -->
                                    <h6><i class="bi bi-list-check me-2"></i>Add Service</h6>
                                    <form method="POST" class="mb-4">
                                        <input type="hidden" name="add_service_item" value="1">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-select" name="service_type" onchange="updateServicePrice(this)">
                                                    <option value="">Select Service...</option>
                                                    <option value="virus_removal" data-price="<?= $servicePrices['virus_removal'] ?? 125 ?>">Virus/Malware Removal - $<?= $servicePrices['virus_removal'] ?? 125 ?></option>
                                                    <option value="os_install" data-price="<?= $servicePrices['os_install'] ?? 150 ?>">OS Installation - $<?= $servicePrices['os_install'] ?? 150 ?></option>
                                                    <option value="data_recovery" data-price="<?= $servicePrices['data_recovery'] ?? 200 ?>">Data Recovery - $<?= $servicePrices['data_recovery'] ?? 200 ?></option>
                                                    <option value="hardware_install" data-price="<?= $servicePrices['hardware_install'] ?? 75 ?>">Hardware Installation - $<?= $servicePrices['hardware_install'] ?? 75 ?></option>
                                                    <option value="software_install" data-price="<?= $servicePrices['software_install'] ?? 50 ?>">Software Installation - $<?= $servicePrices['software_install'] ?? 50 ?></option>
                                                    <option value="network_setup" data-price="<?= $servicePrices['network_setup'] ?? 100 ?>">Network Setup - $<?= $servicePrices['network_setup'] ?? 100 ?></option>
                                                    <option value="tune_up" data-price="<?= $servicePrices['tune_up'] ?? 85 ?>">System Tune-up - $<?= $servicePrices['tune_up'] ?? 85 ?></option>
                                                    <option value="custom">Custom Service</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="price" id="service_price" step="0.01" placeholder="Price" required>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="bi bi-plus me-1"></i>Add Service
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <input type="text" class="form-control" name="custom_description" id="custom_description" placeholder="Custom service description" style="display: none;">
                                        </div>
                                    </form>
                                    
                                    <hr>
                                    
                                    <!-- Custom Labor -->
                                    <h6><i class="bi bi-clock me-2"></i>Add Labor</h6>
                                    <form method="POST">
                                        <input type="hidden" name="add_labor_item" value="1">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="description" placeholder="Labor description" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="hours" step="0.25" placeholder="Hours" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="rate" step="0.01" value="75" placeholder="Rate">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="total" id="labor_total" step="0.01" placeholder="Total" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="bi bi-plus me-1"></i>Add Labor
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Combined Invoice Preview -->
                        <?php 
                        $allInvoiceItems = [];
                        $subtotal = 0;
                        
                        // Add billing items (services/labor)
                        foreach ($billingItems as $item) {
                            $allInvoiceItems[] = [
                                'type' => 'service',
                                'id' => $item['id'],
                                'description' => $item['description'],
                                'quantity' => $item['quantity'],
                                'unit_price' => $item['unit_price'],
                                'total_price' => $item['total_price'],
                                'discount' => $item['discount'] ?? 0,
                                'taxable' => $item['taxable'] ?? true
                            ];
                        }
                        
                        // Add parts with sell prices
                        foreach ($parts as $part) {
                            if ($part['sell_price'] > 0) {
                                $allInvoiceItems[] = [
                                    'type' => 'part',
                                    'id' => $part['id'],
                                    'description' => $part['description'],
                                    'quantity' => 1,
                                    'unit_price' => $part['sell_price'],
                                    'total_price' => $part['sell_price'],
                                    'discount' => $part['discount'] ?? 0,
                                    'taxable' => $part['taxable'] ?? true
                                ];
                            }
                        }
                        
                        // Calculate subtotal with discounts
                        $subtotal = 0;
                        ?>
                        
                        <?php if ($allInvoiceItems): ?>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Invoice Preview - All Items</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Discount</th>
                                            <th>Total</th>
                                            <th>Taxable</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allInvoiceItems as $item): 
                                            $discount = $item['discount'] ?? 0;
                                            $discountAmount = $item['unit_price'] * ($discount / 100);
                                            $finalPrice = $item['unit_price'] - $discountAmount;
                                            $finalTotal = $finalPrice * $item['quantity'];
                                        ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($item['description']) ?>
                                                <small class="text-muted d-block"><?= ucfirst($item['type']) ?></small>
                                            </td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td>
                                                <span id="price_<?= $item['type'] ?>_<?= $item['id'] ?>">$<?= number_format($item['unit_price'], 2) ?></span>
                                                <button class="btn btn-sm btn-outline-primary ms-1" onclick="editPrice('<?= $item['type'] ?>', <?= $item['id'] ?>, <?= $item['unit_price'] ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span id="discount_<?= $item['type'] ?>_<?= $item['id'] ?>"><?= $discount ?>%</span>
                                                <button class="btn btn-sm btn-outline-warning ms-1" onclick="editDiscount('<?= $item['type'] ?>', <?= $item['id'] ?>, <?= $discount ?>)">
                                                    <i class="bi bi-percent"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span id="total_<?= $item['type'] ?>_<?= $item['id'] ?>">$<?= number_format($finalTotal, 2) ?></span>
                                                <?php if ($discount > 0): ?>
                                                <br><small class="text-success">Saved $<?= number_format($discountAmount * $item['quantity'], 2) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm <?= $item['taxable'] ? 'btn-success' : 'btn-outline-secondary' ?>" onclick="toggleTax('<?= $item['type'] ?>', <?= $item['id'] ?>, this)">
                                                    <?= $item['taxable'] ? 'Yes' : 'No' ?>
                                                </button>
                                            </td>
                                            <td>
                                                <?php if ($item['type'] === 'service'): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this item?')">
                                                    <input type="hidden" name="delete_billing_item" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                <small class="text-muted">From Parts</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php 
                                        $subtotal += $finalTotal; // Use discounted total
                                        endforeach; ?>
                                        <tr class="table-info">
                                            <td colspan="4"><strong>Subtotal</strong></td>
                                            <td><strong>$<?= number_format($subtotal, 2) ?></strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($ticket['status'] === 'resolved'): ?>
                            <div class="card-footer text-center">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="create_invoice" value="1">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-receipt me-2"></i>Create Invoice & Mark Waiting Payment
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No billing items added yet. Add services or labor above, and parts will appear from the Parts tab.</p>
                        <?php endif; ?>
                    </div>
                    
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
                            
                            <?php if ($ticket['status'] === 'resolved'): ?>
                            <!-- Invoice Actions -->
                            <hr class="my-3">
                            <small class="text-muted text-uppercase fw-bold">Billing Actions</small>
                            
                            <form method="POST" style="display: inline;" class="mt-2">
                                <input type="hidden" name="create_invoice" value="1">
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-receipt me-1"></i>Create Invoice & Mark Waiting Payment
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
                            <option value="priority_change">Priority Change</option>
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
                            <option value="waiting_payment">Waiting Payment</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Change Priority To</label>
                        <select class="form-select" name="new_priority">
                            <option value="">Keep current priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
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

function showTicketPassword(button, password) {
    const isShowing = button.innerHTML.includes('Hide');
    if (isShowing) {
        button.innerHTML = '<i class="bi bi-eye"></i> Show';
    } else {
        button.innerHTML = '<i class="bi bi-eye-slash"></i> Hide';
        alert('Password: ' + password);
        setTimeout(() => {
            button.innerHTML = '<i class="bi bi-eye"></i> Show';
        }, 3000);
    }
}

function updateServicePrice(select) {
    const option = select.options[select.selectedIndex];
    const price = option.getAttribute('data-price');
    const customDesc = document.getElementById('custom_description');
    
    if (select.value === 'custom') {
        customDesc.style.display = 'block';
        customDesc.required = true;
        document.getElementById('service_price').value = '';
    } else {
        customDesc.style.display = 'none';
        customDesc.required = false;
        document.getElementById('service_price').value = price || '';
    }
}

// Auto-calculate labor total
document.addEventListener('DOMContentLoaded', function() {
    const hoursInput = document.querySelector('input[name="hours"]');
    const rateInput = document.querySelector('input[name="rate"]');
    const totalInput = document.getElementById('labor_total');
    
    function calculateLabor() {
        const hours = parseFloat(hoursInput?.value) || 0;
        const rate = parseFloat(rateInput?.value) || 0;
        if (totalInput) {
            totalInput.value = (hours * rate).toFixed(2);
        }
    }
    
    if (hoursInput) hoursInput.addEventListener('input', calculateLabor);
    if (rateInput) rateInput.addEventListener('input', calculateLabor);
});

function toggleTax(itemType, itemId, button) {
    const formData = new FormData();
    formData.append('toggle_tax', '1');
    formData.append('item_type', itemType);
    formData.append('item_id', itemId);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // Toggle button appearance
            if (button.classList.contains('btn-success')) {
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
                button.textContent = 'No';
            } else {
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                button.textContent = 'Yes';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function editPrice(itemType, itemId, currentPrice) {
    const newPrice = prompt('Enter new price:', currentPrice);
    if (newPrice && !isNaN(newPrice)) {
        updateItemField(itemType, itemId, 'price', newPrice);
    }
}

function editDiscount(itemType, itemId, currentDiscount) {
    const newDiscount = prompt('Enter discount percentage (0-100):', currentDiscount);
    if (newDiscount !== null && !isNaN(newDiscount) && newDiscount >= 0 && newDiscount <= 100) {
        updateItemField(itemType, itemId, 'discount', newDiscount);
    }
}

function updateItemField(itemType, itemId, field, value) {
    const formData = new FormData();
    formData.append('update_item_field', '1');
    formData.append('item_type', itemType);
    formData.append('item_id', itemId);
    formData.append('field', field);
    formData.append('value', value);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>