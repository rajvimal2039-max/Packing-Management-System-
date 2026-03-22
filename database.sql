-- =====================================================
-- Packing Management System - Complete Database
-- =====================================================

DROP DATABASE IF EXISTS packing_db;
CREATE DATABASE packing_db;
USE packing_db;

-- =====================================================
-- 1. CREATE BRANCHES TABLE
-- =====================================================
CREATE TABLE branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    branch_code VARCHAR(50) NOT NULL UNIQUE,
    street VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 2. CREATE USERS TABLE
-- =====================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    branch_id INT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    type INT DEFAULT 2,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL
);

-- =====================================================
-- 3. CREATE CUSTOMERS TABLE
-- =====================================================
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(50) UNIQUE NOT NULL,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'USA',
    customer_type ENUM('individual', 'business') DEFAULT 'individual',
    company_name VARCHAR(200),
    tax_id VARCHAR(50),
    notes TEXT,
    total_parcels INT DEFAULT 0,
    total_spent DECIMAL(10,2) DEFAULT 0,
    status TINYINT DEFAULT 1,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =====================================================
-- 4. CREATE PARCELS TABLE
-- =====================================================
CREATE TABLE parcels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reference_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    sender_name VARCHAR(100) NOT NULL,
    sender_address TEXT NOT NULL,
    sender_contact VARCHAR(20) NOT NULL,
    recipient_name VARCHAR(100) NOT NULL,
    recipient_address TEXT NOT NULL,
    recipient_contact VARCHAR(20) NOT NULL,
    type INT DEFAULT 0,
    from_branch_id INT,
    to_branch_id INT,
    total DECIMAL(10,2) DEFAULT 0,
    status INT DEFAULT 0,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (from_branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    FOREIGN KEY (to_branch_id) REFERENCES branches(id) ON DELETE SET NULL
);

-- =====================================================
-- 5. CREATE PARCEL ITEMS TABLE
-- =====================================================
CREATE TABLE parcel_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parcel_id INT NOT NULL,
    weight DECIMAL(10,2) NOT NULL,
    height DECIMAL(10,2) NOT NULL,
    length DECIMAL(10,2) NOT NULL,
    width DECIMAL(10,2) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (parcel_id) REFERENCES parcels(id) ON DELETE CASCADE
);

-- =====================================================
-- 6. CREATE PARCEL TRACKING TABLE
-- =====================================================
CREATE TABLE parcel_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parcel_id INT NOT NULL,
    status INT DEFAULT 0,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parcel_id) REFERENCES parcels(id) ON DELETE CASCADE
);

-- =====================================================
-- 7. INSERT SAMPLE DATA
-- =====================================================

-- Insert branches
INSERT INTO branches (branch_code, street, city, state, zip_code, country, contact) VALUES
('NYC001', '123 Main St', 'New York', 'NY', '10001', 'USA', '2125550123'),
('LAX001', '456 Hollywood Blvd', 'Los Angeles', 'CA', '90028', 'USA', '3235550123'),
('CHI001', '789 Michigan Ave', 'Chicago', 'IL', '60601', 'USA', '3125550123');

-- Insert users (Admin and Staff)
INSERT INTO users (firstname, lastname, branch_id, email, password, contact, address, type) VALUES
('Admin', 'User', 1, 'admin@example.com', 'admin123', '5550001', '123 Admin St, New York, NY', 1),
('John', 'Doe', 1, 'john.doe@example.com', 'staff123', '5550002', '456 Staff Ave, New York, NY', 2),
('Jane', 'Smith', 2, 'jane.smith@example.com', 'staff123', '5550003', '789 Employee Rd, Los Angeles, CA', 2);

-- Insert customers
INSERT INTO customers (customer_code, firstname, lastname, email, phone, address, city, state, zip_code, country, created_by) VALUES
('CUST2024001', 'Alice', 'Johnson', 'alice@example.com', '5551001', '123 Sender St', 'New York', 'NY', '10001', 'USA', 1),
('CUST2024002', 'Bob', 'Williams', 'bob@example.com', '5552001', '456 Receiver Ave', 'Los Angeles', 'CA', '90028', 'USA', 1),
('CUST2024003', 'Charlie', 'Brown', 'charlie@example.com', '5551002', '789 Pine St', 'New York', 'NY', '10003', 'USA', 1),
('CUST2024004', 'David', 'Miller', 'david@example.com', '5551003', '456 Elm St', 'Los Angeles', 'CA', '90030', 'USA', 1),
('CUST2024005', 'Sarah', 'Wilson', 'sarah@example.com', '5552003', '654 Maple Dr', 'New York', 'NY', '10004', 'USA', 1);

-- Insert parcels (with your test data)
INSERT INTO parcels (reference_number, customer_id, sender_name, sender_address, sender_contact, recipient_name, recipient_address, recipient_contact, type, from_branch_id, to_branch_id, total, status, date_created) VALUES
('CMP2025001', 1, 'Alice Johnson', '123 Sender St, NY', '5551001', 'Bob Williams', '456 Receiver Ave, LA', '5552001', 1, 1, 2, 150.00, 7, NOW()),
('CMP2025002', 2, 'Bob Williams', '456 Receiver Ave, LA', '5552001', 'Alice Johnson', '123 Sender St, NY', '5551001', 0, 2, 1, 200.00, 3, NOW()),
('CMP202603189327', NULL, 'vimal', 'thiruvallur', '7305237272', 'raj', 'karanodai', '9962367887', 0, 2, 3, 80.00, 0, NOW()),
('CMP202603191810', NULL, 'raj', 'thiruvallur', '7305237272', 'vimal', 'karanodai', '9962367887', 0, 2, 3, 1000.00, 7, NOW()),
('CMP202603192437', NULL, 'raj', 'thiruvallur', '7305237272', 'vimal', 'karanodai', '9962367887', 0, 2, 3, 1000.00, 0, NOW());

-- Insert parcel items
INSERT INTO parcel_items (parcel_id, weight, height, length, width, price) VALUES
(1, 2.5, 10, 15, 8, 75.00),
(1, 1.8, 8, 12, 6, 75.00),
(2, 3.2, 12, 18, 10, 100.00),
(2, 2.0, 9, 14, 7, 100.00),
(3, 80.00, 5.2, 7.00, 4.00, 80.00),
(4, 80.00, 5.2, 7.00, 4.00, 1000.00),
(5, 80.00, 5.2, 7.00, 4.00, 1000.00);

-- Insert tracking records
INSERT INTO parcel_tracking (parcel_id, status, date_created) VALUES
(1, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(1, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(1, 2, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 3, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(1, 4, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 5, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 6, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 7, NOW()),
(2, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 2, NOW()),
(4, 0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 1, DATE_SUB(NOW(), INTERVAL 12 HOUR)),
(4, 2, DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(4, 3, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(4, 4, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(4, 5, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(4, 6, NOW()),
(4, 7, NOW());

-- =====================================================
-- 8. SHOW RESULTS
-- =====================================================
SELECT '✅ DATABASE COMPLETE - ALL TABLES CREATED' as 'STATUS';

SELECT 'branches' as 'Table', COUNT(*) as 'Records' FROM branches
UNION ALL SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'customers', COUNT(*) FROM customers
UNION ALL SELECT 'parcels', COUNT(*) FROM parcels
UNION ALL SELECT 'parcel_items', COUNT(*) FROM parcel_items
UNION ALL SELECT 'parcel_tracking', COUNT(*) FROM parcel_tracking;

-- =====================================================
-- 9. DEFAULT LOGIN CREDENTIALS
-- =====================================================
-- Admin:  admin@example.com / admin123
-- Staff:  john.doe@example.com / staff123
-- Staff:  jane.smith@example.com / staff123
-- =====================================================