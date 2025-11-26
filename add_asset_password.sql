-- Add password field to assets table for device access credentials
ALTER TABLE assets ADD COLUMN device_password VARCHAR(255) NULL AFTER notes;