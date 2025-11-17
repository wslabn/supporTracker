-- Add password fields directly to assets table
ALTER TABLE assets ADD COLUMN admin_username VARCHAR(255) AFTER notes;
ALTER TABLE assets ADD COLUMN admin_password_encrypted TEXT AFTER admin_username;
ALTER TABLE assets ADD COLUMN wifi_password_encrypted TEXT AFTER admin_password_encrypted;
ALTER TABLE assets ADD COLUMN other_passwords TEXT AFTER wifi_password_encrypted;