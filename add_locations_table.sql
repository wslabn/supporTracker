CREATE TABLE IF NOT EXISTS locations (
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
);

ALTER TABLE invoices ADD COLUMN location_id INT AFTER company_id;
ALTER TABLE invoices ADD FOREIGN KEY (location_id) REFERENCES locations(id);ALTER TABLE work_orders ADD COLUMN location_id INT AFTER company_id;
