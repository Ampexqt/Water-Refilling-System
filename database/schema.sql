-- Database: water_refilling_system
-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS water_refilling_system;
CREATE DATABASE water_refilling_system;
USE water_refilling_system;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Customers Table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    total_orders INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    container_size ENUM('5-gallon', '3-gallon', '1-gallon') NOT NULL,
    quantity INT NOT NULL,
    delivery_date DATE,
    delivery_time TIME,
    notes TEXT,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Deliveries Table
CREATE TABLE deliveries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    customer_id INT NOT NULL,
    delivery_address TEXT NOT NULL,
    items VARCHAR(255) NOT NULL,
    scheduled_date DATE NOT NULL,
    scheduled_time TIME NOT NULL,
    status ENUM('pending', 'in-transit', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    delivered_at DATETIME,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- POS Transactions Table
CREATE TABLE pos_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(255),
    items JSON NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'gcash') DEFAULT 'cash',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Insert Default Admin and Cashier Users
-- Password for both: password123
INSERT INTO users (name, email, password, role, status) VALUES
('Admin User', 'admin@aquaflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
('Cashier User', 'cashier@aquaflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier', 'active');

-- Insert Sample Customers
INSERT INTO customers (name, phone, address, status, total_orders) VALUES
('Juan Dela Cruz', '09171234567', '123 Main St, Manila', 'active', 15),
('Maria Santos', '09181234567', '456 Oak Ave, Quezon City', 'active', 8),
('Pedro Reyes', '09191234567', '789 Pine Rd, Makati', 'active', 12),
('Ana Garcia', '09201234567', '321 Elm St, Pasig', 'active', 5);

-- Insert Sample Orders
INSERT INTO orders (customer_id, container_size, quantity, delivery_date, delivery_time, notes, status, created_by) VALUES
(1, '5-gallon', 3, '2025-11-28', '10:00:00', 'Please call before delivery', 'pending', 1),
(2, '3-gallon', 5, '2025-11-28', '14:00:00', NULL, 'processing', 1),
(3, '5-gallon', 2, '2025-11-27', '09:00:00', 'Leave at gate', 'completed', 1),
(4, '1-gallon', 10, '2025-11-29', '11:00:00', NULL, 'pending', 2);

-- Insert Sample Deliveries
INSERT INTO deliveries (order_id, customer_id, delivery_address, items, scheduled_date, scheduled_time, status, notes, created_by) VALUES
(1, 1, '123 Main St, Manila', '3x 5-gallon', '2025-11-28', '10:00:00', 'pending', 'Please call before delivery', 1),
(2, 2, '456 Oak Ave, Quezon City', '5x 3-gallon', '2025-11-28', '14:00:00', 'in-transit', NULL, 1),
(3, 3, '789 Pine Rd, Makati', '2x 5-gallon', '2025-11-27', '09:00:00', 'completed', 'Leave at gate', 1);
