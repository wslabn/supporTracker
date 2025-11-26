-- Phase 3: Billing System Schema

-- Invoice management
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    company_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    paid_amount DECIMAL(10,2) DEFAULT 0.00,
    balance_due DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Invoice line items
CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    work_order_id INT NULL,
    description TEXT NOT NULL,
    quantity DECIMAL(8,2) DEFAULT 1.00,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    item_type ENUM('labor', 'parts', 'contract', 'other') DEFAULT 'labor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL
);

-- Payment tracking
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    company_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('check', 'cash', 'credit_card', 'bank_transfer', 'online') DEFAULT 'check',
    reference_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Monthly contract billing
CREATE TABLE contract_billing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    billing_period_start DATE NOT NULL,
    billing_period_end DATE NOT NULL,
    monthly_rate DECIMAL(10,2) NOT NULL,
    invoice_id INT NULL,
    status ENUM('pending', 'invoiced', 'paid') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL
);

-- Add billing fields to work_orders if not exists
ALTER TABLE work_orders 
ADD COLUMN invoiced BOOLEAN DEFAULT FALSE,
ADD COLUMN invoice_id INT NULL,
ADD FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL;

-- Add billing fields to parts_orders if not exists  
ALTER TABLE parts_orders
ADD COLUMN invoiced BOOLEAN DEFAULT FALSE,
ADD COLUMN invoice_id INT NULL,
ADD FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL;