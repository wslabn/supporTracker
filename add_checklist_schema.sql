-- Technician Checklist System
CREATE TABLE checklist_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('maintenance', 'troubleshooting', 'setup', 'security') DEFAULT 'maintenance',
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE checklist_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    item_text VARCHAR(500) NOT NULL,
    customer_note TEXT,
    sort_order INT DEFAULT 0,
    required BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (template_id) REFERENCES checklist_templates(id) ON DELETE CASCADE
);

CREATE TABLE work_order_checklists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_order_id INT NOT NULL,
    template_id INT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES checklist_templates(id)
);

CREATE TABLE work_order_checklist_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_order_checklist_id INT NOT NULL,
    checklist_item_id INT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (work_order_checklist_id) REFERENCES work_order_checklists(id) ON DELETE CASCADE,
    FOREIGN KEY (checklist_item_id) REFERENCES checklist_items(id)
);

-- Insert default maintenance checklist
INSERT INTO checklist_templates (name, description, category) VALUES 
('Standard PC Maintenance', 'Basic maintenance tasks for desktop/laptop computers', 'maintenance');

SET @template_id = LAST_INSERT_ID();

INSERT INTO checklist_items (template_id, item_text, customer_note, sort_order, required) VALUES
(@template_id, 'Run full antivirus scan', 'Performed comprehensive virus and malware scan - system clean', 1, TRUE),
(@template_id, 'Check for Windows updates', 'Installed latest Windows security and feature updates', 2, TRUE),
(@template_id, 'Clean temporary files and cache', 'Cleaned system temporary files to improve performance', 3, FALSE),
(@template_id, 'Check disk space and defragment if needed', 'Optimized disk storage and performance', 4, FALSE),
(@template_id, 'Update installed software', 'Updated critical software applications to latest versions', 5, TRUE),
(@template_id, 'Check system startup programs', 'Optimized system startup for faster boot times', 6, FALSE),
(@template_id, 'Test network connectivity', 'Verified network and internet connectivity functioning properly', 7, TRUE),
(@template_id, 'Backup important data', 'Ensured critical data is properly backed up', 8, TRUE),
(@template_id, 'Check hardware temperatures', 'Monitored system temperatures - all within normal ranges', 9, FALSE),
(@template_id, 'Test basic functionality', 'Verified all basic system functions operating normally', 10, TRUE);

-- Insert troubleshooting checklist
INSERT INTO checklist_templates (name, description, category) VALUES 
('System Troubleshooting', 'Standard troubleshooting steps for system issues', 'troubleshooting');

SET @template_id = LAST_INSERT_ID();

INSERT INTO checklist_items (template_id, item_text, customer_note, sort_order, required) VALUES
(@template_id, 'Document reported issue', 'Documented and investigated reported system issue', 1, TRUE),
(@template_id, 'Check event logs for errors', 'Reviewed system logs to identify potential causes', 2, TRUE),
(@template_id, 'Run system file checker (sfc /scannow)', 'Scanned and repaired system files', 3, FALSE),
(@template_id, 'Check hardware connections', 'Verified all hardware connections are secure', 4, FALSE),
(@template_id, 'Test in safe mode if needed', 'Tested system functionality in safe mode', 5, FALSE),
(@template_id, 'Update or reinstall drivers', 'Updated device drivers to resolve compatibility issues', 6, FALSE),
(@template_id, 'Run memory diagnostic', 'Performed memory test - no issues detected', 7, FALSE),
(@template_id, 'Check disk for errors', 'Scanned disk for errors and bad sectors', 8, FALSE),
(@template_id, 'Test issue resolution', 'Verified reported issue has been resolved', 9, TRUE),
(@template_id, 'Document solution', 'Documented solution for future reference', 10, TRUE);