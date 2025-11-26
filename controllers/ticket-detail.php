<?php
require_once 'config.php';

$ticketId = $_GET['id'] ?? null;
if (!$ticketId) {
    header('Location: /SupporTracker/tickets');
    exit;
}

if ($_POST) {
    if (isset($_POST['send_message'])) {
        $stmt = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, sender_type, sender_name, message) VALUES (?, 'technician', ?, ?)");
        $stmt->execute([
            $ticketId,
            $_SESSION['user_name'],
            $_POST['message']
        ]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['delete_update'])) {
        $update_id = (int)$_POST['delete_update'];
        $stmt = $pdo->prepare("DELETE FROM ticket_updates WHERE id = ? AND ticket_id = ?");
        $stmt->execute([$update_id, $ticketId]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['add_part'])) {
        $stmt = $pdo->prepare("INSERT INTO ticket_parts (ticket_id, description, vendor, part_url, cost_paid, sell_price, status, order_number, notes, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $ticketId,
            $_POST['description'],
            $_POST['vendor'] ?: null,
            $_POST['part_url'] ?: null,
            $_POST['cost_paid'] ?: null,
            $_POST['sell_price'] ?: null,
            $_POST['status'],
            $_POST['order_number'] ?: null,
            $_POST['notes'] ?: null,
            $_SESSION['user_id']
        ]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['update_part_status'])) {
        $part_id = (int)$_POST['part_id'];
        $new_status = $_POST['new_status'];
        $stmt = $pdo->prepare("UPDATE ticket_parts SET status = ? WHERE id = ? AND ticket_id = ?");
        $stmt->execute([$new_status, $part_id, $ticketId]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['update_part'])) {
        $part_id = (int)$_POST['update_part'];
        $stmt = $pdo->prepare("UPDATE ticket_parts SET cost_paid = ?, sell_price = ?, order_number = ? WHERE id = ? AND ticket_id = ?");
        $stmt->execute([
            $_POST['cost_paid'] ?: null,
            $_POST['sell_price'] ?: null,
            $_POST['order_number'] ?: null,
            $part_id,
            $ticketId
        ]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['add_update'])) {
        $stmt = $pdo->prepare("INSERT INTO ticket_updates (ticket_id, technician_id, update_type, content, hours_logged, is_internal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $ticketId,
            $_SESSION['user_id'],
            $_POST['update_type'],
            $_POST['content'],
            $_POST['hours_logged'] ?: null,
            isset($_POST['is_internal']) ? 1 : 0
        ]);
        
        // Update ticket status if provided
        if ($_POST['new_status']) {
            $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            $stmt->execute([$_POST['new_status'], $ticketId]);
        }
        
        header("Location: /SupporTracker/ticket-detail?id=$ticketId");
        exit;
    }
}

// Get ticket details
$ticket = $pdo->prepare("
    SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
           a.name as asset_name, a.model as asset_model, a.serial_number,
           u.name as technician_name,
           sc.name as service_category_name, sc.sla_hours
    FROM tickets t
    LEFT JOIN customers c ON t.customer_id = c.id
    LEFT JOIN assets a ON t.asset_id = a.id
    LEFT JOIN users u ON t.assigned_to = u.id
    LEFT JOIN service_categories sc ON t.service_category_id = sc.id
    WHERE t.id = ?
");
$ticket->execute([$ticketId]);
$ticket = $ticket->fetch();

if (!$ticket) {
    header('Location: /SupporTracker/tickets');
    exit;
}

// Get ticket updates
$updates = $pdo->prepare("
    SELECT tu.*, u.name as technician_name
    FROM ticket_updates tu
    LEFT JOIN users u ON tu.technician_id = u.id
    WHERE tu.ticket_id = ?
    ORDER BY tu.created_at DESC
");
$updates->execute([$ticketId]);
$updates = $updates->fetchAll();

// Get ticket parts
$parts = $pdo->prepare("
    SELECT tp.*, u.name as added_by_name
    FROM ticket_parts tp
    LEFT JOIN users u ON tp.added_by = u.id
    WHERE tp.ticket_id = ?
    ORDER BY tp.created_at DESC
");
$parts->execute([$ticketId]);
$parts = $parts->fetchAll();

// Check if messaging is enabled
$messagingEnabled = false;
try {
    $settings = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'customer_messaging_enabled'")->fetch();
    $messagingEnabled = $settings && $settings['setting_value'] == '1';
} catch (Exception $e) {
    // Settings table doesn't exist
}

// Get messages if messaging enabled
$messages = [];
if ($messagingEnabled) {
    $messages = $pdo->prepare("
        SELECT * FROM ticket_messages
        WHERE ticket_id = ?
        ORDER BY created_at DESC
    ");
    $messages->execute([$ticketId]);
    $messages = $messages->fetchAll();
    
    // Mark customer messages as read when technician views
    $pdo->prepare("UPDATE ticket_messages SET is_read = TRUE, read_at = NOW() WHERE ticket_id = ? AND sender_type = 'customer' AND is_read = FALSE")->execute([$ticketId]);
}

renderModernPage(
    'Ticket Details - SupportTracker',
    'Ticket #' . ($ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT)),
    'ticket-detail.php',
    compact('ticket', 'updates', 'parts', 'messages', 'messagingEnabled'),
    ''
);
?>