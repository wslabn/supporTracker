-- Expand update_type column to accommodate 'priority_change'
ALTER TABLE ticket_updates MODIFY COLUMN update_type VARCHAR(20) NOT NULL;