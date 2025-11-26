-- Add company branding settings
INSERT INTO settings (setting_key, setting_value) VALUES 
('company_name', 'SupportTracker'),
('company_logo', NULL)
ON DUPLICATE KEY UPDATE 
setting_key = VALUES(setting_key);