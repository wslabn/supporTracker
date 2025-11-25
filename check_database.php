<?php
require_once 'config.php';

try {
    echo "<h3>Database Schema Check</h3>";
    
    // Check if new tables exist
    $tables_to_check = ['projects', 'parts_orders', 'project_status_history'];
    
    foreach ($tables_to_check as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists<br>";
            
            // Show column count
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->rowCount();
            echo "&nbsp;&nbsp;→ $columns columns<br>";
        } else {
            echo "✗ Table '$table' missing<br>";
        }
    }
    
    // Check if work_orders has project_id column
    $stmt = $pdo->query("DESCRIBE work_orders");
    $columns = $stmt->fetchAll();
    $has_project_id = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'project_id') {
            $has_project_id = true;
            break;
        }
    }
    
    if ($has_project_id) {
        echo "✓ work_orders table has project_id column<br>";
    } else {
        echo "✗ work_orders table missing project_id column<br>";
    }
    
    echo "<br><strong>Phase 2 Schema Status: " . 
         (count($tables_to_check) === 3 && $has_project_id ? "✓ Complete" : "⚠ Incomplete") . 
         "</strong>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>