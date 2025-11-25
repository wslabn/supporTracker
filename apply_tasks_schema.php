<?php
require_once 'config.php';

try {
    $sql = file_get_contents('add_tasks_schema.sql');
    $pdo->exec($sql);
    echo "✓ Work order tasks table created successfully!";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "✓ Work order tasks table already exists";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>