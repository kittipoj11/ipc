CREATE DATABASE test_db;
USE test_db;

CREATE TABLE items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100),
  description TEXT
);

-- ข้อมูลตัวอย่าง
INSERT INTO items (title, description) VALUES
('Item 1', 'รายละเอียดของ Item 1'),
('Item 2', 'รายละเอียดของ Item 2'),
('Item 3', 'รายละเอียดของ Item 3'),
('Item 4', 'รายละเอียดของ Item 4'),
('Item 5', 'รายละเอียดของ Item 5'),
('Item 6', 'รายละเอียดของ Item 6'),
('Item 7', 'รายละเอียดของ Item 7'),
('Item 8', 'รายละเอียดของ Item 8'),
('Item 9', 'รายละเอียดของ Item 9'),
('Item 10', 'รายละเอียดของ Item 10');

ALTER TABLE items ADD image VARCHAR(255);

UPDATE items SET image = 'images/item1.jpg' WHERE id = 1;
UPDATE items SET image = 'images/item2.jpg' WHERE id = 2;
UPDATE items SET image = 'images/item3.jpg' WHERE id = 3;
UPDATE items SET image = 'images/item4.jpg' WHERE id = 4;
UPDATE items SET image = 'images/item5.jpg' WHERE id = 5;
-- (ใส่รูปอื่น ๆ ตามต้องการ)
