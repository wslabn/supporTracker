-- Expand Asset Categories for Software and Services (Fixed)
USE supporttracker;

-- Add more asset categories for software and services
INSERT INTO asset_categories (name, description, icon, color) VALUES
('Software Licenses', 'Desktop applications and software licenses', 'bi bi-app', '#17a2b8'),
('Cloud Services', 'SaaS applications and cloud subscriptions', 'bi bi-cloud', '#28a745'),
('Security Software', 'Antivirus, firewalls, and security tools', 'bi bi-shield-check', '#dc3545'),
('Productivity Software', 'Office suites, email clients, collaboration tools', 'bi bi-briefcase', '#6f42c1'),
('Specialized Software', 'Industry-specific applications (QuickBooks, CAD, etc.)', 'bi bi-tools', '#fd7e14'),
('Web Services', 'Hosting, domains, and web-related services', 'bi bi-globe', '#20c997'),
('Communication Services', 'Phone systems, VoIP, messaging services', 'bi bi-telephone', '#ffc107'),
('Backup & Storage', 'Backup solutions and cloud storage services', 'bi bi-hdd-stack', '#6c757d');

-- Update assets table to support software/service specific fields
ALTER TABLE assets 
ADD COLUMN license_key VARCHAR(255) AFTER serial_number,
ADD COLUMN license_type ENUM('per_user', 'per_device', 'site_license', 'subscription') AFTER license_key,
ADD COLUMN vendor VARCHAR(100) AFTER model,
ADD COLUMN version VARCHAR(50) AFTER vendor,
ADD COLUMN expiration_date DATE AFTER version,
ADD COLUMN monthly_cost DECIMAL(10,2) AFTER expiration_date,
ADD COLUMN renewal_frequency ENUM('monthly', 'quarterly', 'yearly', 'one_time') DEFAULT 'one_time' AFTER monthly_cost;