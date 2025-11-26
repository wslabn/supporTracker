<?php
echo "<script>console.log('TICKET CONTROLLER LOADED');</script>";
$ticket_id = $_GET['id'] ?? null;
if (!$ticket_id) {
    header('Location: /SupporTracker/portal/');
    exit;
}

// Handle credential form submission
if ($_POST && isset($_POST['add_credential'])) {
    echo "<script>console.log('CREDENTIAL FORM SUBMITTED');</script>";
    $asset_id = $_POST['asset_id'] ?? null;
    $credential_type = $_POST['credential_type'] ?? null;
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    $notes = $_POST['notes'] ?? null;
    
    if ($asset_id && $credential_type && $username && $password) {
        $stmt = $pdo->prepare("INSERT INTO asset_credentials (asset_id, credential_type, username, password, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$asset_id, $credential_type, $username, $password, $notes]);
        header("Location: /SupporTracker/portal/ticket?id=" . $ticket_id);
        exit;
    }
}

// Handle message sending
if ($_POST && isset($_POST['send_message']) && PORTAL_MESSAGING_ENABLED) {
    echo "<script>console.log('MESSAGE FORM SUBMITTED');</script>";
    $message = trim($_POST['message']);
    $customer_name = trim($_POST['customer_name']);
    
    if ($message && $customer_name) {
        $stmt = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, sender_type, sender_name, message) VALUES (?, 'customer', ?, ?)");
        $stmt->execute([$ticket_id, $customer_name, $message]);
        header("Location: /SupporTracker/portal/ticket?id=" . $ticket_id);
        exit;
    }
}

// Get ticket details
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

if (!$ticket) {
    header('Location: /SupporTracker/portal/');
    exit;
}

// Get location info
$location_stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
$location_stmt->execute([$ticket['location_id'] ?? 1]);
$location = $location_stmt->fetch();

// Get updates (non-internal only)
$stmt = $pdo->prepare("
    SELECT tu.*, u.name as technician_name
    FROM ticket_updates tu
    LEFT JOIN users u ON tu.technician_id = u.id
    WHERE tu.ticket_id = ? AND tu.is_internal = 0
    ORDER BY tu.created_at DESC
");
$stmt->execute([$ticket_id]);
$updates = $stmt->fetchAll();

// Get parts
$stmt = $pdo->prepare("
    SELECT description, status, sell_price
    FROM ticket_parts
    WHERE ticket_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$ticket_id]);
$parts = $stmt->fetchAll();

// Get messages and mark technician messages as read
$messages = [];
if (PORTAL_MESSAGING_ENABLED) {
    $stmt = $pdo->prepare("
        SELECT * FROM ticket_messages
        WHERE ticket_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$ticket_id]);
    $messages = $stmt->fetchAll();
    
    // Mark technician messages as read
    $pdo->prepare("UPDATE ticket_messages SET is_read = TRUE, read_at = NOW() WHERE ticket_id = ? AND sender_type = 'technician' AND is_read = FALSE")->execute([$ticket_id]);
}

// Get asset credentials if asset exists
$credentials = [];
if ($ticket['asset_id']) {
    $stmt = $pdo->prepare("SELECT * FROM asset_credentials WHERE asset_id = ? ORDER BY credential_type, created_at DESC");
    $stmt->execute([$ticket['asset_id']]);
    $credentials = $stmt->fetchAll();
}

echo "<script>console.log('RENDERING TICKET PAGE');</script>";
renderPortalPage('Repair Status - SupportTracker', 'ticket.php', compact('ticket', 'location', 'updates', 'parts', 'messages', 'credentials'));
?>
?>