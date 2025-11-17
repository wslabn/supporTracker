-- Remove old password columns from assets
ALTER TABLE assets DROP COLUMN admin_username;
ALTER TABLE assets DROP COLUMN admin_password_encrypted;
ALTER TABLE assets DROP COLUMN wifi_password_encrypted;
ALTER TABLE assets DROP COLUMN other_passwords;

-- Create credential categories table
CREATE TABLE credential_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create flexible credentials table
CREATE TABLE asset_credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asset_id INT NOT NULL,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    username VARCHAR(255),
    password_encrypted TEXT,
    pin_encrypted VARCHAR(255),
    url VARCHAR(500),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES credential_categories(id) ON DELETE SET NULL
);

-- Insert default credential categories
INSERT INTO credential_categories (name, description) VALUES
('Administrator Account', 'Local admin or root accounts'),
('WiFi/Network', 'WiFi passwords, network keys'),
('Software/Applications', 'Application logins, software licenses'),
('Web Services', 'Online accounts, cloud services'),
('Security/Access', 'PIN codes, security codes, key cards'),
('Remote Access', 'RDP, SSH, VPN credentials'),
('Other', 'Miscellaneous credentials');