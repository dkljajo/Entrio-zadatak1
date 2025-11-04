CREATE DATABASE enrio_tickets CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE enrio_tickets;

CREATE TABLE ticket_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quota INT NOT NULL,
    sold INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_type_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(id)
);

INSERT INTO ticket_types (name, price, quota)
VALUES 
('Early Bird', 40.00, 100),
('Regular', 60.00, 200),
('VIP', 120.00, 50);
