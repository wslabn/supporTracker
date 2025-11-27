<?php
require_once 'config.php';

$ticketId = $_GET['id'] ?? null;
if (!$ticketId) {
    header('Location: /SupporTracker/tickets');
    exit;
}

// Get ticket details first
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

if ($_POST) {
    if (isset($_POST['add_service_item'])) {
        $description = $_POST['service_type'] === 'custom' ? $_POST['custom_description'] : 
            str_replace('_', ' ', ucwords($_POST['service_type'], '_'));
        $price = (float)$_POST['price'];
        
        $stmt = $pdo->prepare("INSERT INTO ticket_billing_items (ticket_id, description, unit_price, total_price, item_type) VALUES (?, ?, ?, ?, 'service')");
        $stmt->execute([$ticketId, $description, $price, $price]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['add_labor_item'])) {
        $hours = (float)$_POST['hours'];
        $rate = (float)$_POST['rate'];
        $total = $hours * $rate;
        
        $stmt = $pdo->prepare("INSERT INTO ticket_billing_items (ticket_id, description, quantity, unit_price, total_price, item_type) VALUES (?, ?, ?, ?, ?, 'labor')");
        $stmt->execute([$ticketId, $_POST['description'], $hours, $rate, $total]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['delete_billing_item'])) {
        $stmt = $pdo->prepare("DELETE FROM ticket_billing_items WHERE id = ? AND ticket_id = ?");
        $stmt->execute([$_POST['delete_billing_item'], $ticketId]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['toggle_tax'])) {
        $itemType = $_POST['item_type'];
        $itemId = $_POST['item_id'];
        
        if ($itemType === 'service') {
            // Toggle taxable status for billing items
            $stmt = $pdo->prepare("UPDATE ticket_billing_items SET taxable = NOT COALESCE(taxable, 1) WHERE id = ? AND ticket_id = ?");
            $stmt->execute([$itemId, $ticketId]);
        } elseif ($itemType === 'part') {
            // Toggle taxable status for parts
            $stmt = $pdo->prepare("UPDATE ticket_parts SET taxable = NOT COALESCE(taxable, 1) WHERE id = ? AND ticket_id = ?");
            $stmt->execute([$itemId, $ticketId]);
        }
        
        // Return success for AJAX
        http_response_code(200);
        exit;
    }
    
    if (isset($_POST['update_item_field'])) {
        $itemType = $_POST['item_type'];
        $itemId = $_POST['item_id'];
        $field = $_POST['field'];
        $value = $_POST['value'];
        
        if ($itemType === 'service') {
            if ($field === 'price') {
                $stmt = $pdo->prepare("UPDATE ticket_billing_items SET unit_price = ?, total_price = unit_price * quantity WHERE id = ? AND ticket_id = ?");
                $stmt->execute([$value, $itemId, $ticketId]);
            } elseif ($field === 'discount') {
                $stmt = $pdo->prepare("UPDATE ticket_billing_items SET discount = ? WHERE id = ? AND ticket_id = ?");
                $stmt->execute([$value, $itemId, $ticketId]);
            }
        } elseif ($itemType === 'part') {
            if ($field === 'price') {
                $stmt = $pdo->prepare("UPDATE ticket_parts SET sell_price = ? WHERE id = ? AND ticket_id = ?");
                $stmt->execute([$value, $itemId, $ticketId]);
            } elseif ($field === 'discount') {
                $stmt = $pdo->prepare("UPDATE ticket_parts SET discount = ? WHERE id = ? AND ticket_id = ?");
                $stmt->execute([$value, $itemId, $ticketId]);
            }
        }
        
        http_response_code(200);
        exit;
    }
    
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
        
        // Get part details for work log
        $part = $pdo->prepare("SELECT description FROM ticket_parts WHERE id = ?");
        $part->execute([$part_id]);
        $part = $part->fetch();
        
        $stmt = $pdo->prepare("UPDATE ticket_parts SET status = ? WHERE id = ? AND ticket_id = ?");
        $stmt->execute([$new_status, $part_id, $ticketId]);
        
        // Add work log entry for status changes
        if ($part) {
            $statusMessages = [
                'quoted' => 'Quoted part: ',
                'ordered' => 'Ordered part: ',
                'received' => 'Received part: ',
                'installed' => 'Installed part: '
            ];
            
            $hours = $new_status === 'installed' ? 0.25 : null;
            $message = ($statusMessages[$new_status] ?? 'Updated part status to ' . $new_status . ': ') . $part['description'];
            
            $stmt = $pdo->prepare("INSERT INTO ticket_updates (ticket_id, technician_id, update_type, content, hours_logged) VALUES (?, ?, 'note', ?, ?)");
            $stmt->execute([
                $ticketId,
                $_SESSION['user_id'],
                $message,
                $hours
            ]);
        }
        
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
    
    if (isset($_POST['add_ticket_credential'])) {
        if ($ticket['asset_id']) {
            $stmt = $pdo->prepare("INSERT INTO asset_credentials (asset_id, credential_type, service_name, username, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $ticket['asset_id'],
                $_POST['credential_type'],
                $_POST['service_name'],
                $_POST['username'],
                $_POST['password']
            ]);
        }
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['delete_credential'])) {
        $credential_id = (int)$_POST['delete_credential'];
        $stmt = $pdo->prepare("DELETE FROM asset_credentials WHERE id = ?");
        $stmt->execute([$credential_id]);
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['update_asset_specs'])) {
        if ($ticket['asset_id']) {
            // Update asset specifications
            $stmt = $pdo->prepare("
                UPDATE assets SET 
                    name = ?, model = ?, serial_number = ?, 
                    operating_system = ?, cpu = ?, ram_gb = ?, 
                    storage_gb = ?, graphics_card = ?, network_card = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['asset_name'],
                $_POST['model'],
                $_POST['serial_number'],
                $_POST['operating_system'],
                $_POST['cpu'],
                $_POST['ram_gb'] ?: null,
                $_POST['storage_gb'] ?: null,
                $_POST['graphics_card'],
                $_POST['network_card'],
                $ticket['asset_id']
            ]);
            
            // Add work log entry
            $stmt = $pdo->prepare("INSERT INTO ticket_updates (ticket_id, technician_id, update_type, content, hours_logged) VALUES (?, ?, 'note', ?, ?)");
            $stmt->execute([
                $ticketId,
                $_SESSION['user_id'],
                "Asset specifications updated: " . $_POST['update_notes'],
                $_POST['hours_logged'] ?: null
            ]);
        }
        header("Location: /SupporTracker/ticket-detail?id=" . $ticketId);
        exit;
    }
    
    if (isset($_POST['create_invoice'])) {
        try {
            // Create invoice from ticket (trigger will generate invoice_number)
            $stmt = $pdo->prepare("INSERT INTO invoices (customer_id, issue_date, due_date, status) VALUES (?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'draft')");
            $stmt->execute([$ticket['customer_id']]);
            $invoiceId = $pdo->lastInsertId();
            
            // Add billing items as invoice items
            $billingItems = $pdo->prepare("SELECT * FROM ticket_billing_items WHERE ticket_id = ?");
            $billingItems->execute([$ticketId]);
            $items = $billingItems->fetchAll();
            
            foreach ($items as $item) {
                $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, ticket_id, description, quantity, rate, amount) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $invoiceId,
                    $ticketId,
                    $item['description'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['total_price']
                ]);
            }
            
            // Add parts as invoice items
            $partsStmt = $pdo->prepare("SELECT * FROM ticket_parts WHERE ticket_id = ? AND sell_price > 0");
            $partsStmt->execute([$ticketId]);
            $parts = $partsStmt->fetchAll();
            
            foreach ($parts as $part) {
                $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, ticket_id, description, quantity, rate, amount) VALUES (?, ?, ?, 1, ?, ?)");
                $stmt->execute([
                    $invoiceId,
                    $ticketId,
                    $part['description'],
                    $part['sell_price'],
                    $part['sell_price']
                ]);
            }
            
            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['total_price'];
            }
            foreach ($parts as $part) {
                $subtotal += $part['sell_price'];
            }
            
            // Update invoice totals
            $stmt = $pdo->prepare("UPDATE invoices SET subtotal = ?, total = ? WHERE id = ?");
            $stmt->execute([$subtotal, $subtotal, $invoiceId]);
            
            // Update ticket status to closed
            $stmt = $pdo->prepare("UPDATE tickets SET status = 'closed' WHERE id = ?");
            $stmt->execute([$ticketId]);
            
            // Add work log entry
            $stmt = $pdo->prepare("INSERT INTO ticket_updates (ticket_id, technician_id, update_type, content) VALUES (?, ?, 'status_change', ?)");
            $stmt->execute([
                $ticketId,
                $_SESSION['user_id'],
                "Invoice created and ticket marked as closed"
            ]);
            
            header("Location: /SupporTracker/invoices");
            exit;
        } catch (Exception $e) {
            die("Invoice creation error: " . $e->getMessage());
        }
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
        
        // Update ticket priority if provided
        if ($_POST['new_priority']) {
            $stmt = $pdo->prepare("UPDATE tickets SET priority = ? WHERE id = ?");
            $stmt->execute([$_POST['new_priority'], $ticketId]);
        }
        
        header("Location: /SupporTracker/ticket-detail?id=$ticketId");
        exit;
    }
}

// Ticket already loaded above

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

// Get asset credentials if ticket has an asset
$assetCredentials = [];
$assetDetails = [];
if ($ticket['asset_id']) {
    $assetCredentials = $pdo->prepare("
        SELECT * FROM asset_credentials
        WHERE asset_id = ?
        ORDER BY credential_type, service_name
    ");
    $assetCredentials->execute([$ticket['asset_id']]);
    $assetCredentials = $assetCredentials->fetchAll();
    
    // Get full asset details for the update form
    $assetDetails = $pdo->prepare("
        SELECT * FROM assets WHERE id = ?
    ");
    $assetDetails->execute([$ticket['asset_id']]);
    $assetDetails = $assetDetails->fetch() ?: [];
}

// Get billing items for this ticket
$billingItems = $pdo->prepare("
    SELECT * FROM ticket_billing_items
    WHERE ticket_id = ?
    ORDER BY created_at
");
$billingItems->execute([$ticketId]);
$billingItems = $billingItems->fetchAll();

// Get service pricing from settings
$servicePrices = [];
try {
    $pricing = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'price_%'")->fetchAll();
    foreach ($pricing as $price) {
        $key = str_replace('price_', '', $price['setting_key']);
        $servicePrices[$key] = $price['setting_value'];
    }
} catch (Exception $e) {
    // No pricing set yet
}

renderModernPage(
    'Ticket Details - SupportTracker',
    'Ticket #' . ($ticket['ticket_number'] ?? 'TKT-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT)),
    'ticket-detail.php',
    compact('ticket', 'updates', 'parts', 'messages', 'messagingEnabled', 'assetCredentials', 'assetDetails', 'billingItems', 'servicePrices'),
    ''
);
?>