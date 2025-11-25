-- Add customer-facing fields to work orders
ALTER TABLE work_orders 
ADD COLUMN customer_summary TEXT AFTER description,
ADD COLUMN customer_notes TEXT AFTER customer_summary;

-- Add customer visibility flag to tasks
ALTER TABLE work_order_tasks 
ADD COLUMN customer_visible BOOLEAN DEFAULT FALSE AFTER notes;