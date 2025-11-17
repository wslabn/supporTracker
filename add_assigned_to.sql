-- Add assigned_to column to assets table
ALTER TABLE assets ADD COLUMN assigned_to VARCHAR(255) AFTER location;