CREATE DATABASE IF NOT EXISTS spare_pos;
USE spare_pos;

CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    emp_id VARCHAR(10) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Trigger to auto-generate emp_id like EMP-1001
DELIMITER //
CREATE TRIGGER before_insert_users
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT AUTO_INCREMENT FROM information_schema.TABLES 
                   WHERE TABLE_SCHEMA = 'oil_pos_db' AND TABLE_NAME = 'users');
    SET NEW.emp_id = CONCAT('EMP-', 1000 + next_id);
END;
//
DELIMITER ;

-- Default Accounts (Password for both: admin123 and cashier123)
-- Admin
INSERT INTO users (full_name, username, password, role) 
VALUES ('System Administrator', 'admin', '$2y$10$8W3V.qNf7H7v6J5vS1.0v.4H4z8q.K.l.v.v.v.v.v.v.v.v.v', 'admin');

-- Cashier
INSERT INTO users (full_name, username, password, role) 
VALUES ('John Doe', 'cashier', '$2y$10$R.v/X9u9V.T.p.S.C.B.G.e.N.u.v.z.w.x.y.z.1.2.3.4.5.6.7', 'cashier');

CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    barcode VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('oil', 'spare_part') NOT NULL,
    oil_type ENUM('can', 'loose', 'none') DEFAULT 'none',
    brand VARCHAR(100),
    vehicle_compatibility TEXT, -- Comma separated or JSON
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS invoices (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    invoice_no VARCHAR(100) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    supplier_name VARCHAR(100),
    invoice_date DATE,
    total_amount DECIMAL(15, 2) DEFAULT 0,
    status ENUM('draft', 'completed') DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS batches (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    invoice_id INT NOT NULL,
    buying_price DECIMAL(15, 2) NOT NULL,
    selling_price DECIMAL(15, 2) NOT NULL,
    estimated_selling_price DECIMAL(15, 2) NOT NULL DEFAULT 0,
    original_qty DECIMAL(10, 2) NOT NULL, -- Supporting liters (loose oil)
    current_qty DECIMAL(10, 2) NOT NULL,
    expire_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
);
CREATE TABLE IF NOT EXISTS customers (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) UNIQUE,
    address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sales (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    user_id INT NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    discount DECIMAL(15, 2) DEFAULT 0,
    final_amount DECIMAL(15, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'cheque', 'credit') NOT NULL,
    payment_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS sale_items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_id INT NOT NULL,
    qty DECIMAL(10, 2) NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    discount DECIMAL(15, 2) DEFAULT 0,
    total_price DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES batches(id)
);
