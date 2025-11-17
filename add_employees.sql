-- Create employees table
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    department VARCHAR(100),
    title VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Update assets table to use employee_id instead of assigned_to text
ALTER TABLE assets ADD COLUMN employee_id INT AFTER location;
ALTER TABLE assets ADD FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL;