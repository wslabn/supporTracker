<?php
require_once 'config.php';

try {
    $pdo->exec("ALTER TABLE employees ADD COLUMN cell_phone VARCHAR(50) AFTER phone");
    echo "Cell phone column added successfully!";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Cell phone column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>