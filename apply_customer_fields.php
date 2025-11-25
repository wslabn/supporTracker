<?php
require_once 'config.php';

try {
    // Add customer fields to work orders
    $pdo->exec("ALTER TABLE work_orders ADD COLUMN customer_summary TEXT AFTER description");
    echo "Added customer_summary field\n";
    
    $pdo->exec("ALTER TABLE work_orders ADD COLUMN customer_notes TEXT AFTER customer_summary");
    echo "Added customer_notes field\n";
    
    // Add customer visibility to tasks
    $pdo->exec("ALTER TABLE work_order_tasks ADD COLUMN customer_visible BOOLEAN DEFAULT FALSE AFTER notes");
    echo "Added customer_visible field to tasks\n";
    
    echo "Customer fields added successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>