<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=supporttracker', 'supporttracker', 'supporttracker123');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Settings in database:\n";
    $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_key");
    while ($row = $stmt->fetch()) {
        echo $row['setting_key'] . " = " . $row['setting_value'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>