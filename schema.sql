-- SupportTracker Database Schema
-- MSP Customer Management System

-- Companies (your billing customers)
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    monthly_rate DECIMAL(10,2) DEFAULT 0.00,
    contract_start DATE,
    contract_end DATE,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    billing_contact VARCHAR(255),
    technical_contact VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Asset categories for organization
CREATE TABLE asset_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Assets (equipment managed per company)
CREATE TABLE assets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    make VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    asset_tag VARCHAR(50),
    location VARCHAR(255),
    status ENUM('active', 'inactive', 'repair', 'retired') DEFAULT 'active',
    purchase_date DATE,
    warranty_end DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES asset_categories(id)
);

-- Secure storage for asset passwords and sensitive info
CREATE TABLE asset_credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asset_id INT NOT NULL,
    credential_type VARCHAR(100) NOT NULL,
    username VARCHAR(255),
    password_encrypted TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);

-- Work orders/tickets
CREATE TABLE work_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    asset_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('new', 'in_progress', 'waiting_parts', 'waiting_customer', 'completed', 'closed') DEFAULT 'new',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    billable BOOLEAN DEFAULT TRUE,
    estimated_hours DECIMAL(5,2),
    actual_hours DECIMAL(5,2),
    hourly_rate DECIMAL(8,2),
    parts_cost DECIMAL(10,2) DEFAULT 0.00,
    labor_cost DECIMAL(10,2) DEFAULT 0.00,
    total_cost DECIMAL(10,2) DEFAULT 0.00,
    created_by VARCHAR(100),
    assigned_to VARCHAR(100),
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE SET NULL
);

-- Twilio services per company
CREATE TABLE twilio_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    phone_number VARCHAR(20),
    service_type ENUM('voice', 'sms', 'trunk', 'number') DEFAULT 'voice',
    monthly_cost DECIMAL(8,2) DEFAULT 0.00,
    usage_cost DECIMAL(8,2) DEFAULT 0.00,
    markup_rate DECIMAL(5,2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Invoices
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax_rate DECIMAL(5,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(10,2) DEFAULT 0.00,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Invoice line items
CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    work_order_id INT,
    description TEXT NOT NULL,
    quantity DECIMAL(8,2) DEFAULT 1.00,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    item_type ENUM('labor', 'parts', 'service', 'monthly_fee', 'twilio') DEFAULT 'labor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL
);

-- Payments received
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('check', 'stripe', 'paypal', 'cash', 'ach') DEFAULT 'check',
    reference_number VARCHAR(100),
    payment_date DATE NOT NULL,
    deposit_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Payment allocations to invoices (oldest first)
CREATE TABLE payment_allocations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    invoice_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- Customer portal users
CREATE TABLE portal_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    last_login TIMESTAMP NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Insert default asset categories
INSERT INTO asset_categories (name, description) VALUES
('Computers', 'Desktop and laptop computers'),
('Servers', 'Physical and virtual servers'),
('Printers', 'Printers, scanners, and multifunction devices'),
('Network Equipment', 'Routers, switches, access points, firewalls'),
('Phones', 'VoIP phones, analog phones, conference phones'),
('Mobile Devices', 'Tablets, smartphones, mobile hotspots'),
('Other', 'Miscellaneous equipment');