-- Add MFA codes table for portal authentication
CREATE TABLE IF NOT EXISTS portal_mfa_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_phone_code (phone, code),
    INDEX idx_expires (expires_at)
);