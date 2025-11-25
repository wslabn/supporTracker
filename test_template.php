<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'includes/template.php';

echo "Config loaded<br>";

$companies = [
    ['id' => 1, 'name' => 'Test Company', 'monthly_rate' => 125.00, 'email' => 'test@test.com', 'phone' => '555-1234', 'employee_count' => 5, 'asset_count' => 10]
];

echo "Data prepared<br>";

renderPage(
    'Test - SupportTracker',
    'companies.php',
    compact('companies')
);
?>