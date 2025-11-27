-- Fix invoice number generation trigger
DELIMITER //
CREATE TRIGGER generate_invoice_number BEFORE INSERT ON invoices
FOR EACH ROW BEGIN
    DECLARE next_num INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED)), 0) + 1 INTO next_num FROM invoices WHERE invoice_number IS NOT NULL;
    SET NEW.invoice_number = CONCAT('INV-', LPAD(next_num, 6, '0'));
END//
DELIMITER ;

-- Update existing invoices with proper numbers
UPDATE invoices SET invoice_number = CONCAT('INV-', LPAD(id, 6, '0')) WHERE invoice_number IS NULL;