<?php
echo "Debug Info:<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'not set') . "<br>";

// Test if mod_rewrite is working
if (file_exists('.htaccess')) {
    echo ".htaccess exists<br>";
    echo "Contents:<br><pre>" . file_get_contents('.htaccess') . "</pre>";
} else {
    echo ".htaccess NOT found<br>";
}

// Test router
require_once 'config.php';
require_once 'includes/template.php';
require_once 'includes/router.php';

echo "Router loaded successfully<br>";
?>