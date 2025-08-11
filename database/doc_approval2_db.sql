-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 12:08 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doc_approval2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `doc_type_id` varchar(10) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Draft',
  `creator_id` int(11) DEFAULT NULL,
  `current_step` int(11) DEFAULT 0,
  `current_approver_id` int(11) DEFAULT NULL,
  `source_doc_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`doc_id`, `doc_type_id`, `title`, `content`, `status`, `creator_id`, `current_step`, `current_approver_id`, `source_doc_id`, `created_at`, `updated_at`) VALUES
(1, 'DOC_A', 'New Document Aทดสอบ', 'Initial content.ทดสอบ', 'Completed', 1, 3, NULL, NULL, '2025-08-11 04:12:30', '2025-08-11 04:15:24'),
(2, 'DOC_B', 'Doc B from A#1', 'Initial content.ทดสอบ', 'Pending', 1, 1, 5, 1, '2025-08-11 04:15:24', '2025-08-11 04:15:24'),
(3, 'DOC_A', 'New Document A ID3', 'Initial content.ID3', 'Pending', 1, 1, 2, NULL, '2025-08-11 06:58:11', '2025-08-11 07:02:57'),
(4, 'DOC_A', 'New Document A', 'Initial content.', 'Draft', 1, 0, NULL, NULL, '2025-08-11 06:58:18', '2025-08-11 06:58:18'),
(5, 'DOC_A', 'New Document A', 'Initial content.', 'Draft', 1, 0, NULL, NULL, '2025-08-11 06:58:52', '2025-08-11 06:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `document_history`
--

CREATE TABLE `document_history` (
  `history_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `comments` text DEFAULT NULL,
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `document_history`
--

INSERT INTO `document_history` (`history_id`, `doc_id`, `user_id`, `action`, `comments`, `action_timestamp`) VALUES
(1, 1, 1, 'Document Created', '', '2025-08-11 04:12:30'),
(2, 1, 1, 'Document Updated', '', '2025-08-11 04:13:31'),
(3, 1, 1, 'Submitted for Approval', '', '2025-08-11 04:13:43'),
(4, 1, 2, 'Rejected at Step 1', 'ข้อมูลผิด', '2025-08-11 04:14:19'),
(5, 1, 1, 'Submitted for Approval', '', '2025-08-11 04:14:33'),
(6, 1, 2, 'Approved at Step 1', '', '2025-08-11 04:14:56'),
(7, 1, 3, 'Approved at Step 2', '', '2025-08-11 04:15:09'),
(8, 1, 4, 'Final Approved. Status: Completed', '', '2025-08-11 04:15:24'),
(9, 2, 4, 'Document B auto-created from A#1', '', '2025-08-11 04:15:24'),
(10, 3, 1, 'Document Created', '', '2025-08-11 06:58:11'),
(11, 4, 1, 'Document Created', '', '2025-08-11 06:58:18'),
(12, 5, 1, 'Document Created', '', '2025-08-11 06:58:52'),
(13, 3, 1, 'Document Updated', '', '2025-08-11 07:02:51'),
(14, 3, 1, 'Submitted for Approval', '', '2025-08-11 07:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `type_id` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`type_id`, `description`) VALUES
('DOC_A', 'เอกสารประเภท A'),
('DOC_B', 'เอกสารประเภท B');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `role`) VALUES
(1, 'admin', 'admin'),
(2, 'approver1', 'approver'),
(3, 'approver2', 'approver'),
(4, 'final_approver_A', 'approver'),
(5, 'approver_B1', 'approver'),
(6, 'approver_B2', 'approver'),
(7, 'approver_B3', 'approver'),
(8, 'approver_B4', 'approver'),
(9, 'final_approver_B', 'approver');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_steps`
--

CREATE TABLE `workflow_steps` (
  `step_id` int(11) NOT NULL,
  `doc_type_id` varchar(10) DEFAULT NULL,
  `step_number` int(11) NOT NULL,
  `approver_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `workflow_steps`
--

INSERT INTO `workflow_steps` (`step_id`, `doc_type_id`, `step_number`, `approver_user_id`) VALUES
(1, 'DOC_A', 1, 2),
(2, 'DOC_A', 2, 3),
(3, 'DOC_A', 3, 4),
(4, 'DOC_B', 1, 5),
(5, 'DOC_B', 2, 6),
(6, 'DOC_B', 3, 7),
(7, 'DOC_B', 4, 8),
(8, 'DOC_B', 5, 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `doc_type_id` (`doc_type_id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `current_approver_id` (`current_approver_id`);

--
-- Indexes for table `document_history`
--
ALTER TABLE `document_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `doc_id` (`doc_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD PRIMARY KEY (`step_id`),
  ADD KEY `doc_type_id` (`doc_type_id`),
  ADD KEY `approver_user_id` (`approver_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `document_history`
--
ALTER TABLE `document_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  MODIFY `step_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`doc_type_id`) REFERENCES `document_types` (`type_id`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`current_approver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `document_history`
--
ALTER TABLE `document_history`
  ADD CONSTRAINT `document_history_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  ADD CONSTRAINT `document_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD CONSTRAINT `workflow_steps_ibfk_1` FOREIGN KEY (`doc_type_id`) REFERENCES `document_types` (`type_id`),
  ADD CONSTRAINT `workflow_steps_ibfk_2` FOREIGN KEY (`approver_user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
