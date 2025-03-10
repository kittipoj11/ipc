-- สร้างฐานข้อมูล (ถ้ายังไม่มี)
CREATE DATABASE IF NOT EXISTS file_storage;
USE file_storage;

-- สร้างตาราง records
CREATE TABLE IF NOT EXISTS records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    record_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- สร้างตาราง files
CREATE TABLE IF NOT EXISTS files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (record_id) REFERENCES records(record_id) ON DELETE CASCADE
);

-- ALTER TABLE files
-- DROP FOREIGN KEY fk_files_record_id;

-- ALTER TABLE files
-- ADD CONSTRAINT fk_files_record_id  -- (สามารถใช้ชื่อเดิม หรือตั้งชื่อใหม่ได้)
-- FOREIGN KEY (record_id)
-- REFERENCES records(record_id)
-- ON DELETE CASCADE;


-- DROP TABLE inspection_files;
-- CREATE TABLE `inspection_files` (
--   `file_id` int(11) NOT NULL AUTO_INCREMENT,
--   `inspection_id` int(10) UNSIGNED NOT NULL,
--   `file_name` varchar(255) NOT NULL,
--   `file_path` varchar(255) NOT NULL,
--   `file_type` varchar(100) NOT NULL,
--   `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
--   PRIMARY KEY (`file_id`),
--   FOREIGN KEY (inspection_id) REFERENCES inspection_periods(inspection_id) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;