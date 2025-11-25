<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "POST Debug Test\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";

if ($_POST) {
    echo "POST data received:\n";
    print_r($_POST);
} else {
    echo "No POST data\n";
}

if (isset($_POST['add_asset'])) {
    echo "add_asset detected\n";
    
    // Try database connection
    try {
        require_once 'config.php';
        echo "Config loaded successfully\n";
        echo "PDO object exists: " . (isset($pdo) ? 'YES' : 'NO') . "\n";
        
        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM assets");
            $count = $stmt->fetchColumn();
            echo "Current asset count: $count\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>