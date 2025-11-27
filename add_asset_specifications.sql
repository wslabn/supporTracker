-- Add asset specification columns for hardware tracking
USE supporttracker;

ALTER TABLE assets 
ADD COLUMN operating_system VARCHAR(100) AFTER notes,
ADD COLUMN cpu VARCHAR(255) AFTER operating_system,
ADD COLUMN ram_gb INT AFTER cpu,
ADD COLUMN storage_gb INT AFTER ram_gb,
ADD COLUMN graphics_card VARCHAR(255) AFTER storage_gb,
ADD COLUMN network_card VARCHAR(255) AFTER graphics_card;