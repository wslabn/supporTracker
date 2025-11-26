<?php
// Customer Portal - Phase 1: Basic ticket lookup
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
            
            header("Location: /SupporTracker/portal?ticket_id=" . $ticket_id);
            exit;
        }
    }
    
    // Handle credential addition
    if (isset($_POST['add_credential'])) {
        $ticket_id = (int)$_POST['ticket_id'];
        $credential_type = trim($_POST['credential_type']);
        $service_name = trim($_POST['service_name']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if ($ticket_id && $credential_type && $service_name && $password) {
            // Get the asset ID from the ticket
            $stmt = $pdo->prepare("SELECT asset_id FROM tickets WHERE id = ?");
            $stmt->execute([$ticket_id]);
            $asset_id = $stmt->fetchColumn();
            
            if ($asset_id) {
                // Add the credential
                $stmt = $pdo->prepare("INSERT INTO asset_credentials (asset_id, credential_type, service_name, username, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$asset_id, $credential_type, $service_name, $username, $password]);
                
                // Add a ticket update about the credential being provided
                $stmt = $pdo->prepare("INSERT INTO ticket_updates (ticket_id, update_type, content, is_internal) VALUES (?, 'customer_update', ?, 0)");
                $stmt->execute([$ticket_id, "Customer provided {$credential_type} credential for {$service_name}"]);
                
                header("Location: /SupporTracker/portal?ticket_id=" . $ticket_id . "&credential_added=1");
                exit;
            }
        }
    }
    
    // Ticket lookup
    if (isset($_POST['lookup_ticket'])) {
        $ticket_number = trim($_POST['ticket_number']);
        $phone = trim($_POST['phone']);
        
        if ($ticket_number && $phone) {
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
                // Get location info
                $location_stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
                $location_stmt->execute([$ticket['location_id'] ?? 1]);
                $location = $location_stmt->fetch();
            }
        } else {
            $error = 'Please enter both ticket number and phone number.';
        }
    }
}

// Check for direct ticket access via URL
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

// Get work updates and messages if ticket found
$updates = [];
$parts = [];
$messages = [];
if ($ticket) {
    // Work updates (non-internal only)
    $stmt = $pdo->prepare("
        SELECT tu.*, u.name as technician_name
        FROM ticket_updates tu
        LEFT JOIN users u ON tu.technician_id = u.id
        WHERE tu.ticket_id = ? AND tu.is_internal = 0
        ORDER BY tu.created_at DESC
    ");
    $stmt->execute([$ticket['id']]);
    $updates = $stmt->fetchAll();
    
    // Parts
    $stmt = $pdo->prepare("
        SELECT description, status, sell_price
        FROM ticket_parts
        WHERE ticket_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$ticket['id']]);
    $parts = $stmt->fetchAll();
    
    // Messages
    $stmt = $pdo->prepare("
        SELECT * FROM ticket_messages
        WHERE ticket_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$ticket['id']]);
    $messages = $stmt->fetchAll();
    
    // Mark technician messages as read
    $pdo->prepare("UPDATE ticket_messages SET is_read = TRUE, read_at = NOW() WHERE ticket_id = ? AND sender_type = 'technician' AND is_read = FALSE")->execute([$ticket['id']]);
}

// Get company info from settings (key-value pairs)
$company_info = ['company_name' => 'SupportTracker', 'company_logo_url' => null];
try {
    $settings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('company_name', 'company_logo_url')")->fetchAll();
    foreach ($settings as $setting) {
        $company_info[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Settings table issue, use defaults
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Portal - <?= htmlspecialchars($company_info['company_name'] ?? 'SupportTracker') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .status-card {
            border-left: 4px solid #0d6efd;
        }
    </style>
    
    <script>
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        document.documentElement.setAttribute('data-bs-theme', mediaQuery.matches ? 'dark' : 'light');
        mediaQuery.addEventListener('change', (e) => {
            document.documentElement.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
        });
    </script>
</head>
<body>
    <?php if (!$ticket): ?>
    <!-- Two-Column Layout for Login -->
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Left Column - Company Branding -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-primary bg-opacity-10">
                <div class="text-center p-5">
                    <?php if ($company_info['company_logo_url']): ?>
                    <img src="<?= htmlspecialchars($company_info['company_logo_url']) ?>" alt="Company Logo" style="max-height: 120px; max-width: 300px;" class="mb-4">
                    <?php else: ?>
                    <h1 class="display-4 text-primary mb-4">
                        <i class="bi bi-tools me-3"></i><?= htmlspecialchars($company_info['company_name'] ?? 'SupportTracker') ?>
                    </h1>
                    <?php endif; ?>
                    
                    <h2 class="h4 mb-4">Customer Portal</h2>
                    
                    <div class="text-start">
                        <h5 class="text-primary mb-3">Check Your Repair Status</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>View repair progress</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Message your technician</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Track parts and materials</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Get status updates</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Login Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="w-100" style="max-width: 400px;">
                    <div class="card border-0 shadow">
                        <div class="card-body p-5">
                            <h3 class="card-title text-center mb-4">Access Your Repair</h3>
                            
                            <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Ticket Number</label>
                                    <input type="text" class="form-control form-control-lg" name="ticket_number" 
                                           placeholder="TKT-000001" value="<?= htmlspecialchars($_POST['ticket_number'] ?? '') ?>" required>
                                    <small class="text-body-secondary">Found on your work order receipt</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Phone Number</label>
                                    <input type="tel" class="form-control form-control-lg" name="phone" 
                                           placeholder="(555) 123-4567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                    <small class="text-body-secondary">Phone number on file</small>
                                </div>
                                
                                <button type="submit" name="lookup_ticket" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search me-2"></i>Check Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <?php else: ?>
    <!-- Header for Ticket Details -->
    <div class="border-bottom">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col">
                    <?php if ($company_info['company_logo_url']): ?>
                    <img src="<?= htmlspecialchars($company_info['company_logo_url']) ?>" alt="Company Logo" style="max-height: 40px;" class="me-3">
                    <?php endif; ?>
                    <span class="h5 mb-0"><?= htmlspecialchars($company_info['company_name'] ?? 'SupportTracker') ?> Customer Portal</span>
                </div>
                <div class="col-auto">
                    <a href="/SupporTracker/portal" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>New Search
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid px-5 mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Repair Status</h2>
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
                                <button class="nav-link" id="credentials-tab" data-bs-toggle="tab" data-bs-target="#credentials" type="button" role="tab">
                                    <i class="bi bi-key me-1"></i>Account Access
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="portalTabContent">
                            <!-- Progress Updates Tab -->
                            <div class="tab-pane fade show active" id="updates" role="tabpanel">
                                <?php if ($updates): ?>
                                    <div style="max-height: 400px; overflow-y: auto; border: 1px solid var(--bs-border-color); border-radius: 0.375rem; padding: 1rem; background-color: var(--bs-body-bg);">
                                    <?php foreach ($updates as $update): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-<?= 
                                                    $update['update_type'] === 'status_change' ? 'warning' : 
                                                    ($update['update_type'] === 'priority_change' ? 'info' : 'secondary') 
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
                            
                            <!-- Credentials Tab -->
                            <div class="tab-pane fade" id="credentials" role="tabpanel">
                                <!-- Credentials Section Content -->
                                <?php if ($ticket['status'] !== 'resolved' && $ticket['status'] !== 'closed'): ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6><i class="bi bi-key me-2"></i>Provide Account Access</h6>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleCredentialForm()">
                                            <i class="bi bi-plus me-1"></i>Add Credential
                                        </button>
                                    </div>
                                    
                                    <?php if (isset($_GET['credential_added'])): ?>
                                    <div class="alert alert-success">
                                        <i class="bi bi-check-circle me-2"></i>Credential added successfully! Your technician now has access.
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Add Credential Form -->
                                    <div id="credentialFormTab" class="border rounded p-3 mb-3" style="display: none;">
                                        <form method="POST">
                                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <select class="form-select" name="credential_type" required>
                                                        <option value="device">Device Login</option>
                                                        <option value="email">Email Account</option>
                                                        <option value="software">Software/App</option>
                                                        <option value="network">WiFi/Network</option>
                                                        <option value="cloud">Cloud Service</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" name="service_name" placeholder="Service name" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="username" placeholder="Username">
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(this.previousElementSibling, this)">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" name="add_credential" class="btn btn-primary">
                                                        <i class="bi bi-plus me-1"></i>Add
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-body-secondary">Examples: Windows Login, Gmail, Office 365, WiFi Password</small>
                                        </form>
                                    </div>
                                    
                                    <p class="text-muted mb-0">Provide account passwords and credentials your technician may need to resolve your issue.</p>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">Credential management is only available for active repairs.</p>
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
                        <p><strong>Email:</strong><br><?= htmlspecialchars($location['email'] ?? 'support@company.com') ?></p>
                        <?php if ($location['address']): ?>
                        <p><strong>Visit us:</strong><br><?= nl2br(htmlspecialchars($location['address'])) ?></p>
                        <?php endif; ?>
                        <?php else: ?>
                        <p><strong>Call us:</strong><br>(555) 123-4567</p>
                        <p><strong>Email:</strong><br>support@company.com</p>
                        <?php endif; ?>
                        <p class="text-muted small">
                            Reference: <?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleCredentialForm() {
        const form = document.getElementById('credentialFormTab');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    
    function togglePasswordVisibility(input, button) {
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
    </script>
</body>
</html>