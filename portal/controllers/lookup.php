<?php
echo "<script>console.log('LOOKUP CONTROLLER LOADED');</script>";
$error = '';

if ($_POST) {
    echo "<script>console.log('LOOKUP FORM SUBMITTED');</script>";
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
        
        if ($ticket) {
            echo "<script>console.log('TICKET FOUND, REDIRECTING:', {$ticket['id']});</script>";
            header("Location: /SupporTracker/portal/ticket?id=" . $ticket['id']);
            exit;
        } else {
            echo "<script>console.log('TICKET NOT FOUND');</script>";
            $error = 'Ticket not found. Please check your ticket number and phone number.';
        }
    } else {
        echo "<script>console.log('MISSING FIELDS');</script>";
        $error = 'Please enter both ticket number and phone number.';
    }
}

// Get company info from settings
$company_name = 'SupportTracker';
$company_logo = null;
try {
    $settings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('company_name', 'company_logo_url')")->fetchAll();
    foreach ($settings as $setting) {
        if ($setting['setting_key'] === 'company_name') $company_name = $setting['setting_value'];
        if ($setting['setting_key'] === 'company_logo_url') $company_logo = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Settings table issue, use defaults
}

echo "<script>console.log('RENDERING LOOKUP PAGE');</script>";
renderPortalPage('Check Repair Status - SupportTracker', 'lookup.php', compact('error', 'company_name', 'company_logo'));
?>