CREATE DATABASE receipt_db CHARACTER SET utf8 COLLATE utf8_general_ci;
USE receipt_db;

CREATE TABLE receipts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(255),
  invoice_no VARCHAR(50),
  total DECIMAL(10,2)
);

CREATE TABLE receipt_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  receipt_id INT,
  description_th VARCHAR(255),
  description_en VARCHAR(255),
  qty INT,
  price DECIMAL(10,2),
  FOREIGN KEY (receipt_id) REFERENCES receipts(id)
);

-- ตัวอย่างข้อมูล
INSERT INTO receipts (customer_name, invoice_no, total)
VALUES ('John Doe', 'INV-2025-001', 2500.00);

INSERT INTO receipt_items (receipt_id, description_th, description_en, qty, price)
VALUES 
(1, 'สินค้า A', 'Product A', 2, 500.00),
(1, 'บริการ B', 'Service B', 1, 1500.00);
