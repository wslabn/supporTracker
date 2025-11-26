-- Add priority response time settings
ALTER TABLE settings 
ADD COLUMN priority_low_hours INT DEFAULT 24,
ADD COLUMN priority_medium_hours INT DEFAULT 8,
ADD COLUMN priority_high_hours INT DEFAULT 2,
ADD COLUMN priority_urgent_hours INT DEFAULT 1;

-- Add location-specific priority overrides
ALTER TABLE locations
ADD COLUMN priority_low_hours INT NULL,
ADD COLUMN priority_medium_hours INT NULL,
ADD COLUMN priority_high_hours INT NULL,
ADD COLUMN priority_urgent_hours INT NULL;