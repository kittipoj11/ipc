-- ตัวอย่างคำสั่ง SQL สำหรับสร้างตาราง (ถ้ายังไม่มี)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- ตัวอย่างการเพิ่มข้อมูล
INSERT INTO users (name) VALUES ('สมชาย ใจดี');
INSERT INTO users (name) VALUES ('สมหญิง น่ารัก');