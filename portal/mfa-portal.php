<?php
// MFA-enabled portal - no authentication required
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supporttracker", 'supporttracker', '3Ga55ociates1nc!');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = '';
$success = '';
$step = 'phone'; // phone, verify, dashboard

// Clean up expired codes
$pdo->query("DELETE FROM portal_mfa_codes WHERE expires_at < NOW()");

if ($_POST) {
    if (isset($_POST['send_code'])) {
        $phone = trim($_POST['phone']);
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone_clean) >= 10) {
            // Check if this phone has any tickets
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE phone LIKE ?");
            $stmt->execute(['%' . $phone_clean . '%']);
            $ticket_count = $stmt->fetchColumn();
            
            if ($ticket_count > 0) {
                // Generate 6-digit code
                $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                // Store code
                $stmt = $pdo->prepare("INSERT INTO portal_mfa_codes (phone, code, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$phone_clean, $code, $expires]);
                
                // Mock SMS - show code for development
                $success = "Development Mode: Your verification code is <strong>$code</strong><br>In production, this would be sent via SMS to $phone";
                $step = 'verify';
                $_SESSION['mfa_phone'] = $phone_clean;
            } else {
                $error = 'No tickets found for this phone number.';
            }
        } else {
            $error = 'Please enter a valid phone number.';
        }
    }
    
    if (isset($_POST['verify_code'])) {
        $phone = $_SESSION['mfa_phone'] ?? '';
        $code = trim($_POST['code']);
        
        $stmt = $pdo->prepare("SELECT * FROM portal_mfa_codes WHERE phone = ? AND code = ? AND expires_at > NOW() AND used = FALSE");
        $stmt->execute([$phone, $code]);
        $mfa_record = $stmt->fetch();
        
        if ($mfa_record) {
            // Mark code as used
            $pdo->prepare("UPDATE portal_mfa_codes SET used = TRUE WHERE id = ?")->execute([$mfa_record['id']]);
            
            // Get all tickets for this phone
            $stmt = $pdo->prepare("
                SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
                       a.name as asset_name, a.model as asset_model,
                       sc.name as service_category_name
                FROM tickets t
                LEFT JOIN customers c ON t.customer_id = c.id
                LEFT JOIN assets a ON t.asset_id = a.id
                LEFT JOIN service_categories sc ON t.service_category_id = sc.id
                WHERE c.phone LIKE ?
                ORDER BY t.created_at DESC
            ");
            $stmt->execute(['%' . $phone . '%']);
            $tickets = $stmt->fetchAll();
            
            $step = 'dashboard';
            $_SESSION['mfa_authenticated'] = true;
            $_SESSION['mfa_phone_verified'] = $phone;
        } else {
            $error = 'Invalid or expired verification code.';
            $step = 'verify';
        }
    }
}

// Start session for MFA
session_start();

// Check if already authenticated
if (isset($_SESSION['mfa_authenticated']) && $_SESSION['mfa_authenticated']) {
    $step = 'dashboard';
    $phone = $_SESSION['mfa_phone_verified'];
    
    // Get tickets
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
               a.name as asset_name, a.model as asset_model,
               sc.name as service_category_name
        FROM tickets t
        LEFT JOIN customers c ON t.customer_id = c.id
        LEFT JOIN assets a ON t.asset_id = a.id
        LEFT JOIN service_categories sc ON t.service_category_id = sc.id
        WHERE c.phone LIKE ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute(['%' . $phone . '%']);
    $tickets = $stmt->fetchAll();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /SupporTracker/portal/mfa-portal.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Portal - SupportTracker</title>
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
                    <p class="lead mb-0">Customer Portal</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <?php if ($step === 'phone'): ?>
        <!-- Phone Entry Step -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-phone me-2"></i>Access Your Repairs</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i><?= $success ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" 
                                       placeholder="(555) 123-4567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                <small class="text-muted">Enter the phone number on file with us</small>
                            </div>
                            
                            <button type="submit" name="send_code" class="btn btn-primary w-100">
                                <i class="bi bi-send me-1"></i>Send Verification Code
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        <div class="text-center">
                            <small class="text-muted">
                                Looking for a specific ticket? 
                                <a href="/SupporTracker/portal/">Individual ticket lookup</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php elseif ($step === 'verify'): ?>
        <!-- Verification Step -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-shield-check me-2"></i>Enter Verification Code</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Verification Code</label>
                                <input type="text" class="form-control text-center" name="code" 
                                       placeholder="123456" maxlength="6" required style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                                <small class="text-muted">Enter the 6-digit code</small>
                            </div>
                            
                            <button type="submit" name="verify_code" class="btn btn-success w-100">
                                <i class="bi bi-check-lg me-1"></i>Verify & Access Dashboard
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="/SupporTracker/portal/mfa-portal.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php elseif ($step === 'dashboard'): ?>
        <!-- Dashboard Step -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Your Repair Dashboard</h2>
                    <a href="?logout=1" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($tickets): ?>
        <div class="row">
            <?php foreach ($tickets as $ticket): ?>
            <div class="col-lg-6 mb-4">
                <div class="card status-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6><?= htmlspecialchars($ticket['title']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($ticket['asset_name'] . ' ' . $ticket['asset_model']) ?></small>
                            </div>
                            <span class="badge bg-<?= 
                                $ticket['status'] === 'resolved' ? 'success' : 
                                ($ticket['status'] === 'in_progress' ? 'warning' : 
                                ($ticket['status'] === 'waiting' ? 'info' : 'secondary')) 
                            ?>">
                                <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                            </span>
                        </div>
                        
                        <p class="small text-muted mb-2"><?= htmlspecialchars($ticket['description']) ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Ticket: <?= $ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?><br>
                                Created: <?= date('M j, Y', strtotime($ticket['created_at'])) ?>
                            </small>
                            <a href="/SupporTracker/portal/?ticket_id=<?= $ticket['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No Tickets Found</h5>
            <p class="text-muted">No repair tickets found for this phone number.</p>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>