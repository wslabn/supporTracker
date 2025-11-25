<?php
echo "Rewrite Test<br>";
echo "If you can see this at /SupporTracker/rewrite_test.php, then PHP files work<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";

if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "mod_rewrite is loaded<br>";
    } else {
        echo "mod_rewrite is NOT loaded<br>";
    }
} else {
    echo "Cannot check Apache modules<br>";
}
?>