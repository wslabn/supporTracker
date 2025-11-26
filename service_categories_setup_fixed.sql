-- Service Categories Setup (Fixed)
USE supporttracker;

-- Create service categories table
CREATE TABLE service_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('ticket', 'project', 'both') DEFAULT 'both',
    description TEXT,
    icon VARCHAR(50) DEFAULT 'bi bi-gear',
    color VARCHAR(7) DEFAULT '#6c757d',
    default_priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    sla_hours INT DEFAULT 24,
    billable_default BOOLEAN DEFAULT 1,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default service categories
INSERT INTO service_categories (name, type, description, icon, color, default_priority, sla_hours) VALUES
('Hardware Support', 'both', 'Computer repairs, equipment issues, hardware troubleshooting', 'bi bi-cpu', '#dc3545', 'high', 4),
('Software Support', 'both', 'Application issues, software installation, license problems', 'bi bi-app-indicator', '#0d6efd', 'medium', 8),
('Network Issues', 'both', 'Internet connectivity, network configuration, WiFi problems', 'bi bi-wifi', '#6f42c1', 'high', 2),
('Security & Compliance', 'both', 'Antivirus, firewall, security audits, compliance requirements', 'bi bi-shield-check', '#dc3545', 'urgent', 1),
('Email & Communication', 'both', 'Email setup, phone systems, communication tools', 'bi bi-envelope', '#198754', 'medium', 6),
('Web Services', 'both', 'Website issues, hosting, domain management, web applications', 'bi bi-globe', '#20c997', 'medium', 12),
('Backup & Recovery', 'both', 'Data backup, disaster recovery, file restoration', 'bi bi-hdd-stack', '#fd7e14', 'high', 4),
('User Management', 'ticket', 'Account creation, password resets, permissions, training', 'bi bi-person-gear', '#6c757d', 'low', 24),
('System Migration', 'project', 'Server migrations, office moves, system upgrades', 'bi bi-arrow-repeat', '#ffc107', 'medium', 48),
('New Installation', 'project', 'New system setup, equipment installation, initial configuration', 'bi bi-plus-circle', '#198754', 'medium', 24),
('Maintenance', 'both', 'Routine maintenance, updates, preventive care', 'bi bi-tools', '#6c757d', 'low', 72),
('Consultation', 'both', 'Planning, advice, system design, recommendations', 'bi bi-chat-dots', '#17a2b8', 'low', 48);

-- Add service category to tickets table (no DROP needed)
ALTER TABLE tickets 
ADD COLUMN service_category_id INT AFTER asset_id,
ADD FOREIGN KEY (service_category_id) REFERENCES service_categories(id) ON DELETE SET NULL;

-- Add service category to projects table
ALTER TABLE projects
ADD COLUMN service_category_id INT AFTER customer_id,
ADD FOREIGN KEY (service_category_id) REFERENCES service_categories(id) ON DELETE SET NULL;