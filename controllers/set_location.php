<?php
if ($_POST && isset($_POST['location_id'])) {
    $_SESSION['current_location'] = $_POST['location_id'] ?: null;
}
echo json_encode(['success' => true]);
?>