<?php
echo "Router test started<br>";

try {
    require_once 'config.php';
    echo "Config loaded<br>";
    
    require_once 'includes/template.php';
    echo "Template loaded<br>";
    
    require_once 'includes/router.php';
    echo "Router loaded<br>";
    
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>