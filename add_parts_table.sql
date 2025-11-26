CREATE TABLE ticket_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    vendor VARCHAR(100),
    part_url TEXT,
    cost_paid DECIMAL(10,2),
    sell_price DECIMAL(10,2),
    status ENUM('quoted', 'ordered', 'received', 'installed') DEFAULT 'quoted',
    order_number VARCHAR(100),
    notes TEXT,
    added_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(id)
);