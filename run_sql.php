<?php
require_once '/var/www/html/SupporTracker/config.php';

try {
    // Create locations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS locations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        address TEXT,
        phone VARCHAR(50),
        email VARCHAR(255),
        website VARCHAR(255),
        tax_rate DECIMAL(5,2),
        tax_id VARCHAR(100),
        payment_terms INT DEFAULT 30,
        logo_url TEXT,
        is_default BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Locations table created\n";

    // Add location_id to invoices
    $pdo->exec("ALTER TABLE invoices ADD COLUMN location_id INT AFTER company_id");
    echo "✓ Added location_id to invoices\n";

    // Add location_id to work_orders
    $pdo->exec("ALTER TABLE work_orders ADD COLUMN location_id INT AFTER company_id");
    echo "✓ Added location_id to work_orders\n";

    echo "\nDatabase updated successfully!\n";

} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✓ Columns already exist - no changes needed\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>