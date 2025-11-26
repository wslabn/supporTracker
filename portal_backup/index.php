<?php
// Portal config - no authentication required
// Debug logging
error_log("Portal accessed directly: " . $_SERVER['REQUEST_URI']);

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supporttracker", 'supporttracker', '3Ga55ociates1nc!');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = '';
$ticket = null;

if ($_POST) {
    // Handle message sending
    if (isset($_POST['send_customer_message'])) {
        $ticket_id = (int)$_POST['ticket_id'];
        $customer_name = trim($_POST['customer_name']);
        $message = trim($_POST['message']);
        
        if ($ticket_id && $customer_name && $message) {
            $stmt = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, sender_type, sender_name, message) VALUES (?, 'customer', ?, ?)");
            $stmt->execute([$ticket_id, $customer_name, $message]);
            
            // Redirect to avoid resubmission
            header("Location: /SupporTracker/portal/?ticket_id=" . $ticket_id);
            exit;
        }
    }
    
    $ticket_number = trim($_POST['ticket_number']);
    $phone = trim($_POST['phone']);
    
    if ($ticket_number && $phone) {
        // Clean phone number (remove formatting)
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        
        $stmt = $pdo->prepare("
            SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
                   a.name as asset_name, a.model as asset_model,
                   sc.name as service_category_name, sc.sla_hours
            FROM tickets t
            LEFT JOIN customers c ON t.customer_id = c.id
            LEFT JOIN assets a ON t.asset_id = a.id
            LEFT JOIN service_categories sc ON t.service_category_id = sc.id
            WHERE (t.ticket_number = ? OR CONCAT('TKT-', LPAD(t.id, 6, '0')) = ?)
            AND (c.phone LIKE ? OR c.phone LIKE ?)
        ");
        
        $stmt->execute([
            $ticket_number, 
            $ticket_number,
            '%' . $phone_clean . '%',
            '%' . $phone . '%'
        ]);
        
        $ticket = $stmt->fetch();
        
        if (!$ticket) {
            $error = 'Ticket not found. Please check your ticket number and phone number.';
        } else {
            // Get location info for contact details
            $location_stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
            $location_stmt->execute([$ticket['location_id'] ?? 1]);
            $location = $location_stmt->fetch();
        }
    } else {
        $error = 'Please enter both ticket number and phone number.';
    }
}

// Check if we're showing a specific ticket from redirect
if (!$ticket && isset($_GET['ticket_id'])) {
    $ticket_id = (int)$_GET['ticket_id'];
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
               a.name as asset_name, a.model as asset_model,
               sc.name as service_category_name, sc.sla_hours
        FROM tickets t
        LEFT JOIN customers c ON t.customer_id = c.id
        LEFT JOIN assets a ON t.asset_id = a.id
        LEFT JOIN service_categories sc ON t.service_category_id = sc.id
        WHERE t.id = ?
    ");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();
    
    if ($ticket) {
        $location_stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
        $location_stmt->execute([$ticket['location_id'] ?? 1]);
        $location = $location_stmt->fetch();
    }
}

// Get work updates if ticket found
$updates = [];
$parts = [];
$messages = [];
if ($ticket) {
    $stmt = $pdo->prepare("
        SELECT tu.*, u.name as technician_name
        FROM ticket_updates tu
        LEFT JOIN users u ON tu.technician_id = u.id
        WHERE tu.ticket_id = ? AND tu.is_internal = 0
        ORDER BY tu.created_at DESC
    ");
    $stmt->execute([$ticket['id']]);
    $updates = $stmt->fetchAll();
    
    // Get parts for this ticket
    $stmt = $pdo->prepare("
        SELECT description, status, sell_price
        FROM ticket_parts
        WHERE ticket_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$ticket['id']]);
    $parts = $stmt->fetchAll();
    
    // Get messages for this ticket and mark technician messages as read
    $stmt = $pdo->prepare("
        SELECT * FROM ticket_messages
        WHERE ticket_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$ticket['id']]);
    $messages = $stmt->fetchAll();
    
    // Mark technician messages as read when customer views
    $pdo->prepare("UPDATE ticket_messages SET is_read = TRUE, read_at = NOW() WHERE ticket_id = ? AND sender_type = 'technician' AND is_read = FALSE")->execute([$ticket['id']]);
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check Repair Status - SupportTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .portal-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            color: white;
            padding: 2rem 0;
        }
        .status-card {
            border-left: 4px solid #0d6efd;
        }
    </style>
    
    <script>
        // Auto-detect system theme
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        document.documentElement.setAttribute('data-bs-theme', mediaQuery.matches ? 'dark' : 'light');
        
        mediaQuery.addEventListener('change', (e) => {
            document.documentElement.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
        });
    </script>
</head>
<body>
    <div class="portal-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><i class="bi bi-tools me-2"></i>SupportTracker</h1>
                    <p class="lead mb-0">Check Your Repair Status</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <?php if (!$ticket): ?>
        <!-- Lookup Form -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-search me-2"></i>Find Your Repair</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Ticket Number</label>
                                <input type="text" class="form-control" name="ticket_number" 
                                       placeholder="TKT-000001" value="<?= htmlspecialchars($_POST['ticket_number'] ?? '') ?>" required>
                                <small class="text-muted">Found on your work order receipt</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" 
                                       placeholder="(555) 123-4567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                <small class="text-muted">Phone number on file</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>Check Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Ticket Details -->
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
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab">
                                    <i class="bi bi-chat me-1"></i>Messages
                                </button>
                            </li>
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
                            <div class="tab-pane fade" id="messages" role="tabpanel">
                                <!-- Send Message Form -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="send_customer_message" value="1">
                                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
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
                        <?php if (isset($location)): ?>
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
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>