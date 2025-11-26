<?php
// Setup script for SupportTracker v2
$host = 'localhost';
$user = 'supporttracker';
$pass = '3Ga55ociates1nc!';

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute schema
    $sql = file_get_contents('schema_v2.sql');
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✓ Database created successfully!\n";
    echo "✓ Default admin user: admin@supporttracker.com / password\n";
    echo "✓ Default location: Main Office\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>