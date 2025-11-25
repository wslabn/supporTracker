-- Add vendor URL field to parts_orders table
ALTER TABLE parts_orders ADD COLUMN vendor_url TEXT AFTER vendor;