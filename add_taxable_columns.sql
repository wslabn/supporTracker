-- Add taxable columns for billing control
USE supporttracker;

ALTER TABLE ticket_billing_items ADD COLUMN taxable BOOLEAN DEFAULT TRUE;
ALTER TABLE ticket_parts ADD COLUMN taxable BOOLEAN DEFAULT TRUE;