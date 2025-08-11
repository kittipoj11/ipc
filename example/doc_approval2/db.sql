-- ตารางผู้ใช้
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `role` VARCHAR(50) NOT NULL
);

-- ตารางประเภทเอกสาร
CREATE TABLE `document_types` (
  `type_id` VARCHAR(10) PRIMARY KEY,
  `description` VARCHAR(255)
);

-- ตารางขั้นตอนการอนุมัติ (Workflow)
CREATE TABLE `workflow_steps` (
  `step_id` INT AUTO_INCREMENT PRIMARY KEY,
  `doc_type_id` VARCHAR(10),
  `step_number` INT NOT NULL,
  `approver_user_id` INT NOT NULL,
  FOREIGN KEY (`doc_type_id`) REFERENCES `document_types`(`type_id`),
  FOREIGN KEY (`approver_user_id`) REFERENCES `users`(`user_id`)
);

-- ตารางเอกสารหลัก
CREATE TABLE `documents` (
  `doc_id` INT AUTO_INCREMENT PRIMARY KEY,
  `doc_type_id` VARCHAR(10),
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Draft',
  `creator_id` INT,
  `current_step` INT DEFAULT 0,
  `current_approver_id` INT NULL,
  `source_doc_id` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`doc_type_id`) REFERENCES `document_types`(`type_id`),
  FOREIGN KEY (`creator_id`) REFERENCES `users`(`user_id`),
  FOREIGN KEY (`current_approver_id`) REFERENCES `users`(`user_id`)
);

-- ตารางประวัติการดำเนินการ (History Log)
CREATE TABLE `document_history` (
  `history_id` INT AUTO_INCREMENT PRIMARY KEY,
  `doc_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `comments` TEXT NULL,
  `action_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`doc_id`) REFERENCES `documents`(`doc_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

-- ข้อมูลตัวอย่าง (Sample Data)
INSERT INTO `users` (`username`, `role`) VALUES
('admin', 'admin'), ('approver1', 'approver'), ('approver2', 'approver'), ('final_approver_A', 'approver'),
('approver_B1', 'approver'), ('approver_B2', 'approver'), ('approver_B3', 'approver'), ('approver_B4', 'approver'), ('final_approver_B', 'approver');

INSERT INTO `document_types` (`type_id`, `description`) VALUES
('DOC_A', 'เอกสารประเภท A'), ('DOC_B', 'เอกสารประเภท B');

INSERT INTO `workflow_steps` (`doc_type_id`, `step_number`, `approver_user_id`) VALUES
('DOC_A', 1, 2), ('DOC_A', 2, 3), ('DOC_A', 3, 4),
('DOC_B', 1, 5), ('DOC_B', 2, 6), ('DOC_B', 3, 7), ('DOC_B', 4, 8), ('DOC_B', 5, 9);