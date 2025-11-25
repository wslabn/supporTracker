-- Work Order Tasks System
CREATE TABLE work_order_tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_order_id INT NOT NULL,
    description TEXT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE
);