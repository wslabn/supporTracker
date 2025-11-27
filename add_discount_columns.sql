-- Add discount columns for pricing flexibility
USE supporttracker;

ALTER TABLE ticket_billing_items ADD COLUMN discount DECIMAL(5,2) DEFAULT 0;
ALTER TABLE ticket_parts ADD COLUMN discount DECIMAL(5,2) DEFAULT 0;