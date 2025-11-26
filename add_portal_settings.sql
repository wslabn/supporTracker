-- Add portal control settings to settings table
ALTER TABLE settings 
ADD COLUMN portal_enabled BOOLEAN DEFAULT TRUE,
ADD COLUMN customer_messaging_enabled BOOLEAN DEFAULT TRUE;