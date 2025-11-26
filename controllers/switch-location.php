<?php
require_once 'config.php';

$locationId = $_GET['id'] ?? null;

if ($locationId) {
    // Verify user has access to this location
    $hasAccess = $pdo->prepare("SELECT l.name FROM locations l JOIN user_locations ul ON l.id = ul.location_id WHERE ul.user_id = ? AND l.id = ? AND l.is_active = 1");
    $hasAccess->execute([$_SESSION['user_id'], $locationId]);
    $location = $hasAccess->fetch();
    
    if ($location) {
        $_SESSION['current_location_id'] = $locationId;
        $_SESSION['current_location_name'] = $location['name'];
    }
}

// Redirect back to previous page or dashboard
$referer = $_SERVER['HTTP_REFERER'] ?? '/SupporTracker/dashboard';
header("Location: $referer");
exit;
?>