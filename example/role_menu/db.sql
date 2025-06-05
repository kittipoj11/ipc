-- 1. ตาราง Roles
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- 2. ตาราง Pages
CREATE TABLE pages (
    page_id INT AUTO_INCREMENT PRIMARY KEY,
    page_filename VARCHAR(100) NOT NULL UNIQUE, -- เช่น A.php, B.php
    menu_title VARCHAR(100) NOT NULL           -- ชื่อที่จะแสดงบนเมนู
);

-- 3. ตาราง Users (สมมติว่ามีระบบ login อยู่แล้ว)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- ควรเก็บ password ที่ hash แล้ว
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- 4. ตาราง Role Permissions (ตารางเชื่อม)
CREATE TABLE role_permissions (
    role_id INT,
    page_id INT,
    PRIMARY KEY (role_id, page_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (page_id) REFERENCES pages(page_id) ON DELETE CASCADE
);

-- -- ข้อมูลตัวอย่าง --

-- Roles
INSERT INTO roles (role_name) VALUES ('admin'), ('user'), ('management');

-- Pages
INSERT INTO pages (page_filename, menu_title) VALUES
('A.php', 'Page A (Admin Only)'),
('B.php', 'Page B (Admin & User)'),
('C.php', 'Page C (All Roles)');

-- Users (ตัวอย่างง่ายๆ password คือชื่อ user)
-- ในระบบจริง password ต้อง hash เช่น password_hash('admin_password', PASSWORD_DEFAULT)
INSERT INTO users (username, password_hash, role_id) VALUES
('admin01', 'hashed_admin_pass', (SELECT role_id FROM roles WHERE role_name = 'admin')),
('user01', 'hashed_user_pass', (SELECT role_id FROM roles WHERE role_name = 'user')),
('manager01', 'hashed_manager_pass', (SELECT role_id FROM roles WHERE role_name = 'management'));

-- Permissions
-- Admin (เข้าได้ทุกหน้า)
INSERT INTO role_permissions (role_id, page_id) VALUES
((SELECT role_id FROM roles WHERE role_name = 'admin'), (SELECT page_id FROM pages WHERE page_filename = 'A.php')),
((SELECT role_id FROM roles WHERE role_name = 'admin'), (SELECT page_id FROM pages WHERE page_filename = 'B.php')),
((SELECT role_id FROM roles WHERE role_name = 'admin'), (SELECT page_id FROM pages WHERE page_filename = 'C.php'));

-- User (เข้า B.php, C.php)
INSERT INTO role_permissions (role_id, page_id) VALUES
((SELECT role_id FROM roles WHERE role_name = 'user'), (SELECT page_id FROM pages WHERE page_filename = 'B.php')),
((SELECT role_id FROM roles WHERE role_name = 'user'), (SELECT page_id FROM pages WHERE page_filename = 'C.php'));

-- Management (เข้า C.php)
INSERT INTO role_permissions (role_id, page_id) VALUES
((SELECT role_id FROM roles WHERE role_name = 'management'), (SELECT page_id FROM pages WHERE page_filename = 'C.php'));