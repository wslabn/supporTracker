<?php
require_once 'config/database.php';

try {
    // Add task_name field
    $pdo->exec("ALTER TABLE work_order_tasks ADD COLUMN task_name VARCHAR(255) NOT NULL AFTER work_order_id");
    echo "Added task_name field\n";
    
    // Rename description to notes
    $pdo->exec("ALTER TABLE work_order_tasks CHANGE COLUMN description notes TEXT");
    echo "Renamed description to notes\n";
    
    // Update existing tasks
    $pdo->exec("UPDATE work_order_tasks SET task_name = LEFT(notes, 100) WHERE task_name = ''");
    echo "Updated existing tasks\n";
    
    echo "Schema update completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>