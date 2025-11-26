-- Create asset credentials table for multiple passwords per asset
CREATE TABLE asset_credentials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    credential_type ENUM('device', 'email', 'software', 'network', 'cloud', 'other') DEFAULT 'other',
    service_name VARCHAR(100) NOT NULL,
    username VARCHAR(255),
    password VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);