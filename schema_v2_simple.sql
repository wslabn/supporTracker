-- SupportTracker v2.0 Database Schema (Simplified)
DROP DATABASE IF EXISTS supporttracker;
CREATE DATABASE supporttracker;
USE supporttracker;

-- Locations table
CREATE TABLE locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    tax_rate DECIMAL(5,4) DEFAULT 0.0000,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type ENUM('business', 'individual') DEFAULT 'business',
    company_name VARCHAR(100),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    monthly_rate DECIMAL(10,2) DEFAULT 0.00,
    location_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

-- Customer contacts table
CREATE TABLE customer_contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Assets table
CREATE TABLE assets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    serial_number VARCHAR(100),
    model VARCHAR(100),
    location VARCHAR(100),
    status ENUM('active', 'inactive', 'retired') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Technicians table
CREATE TABLE technicians (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    hourly_rate DECIMAL(10,2) DEFAULT 75.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tickets table with auto-increment number
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_number VARCHAR(20) UNIQUE,
    customer_id INT NOT NULL,
    asset_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('open', 'in_progress', 'waiting', 'resolved', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    assigned_to INT,
    billable BOOLEAN DEFAULT TRUE,
    estimated_hours DECIMAL(5,2),
    actual_hours DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (assigned_to) REFERENCES technicians(id)
);

-- Projects table
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
    start_date DATE,
    end_date DATE,
    budget DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Project tickets relationship
CREATE TABLE project_tickets (
    project_id INT,
    ticket_id INT,
    PRIMARY KEY (project_id, ticket_id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
);

-- Invoices table with auto-increment number
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(20) UNIQUE,
    customer_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Invoice items table
CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    ticket_id INT,
    description TEXT NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 1.00,
    rate DECIMAL(10,2) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);

-- Insert default data
INSERT INTO locations (name, address, is_default) VALUES 
('Main Office', '123 Main St, City, State 12345', TRUE);

INSERT INTO technicians (name, email, hourly_rate) VALUES 
('Admin User', 'admin@company.com', 75.00);

-- Create triggers for auto-numbering (simplified version)
DELIMITER //

CREATE TRIGGER ticket_number_trigger 
BEFORE INSERT ON tickets 
FOR EACH ROW 
BEGIN 
    DECLARE next_num INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(ticket_number, 4) AS UNSIGNED)), 0) + 1 INTO next_num FROM tickets;
    SET NEW.ticket_number = CONCAT('TKT', LPAD(next_num, 6, '0'));
END//

CREATE TRIGGER invoice_number_trigger 
BEFORE INSERT ON invoices 
FOR EACH ROW 
BEGIN 
    DECLARE next_num INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED)), 0) + 1 INTO next_num FROM invoices;
    SET NEW.invoice_number = CONCAT('INV-', LPAD(next_num, 6, '0'));
END//

DELIMITER ;