-- Asset Categories Setup
USE supporttracker;

-- Create asset categories table
CREATE TABLE asset_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'bi bi-box',
    color VARCHAR(7) DEFAULT '#6c757d',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add category_id to assets table
ALTER TABLE assets 
ADD COLUMN category_id INT AFTER customer_id,
ADD FOREIGN KEY (category_id) REFERENCES asset_categories(id) ON DELETE SET NULL;

-- Insert default categories
INSERT INTO asset_categories (name, description, icon, color) VALUES
('Laptops', 'Portable computers and notebooks', 'bi bi-laptop', '#0d6efd'),
('Desktops', 'Desktop computers and workstations', 'bi bi-pc-display', '#198754'),
('Servers', 'Server hardware and infrastructure', 'bi bi-server', '#dc3545'),
('Printers', 'Printing devices and scanners', 'bi bi-printer', '#ffc107'),
('Phones', 'Business phones and communication devices', 'bi bi-phone', '#fd7e14'),
('Network Equipment', 'Routers, switches, and network hardware', 'bi bi-router', '#6f42c1'),
('Mobile Devices', 'Tablets and mobile devices', 'bi bi-tablet', '#20c997'),
('Other Equipment', 'Miscellaneous IT equipment', 'bi bi-gear', '#6c757d');

-- Update existing assets to use categories (map old type enum to new categories)
UPDATE assets SET category_id = 1 WHERE type = 'laptop';
UPDATE assets SET category_id = 2 WHERE type = 'desktop';
UPDATE assets SET category_id = 3 WHERE type = 'server';
UPDATE assets SET category_id = 4 WHERE type = 'printer';
UPDATE assets SET category_id = 5 WHERE type = 'phone';
UPDATE assets SET category_id = 7 WHERE type = 'tablet';
UPDATE assets SET category_id = 6 WHERE type = 'network';
UPDATE assets SET category_id = 8 WHERE type = 'other';