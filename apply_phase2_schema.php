<?php
require_once 'config.php';

try {
    // Read and execute schema
    $schema = file_get_contents('phase2_schema.sql');
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
            echo "✓ Executed: " . substr($statement, 0, 50) . "...<br>";
        }
    }
    
    echo "<br><strong>Phase 2 schema applied successfully!</strong><br>";
    echo "Added: Projects table, Parts orders table, Project status history<br>";
    echo "Modified: Work orders table (added project_id)<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>