-- Phase 2: Projects and Parts Management Schema

-- Projects table
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    budget DECIMAL(10,2) DEFAULT 0.00,
    start_date DATE,
    end_date DATE,
    estimated_hours DECIMAL(8,2),
    actual_hours DECIMAL(8,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Add project_id to work_orders table
ALTER TABLE work_orders ADD COLUMN project_id INT AFTER company_id;
ALTER TABLE work_orders ADD FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL;

-- Parts and equipment orders
CREATE TABLE parts_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    project_id INT,
    work_order_id INT,
    asset_id INT,
    description VARCHAR(255) NOT NULL,
    part_number VARCHAR(100),
    quantity DECIMAL(8,2) NOT NULL DEFAULT 1,
    unit_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    markup_percent DECIMAL(5,2) DEFAULT 0.00,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- what client pays
    total_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- quantity * unit_cost
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- quantity * unit_price
    vendor VARCHAR(255),
    order_date DATE,
    expected_date DATE,
    received_date DATE,
    status ENUM('pending', 'ordered', 'shipped', 'received', 'installed') DEFAULT 'pending',
    billable BOOLEAN DEFAULT TRUE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE SET NULL
);

-- Project status history for tracking
CREATE TABLE project_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    notes TEXT,
    changed_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);