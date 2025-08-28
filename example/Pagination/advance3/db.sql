CREATE DATABASE file_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE file_demo;

CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_type ENUM('image','pdf') NOT NULL,
    file_path VARCHAR(255) NOT NULL
);

-- ตัวอย่างข้อมูล
INSERT INTO files (title, description, file_type, file_path) VALUES
('รูปสินค้า A', 'ภาพถ่ายสินค้า', 'image', 'uploads/productA.jpg'),
('รายงานผลการขาย Q1', 'เอกสารสรุปยอดขาย', 'pdf', 'uploads/salesQ1.pdf'),
('รูปสินค้า B', 'สินค้าอีกตัว', 'image', 'uploads/productB.png'),
('รายงานการเงิน Q2', 'ไฟล์ PDF ตัวอย่าง', 'pdf', 'uploads/financeQ2.pdf');
