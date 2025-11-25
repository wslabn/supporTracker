<?php
file_put_contents('/tmp/settings_debug.log', date('Y-m-d H:i:s') . ' - Method: ' . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
file_put_contents('/tmp/settings_debug.log', 'POST data: ' . print_r($_POST, true) . "\n", FILE_APPEND);

if ($_POST) {
    file_put_contents('/tmp/settings_debug.log', 'Processing POST data' . "\n", FILE_APPEND);
    if (isset($_POST['location_id'])) {
        // Update location
        $id = $_POST['location_id'];
        $stmt = $pdo->prepare("UPDATE locations SET name=?, address=?, phone=?, email=?, website=?, tax_rate=?, tax_id=?, payment_terms=?, logo_url=? WHERE id=?");
        $stmt->execute([
            $_POST['name'], $_POST['address'], $_POST['phone'], $_POST['email'], 
            $_POST['website'], $_POST['tax_rate'] ?: null, $_POST['tax_id'], 
            $_POST['payment_terms'], $_POST['logo_url'], $id
        ]);
    } else {
        // Create new location
        $stmt = $pdo->prepare("INSERT INTO locations (name, address, phone, email, website, tax_rate, tax_id, payment_terms, logo_url, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $is_default = $pdo->query("SELECT COUNT(*) FROM locations")->fetchColumn() == 0 ? 1 : 0;
        $result = $stmt->execute([
            $_POST['name'], $_POST['address'], $_POST['phone'], $_POST['email'],
            $_POST['website'], $_POST['tax_rate'] ?: null, $_POST['tax_id'],
            $_POST['payment_terms'], $_POST['logo_url'], $is_default
        ]);
        error_log('Insert result: ' . ($result ? 'SUCCESS' : 'FAILED') . ', Last insert ID: ' . $pdo->lastInsertId());
    }
    
    if (isset($_POST['set_default'])) {
        $pdo->query("UPDATE locations SET is_default = 0");
        $pdo->prepare("UPDATE locations SET is_default = 1 WHERE id = ?")->execute([$_POST['location_id'] ?? $pdo->lastInsertId()]);
    }
    
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    
    header('Location: /SupporTracker/settings?saved=1');
    exit;
}

// Get all locations
try {
    $stmt = $pdo->query("SELECT * FROM locations ORDER BY is_default DESC, name");
    $locations = $stmt->fetchAll();
    error_log('Locations loaded: ' . count($locations));
} catch (Exception $e) {
    error_log('Error loading locations: ' . $e->getMessage());
    $locations = [];
}

renderPage('Settings - SupportTracker', 'settings.php', compact('locations'));
?>