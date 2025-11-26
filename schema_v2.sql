-- SupportTracker v2.0 - Clean Modern Schema
USE supporttracker;

-- Core entities
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('business', 'individual') DEFAULT 'business',
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    billing_address TEXT,
    tax_id VARCHAR(100),
    notes TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE customer_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(50),
    department VARCHAR(100),
    is_primary BOOLEAN DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    contact_id INT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('laptop', 'desktop', 'server', 'printer', 'phone', 'tablet', 'network', 'other') NOT NULL,
    make VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    asset_tag VARCHAR(50),
    location VARCHAR(255),
    purchase_date DATE,
    warranty_expires DATE,
    notes TEXT,
    status ENUM('active', 'retired', 'repair') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES customer_contacts(id) ON DELETE SET NULL
);

CREATE TABLE technicians (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(50),
    role ENUM('admin', 'manager', 'technician') DEFAULT 'technician',
    hourly_rate DECIMAL(10,2) DEFAULT 75.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    password_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    contact_id INT NULL,
    asset_id INT NULL,
    assigned_to INT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'waiting_customer', 'waiting_parts', 'resolved', 'closed') DEFAULT 'open',
    category ENUM('hardware', 'software', 'network', 'security', 'maintenance', 'other') DEFAULT 'other',
    billable BOOLEAN DEFAULT 1,
    estimated_hours DECIMAL(5,2),
    actual_hours DECIMAL(5,2),
    hourly_rate DECIMAL(10,2) DEFAULT 75.00,
    resolution TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES customer_contacts(id) ON DELETE SET NULL,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES technicians(id) ON DELETE SET NULL
);

CREATE TABLE ticket_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    technician_id INT NOT NULL,
    update_type ENUM('note', 'status_change', 'assignment', 'time_log') NOT NULL,
    content TEXT,
    hours_logged DECIMAL(5,2),
    is_internal BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES technicians(id) ON DELETE CASCADE
);

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    subtotal DECIMAL(10,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) DEFAULT 0,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    ticket_id INT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE SET NULL
);

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(255),
    tax_rate DECIMAL(5,2) DEFAULT 0,
    tax_id VARCHAR(100),
    is_default BOOLEAN DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    start_date DATE,
    due_date DATE,
    completed_date DATE,
    budget DECIMAL(10,2),
    actual_cost DECIMAL(10,2) DEFAULT 0,
    project_manager_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (project_manager_id) REFERENCES technicians(id) ON DELETE SET NULL
);

-- Add location_id to relevant tables
ALTER TABLE customers ADD COLUMN location_id INT AFTER type,
    ADD FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL;

ALTER TABLE tickets ADD COLUMN location_id INT AFTER assigned_to,
    ADD COLUMN project_id INT AFTER location_id,
    ADD FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    ADD FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL;

ALTER TABLE technicians ADD COLUMN location_id INT AFTER role,
    ADD FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL;

-- Insert default data
INSERT INTO locations (name, is_default) VALUES ('Main Office', 1);

INSERT INTO technicians (name, email, role, password_hash, location_id) VALUES 
('Admin User', 'admin@supporttracker.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Auto-generate ticket numbers
DELIMITER //
CREATE TRIGGER generate_ticket_number BEFORE INSERT ON tickets
FOR EACH ROW BEGIN
    DECLARE next_num INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(ticket_number, 4) AS UNSIGNED)), 0) + 1 INTO next_num FROM tickets;
    SET NEW.ticket_number = CONCAT('TKT', LPAD(next_num, 6, '0'));
END//

CREATE TRIGGER generate_invoice_number BEFORE INSERT ON invoices
FOR EACH ROW BEGIN
    DECLARE next_num INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED)), 0) + 1 INTO next_num FROM invoices;
    SET NEW.invoice_number = CONCAT('INV-', LPAD(next_num, 6, '0'));
END//
DELIMITER ;