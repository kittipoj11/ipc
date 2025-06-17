-- ---------------------------------
-- -- 1. สร้างตาราง (TABLES) --
-- ---------------------------------
CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name_unique` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_unique` (`username`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `menu_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(100) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(50) NOT NULL,
  `order_num` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`parent_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `role_menu_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `menu_item_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `menu_item_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- -- 2. ใส่ข้อมูลตัวอย่าง (SAMPLE DATA) --
-- ---------------------------------
INSERT INTO `roles` (`id`, `role_name`) VALUES (1, 'System Admin'), (2, 'Editor');

INSERT INTO `users` (`id`, `username`, `password`, `role_id`) VALUES
(1, 'admin', 'admin123', 1),
(2, 'editor', 'editor456', 2);

INSERT INTO `menu_items` (`id`, `parent_id`, `title`, `url`, `icon`, `order_num`) VALUES
(1, NULL, 'แดชบอร์ด', '/dashboard.php', 'fa-solid fa-house', 1),
(2, NULL, 'จัดการบทความ', '#', 'fa-solid fa-file-pen', 2),
(3, 2, 'บทความทั้งหมด', '/posts/list.php', 'fa-solid fa-list', 1),
(4, 2, 'เขียนบทความใหม่', '/posts/add.php', 'fa-solid fa-plus', 2),
(5, NULL, 'จัดการผู้ใช้', '/users/list.php', 'fa-solid fa-users', 3),
(6, NULL, 'ตั้งค่าระบบ', '/settings.php', 'fa-solid fa-gears', 4);

INSERT INTO `role_menu_permissions` (`role_id`, `menu_item_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
(2, 1), (2, 2), (2, 3), (2, 4);