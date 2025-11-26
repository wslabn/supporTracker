<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Repair Status</h2>
            <a href="/SupporTracker/portal/" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>New Search
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Ticket Info -->
        <div class="card status-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><?= htmlspecialchars($ticket['title']) ?></h5>
                        <p class="text-muted mb-2"><?= htmlspecialchars($ticket['description']) ?></p>
                        <p><strong>Device:</strong> <?= htmlspecialchars($ticket['asset_name'] . ' ' . $ticket['asset_model']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-end">
                            <span class="badge bg-<?= 
                                $ticket['status'] === 'resolved' ? 'success' : 
                                ($ticket['status'] === 'in_progress' ? 'warning' : 
                                ($ticket['status'] === 'waiting' ? 'info' : 'secondary')) 
                            ?> fs-6 mb-2">
                                <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                            </span>
                            <br>
                            <small class="text-muted">
                                Ticket: <?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?><br>
                                Created: <?= date('M j, Y', strtotime($ticket['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabbed Interface -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="portalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="updates-tab" data-bs-toggle="tab" data-bs-target="#updates" type="button" role="tab">
                            <i class="bi bi-clock-history me-1"></i>Progress Updates
                        </button>
                    </li>
                    <?php if ($parts): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="parts-tab" data-bs-toggle="tab" data-bs-target="#parts" type="button" role="tab">
                            <i class="bi bi-gear me-1"></i>Parts & Materials
                        </button>
                    </li>
                    <?php endif; ?>
                    <?php if (PORTAL_MESSAGING_ENABLED): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab">
                            <i class="bi bi-chat me-1"></i>Messages
                        </button>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="portalTabContent">
                    <!-- Progress Updates Tab -->
                    <div class="tab-pane fade show active" id="updates" role="tabpanel">
                        <?php if ($updates): ?>
                            <div class="work-log-container" style="max-height: 400px; overflow-y: auto; border: 1px solid var(--bs-border-color); border-radius: 0.375rem; padding: 1rem; background-color: var(--bs-body-bg);">
                            <?php foreach ($updates as $update): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-<?= 
                                            $update['update_type'] === 'status_change' ? 'warning' : 'secondary' 
                                        ?>">
                                            <?= ucfirst(str_replace('_', ' ', $update['update_type'])) ?>
                                        </span>
                                        <?php if ($update['technician_name']): ?>
                                        <small class="text-muted ms-2">by <?= explode(' ', $update['technician_name'])[0] ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><?= date('M j, Y g:i A', strtotime($update['created_at'])) ?></small>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($update['content'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No updates available yet. We'll post updates as work progresses.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Parts Tab -->
                    <?php if ($parts): ?>
                    <div class="tab-pane fade" id="parts" role="tabpanel">
                        <?php foreach ($parts as $part): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <strong><?= htmlspecialchars($part['description']) ?></strong>
                                <br>
                                <span class="badge bg-<?= 
                                    $part['status'] === 'installed' ? 'success' : 
                                    ($part['status'] === 'received' ? 'info' : 
                                    ($part['status'] === 'ordered' ? 'warning' : 'secondary')) 
                                ?>">
                                    <?= ucfirst($part['status']) ?>
                                </span>
                            </div>
                            <?php if ($part['sell_price']): ?>
                            <div class="text-end">
                                <strong class="text-success">$<?= number_format($part['sell_price'], 2) ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Messages Tab -->
                    <?php if (PORTAL_MESSAGING_ENABLED): ?>
                    <div class="tab-pane fade" id="messages" role="tabpanel">
                        <!-- Send Message Form -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="send_message" value="1">
                                    <input type="hidden" name="customer_name" value="<?= htmlspecialchars($ticket['customer_name']) ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Send Message to Technician</label>
                                        <textarea class="form-control" name="message" rows="3" required placeholder="Ask a question or provide additional information..."></textarea>
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
                                        <?php if ($message['sender_type'] === 'customer' && $message['is_read']): ?>
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
                            <p class="text-muted">No messages yet. Send a message to communicate with your technician.</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Contact Info -->
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-telephone me-2"></i>Need Help?</h6>
            </div>
            <div class="card-body">
                <?php if ($location): ?>
                <p><strong>Call us:</strong><br><?= htmlspecialchars($location['phone'] ?? '(555) 123-4567') ?></p>
                <p><strong>Email:</strong><br><?= htmlspecialchars($location['email'] ?? 'support@yourcompany.com') ?></p>
                <?php if ($location['address']): ?>
                <p><strong>Visit us:</strong><br><?= nl2br(htmlspecialchars($location['address'])) ?></p>
                <?php endif; ?>
                <?php else: ?>
                <p><strong>Call us:</strong><br>(555) 123-4567</p>
                <p><strong>Email:</strong><br>support@yourcompany.com</p>
                <?php endif; ?>
                <p class="text-muted small">
                    Reference ticket: <?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?>
                </p>
            </div>
        </div>
    </div>
</div>