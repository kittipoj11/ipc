-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2025 at 06:29 PM
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
-- Database: `inspection_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `workflow_steps`
--

CREATE TABLE `workflow_steps` (
  `workflow_step_id` int(11) UNSIGNED NOT NULL,
  `workflow_id` int(11) UNSIGNED DEFAULT NULL,
  `approved_level` int(11) DEFAULT NULL,
  `approver_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `workflow_steps`
--

INSERT INTO `workflow_steps` (`workflow_step_id`, `workflow_id`, `approved_level`, `approver_id`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 3),
(3, 1, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `approval_status`
--

CREATE TABLE `approval_status` (
  `approved_status_id` int(11) UNSIGNED NOT NULL,
  `approved_status_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `approval_status`
--

INSERT INTO `approval_status` (`approved_status_id`, `approved_status_name`, `is_deleted`) VALUES
(0, 'ไม่อนุมัติ', 0),
(1, 'รออนุมัติ', 0),
(2, 'อนุมัติ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `workflow_id` int(11) UNSIGNED NOT NULL,
  `workflow_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`workflow_id`, `workflow_name`, `is_deleted`) VALUES
(1, 'การอนุมัติแบบปกติ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) UNSIGNED NOT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `is_deleted`) VALUES
(1, 'IT', 0),
(2, 'FM', 0),
(3, 'FA', 0);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `record_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 1, '1741334107_taxinvoice homepro.pdf', 'uploads/67cef01fc31c1.pdf', 'application/pdf', '2025-03-10 13:58:55'),
(2, 1, '1741515237_Gemini_Generated_Image_yut9dsyut9dsyut9.jpg', 'uploads/67cef01fc3aee.jpg', 'image/jpeg', '2025-03-10 13:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `inspection_approvals`
--

CREATE TABLE `inspection_approvals` (
  `inspection_approved_id` int(11) UNSIGNED NOT NULL,
  `inspection_id` int(11) UNSIGNED DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `approved_level` int(11) UNSIGNED DEFAULT NULL,
  `approver_id` int(11) UNSIGNED DEFAULT NULL,
  `approved_status_id` int(11) UNSIGNED DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `approved_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_files`
--

CREATE TABLE `inspection_files` (
  `file_id` int(11) NOT NULL,
  `inspection_id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_periods`
--

CREATE TABLE `inspection_periods` (
  `inspection_id` int(11) UNSIGNED NOT NULL,
  `period_id` int(11) DEFAULT NULL,
  `workload_planned_percent` decimal(5,2) DEFAULT NULL,
  `workload_actual_completed_percent` decimal(5,2) DEFAULT NULL,
  `workload_remaining_percent` decimal(5,2) DEFAULT NULL,
  `interim_payment` decimal(19,2) DEFAULT NULL,
  `interim_payment_percent` decimal(5,2) DEFAULT NULL,
  `interim_payment_less_previous` decimal(19,2) DEFAULT NULL,
  `interim_payment_less_previous_percent` decimal(5,2) DEFAULT NULL,
  `interim_payment_accumulated` decimal(19,2) DEFAULT NULL,
  `interim_payment_accumulated_percent` decimal(5,2) DEFAULT NULL,
  `interim_payment_remain` decimal(19,2) DEFAULT NULL,
  `interim_payment_remain_percent` decimal(5,2) DEFAULT NULL,
  `retention_value` decimal(19,2) DEFAULT NULL,
  `plan_status` int(11) UNSIGNED DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT NULL,
  `is_retention` tinyint(1) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `workflow_id` int(11) UNSIGNED DEFAULT NULL,
  `current_status` varchar(255) DEFAULT NULL,
  `current_approval_level` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_period_details`
--

CREATE TABLE `inspection_period_details` (
  `rec_id` int(11) UNSIGNED NOT NULL,
  `inspection_id` int(11) UNSIGNED DEFAULT NULL,
  `order_no` int(11) DEFAULT 1,
  `details` text DEFAULT NULL,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_status`
--

CREATE TABLE `inspection_status` (
  `inspection_status_id` int(11) UNSIGNED NOT NULL,
  `inspection_status_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inspection_status`
--

INSERT INTO `inspection_status` (`inspection_status_id`, `inspection_status_name`, `is_deleted`) VALUES
(0, 'ไม่ผ่าน', 0),
(1, 'รอตรวจ', 0),
(2, 'ตรวจแล้ว', 0),
(3, 'ผ่าน', 0);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) UNSIGNED NOT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_name`, `is_deleted`) VALUES
(1, 'Sky', 0),
(2, 'Aktiv xxx', 0),
(3, 'Challenger', 0);

-- --------------------------------------------------------

--
-- Table structure for table `plan_status`
--

CREATE TABLE `plan_status` (
  `plan_status_id` int(11) UNSIGNED NOT NULL,
  `plan_status_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `plan_status`
--

INSERT INTO `plan_status` (`plan_status_id`, `plan_status_name`, `is_deleted`) VALUES
(0, 'ล่าช้ากว่าแผนงาน', 0),
(1, 'ตามแผนงาน', 0),
(2, 'เร็วกว่าแผนงาน', 0);

-- --------------------------------------------------------

--
-- Table structure for table `po_main`
--

CREATE TABLE `po_main` (
  `po_id` int(11) UNSIGNED NOT NULL,
  `po_number` varchar(255) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `supplier_id` int(11) UNSIGNED DEFAULT NULL,
  `location_id` int(11) UNSIGNED DEFAULT NULL,
  `working_name_th` varchar(255) DEFAULT NULL,
  `working_name_en` varchar(255) DEFAULT NULL,
  `is_include_vat` tinyint(1) DEFAULT NULL,
  `contract_value` decimal(19,2) DEFAULT NULL,
  `contract_value_before` decimal(19,2) DEFAULT NULL,
  `vat` decimal(19,2) DEFAULT NULL,
  `is_deposit` tinyint(1) DEFAULT NULL,
  `deposit_percent` decimal(5,2) DEFAULT NULL,
  `deposit_value` decimal(19,2) DEFAULT NULL,
  `working_date_from` date DEFAULT NULL,
  `working_date_to` date DEFAULT NULL,
  `working_day` int(11) DEFAULT NULL,
  `create_by` varchar(255) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `number_of_period` int(11) NOT NULL DEFAULT 0,
  `remain_value_interim_payment` decimal(9,2) NOT NULL,
  `total_retention_value` decimal(9,2) NOT NULL,
  `inspect_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `po_period`
--

CREATE TABLE `po_period` (
  `period_id` int(11) NOT NULL,
  `po_id` int(11) UNSIGNED DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `interim_payment` decimal(19,2) DEFAULT NULL,
  `interim_payment_percent` decimal(4,2) NOT NULL,
  `period_status` int(11) DEFAULT NULL,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `record_id` int(11) NOT NULL,
  `record_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`record_id`, `record_name`, `created_at`) VALUES
(1, 'IMPO0001', '2025-03-10 13:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `role_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'ผู้ช่วยผู้จัดการ'),
(3, 'ผู้จัดการ'),
(4, 'ผู้อำนวยการ'),
(5, 'กรรมการผู้จัดการ');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `is_deleted`) VALUES
(1, 'บริษัทวินสตาร์คอร์ปจำกัด', 0),
(2, 'บริษัทไมโครซอฟต์จำกัด', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_code` varchar(5) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role_id` int(11) UNSIGNED DEFAULT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_code`, `username`, `password`, `full_name`, `role_id`, `department_id`) VALUES
(1, '05389', 'admin', 'admin', 'Administrator', 1, 1),
(3, '05389', 'nathapat', '1111', 'Nathapat Soontornpurmsap', 2, 1),
(4, '00001', 'A00001', '1111', 'AA Admin', 1, 1),
(5, '00002', 'A00002', '1111', 'BB AM', 2, 1),
(6, '00003', 'A00003', '1111', 'CC Mgr', 3, 1),
(7, '00004', 'A00004', '1111', 'DD D', 4, 1),
(8, '00005', 'A00005', '1111', 'EE MD', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `your_table_name`
--

CREATE TABLE `your_table_name` (
  `id` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `column1` varchar(255) DEFAULT NULL,
  `column2` varchar(255) DEFAULT NULL,
  `column3` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `your_table_name`
--

INSERT INTO `your_table_name` (`id`, `category`, `column1`, `column2`, `column3`, `status`) VALUES
(1, 'A', 'ข้อมูล A1', 'ข้อมูล A2', 'รายละเอียดเพิ่มเติมเกี่ยวกับ A', 1),
(2, 'B', 'ข้อมูล B1', 'ข้อมูล B2', 'รายละเอียดเพิ่มเติมเกี่ยวกับ B', 0),
(3, 'A', 'ข้อมูล A3', 'ข้อมูล A4', 'รายละเอียดเพิ่มเติมของ A อีกรายการ', 1),
(4, 'C', 'ข้อมูล C1', 'ข้อมูล C2', 'รายละเอียดเพิ่มเติมเกี่ยวกับ C', 1),
(5, 'B', 'ข้อมูล B3', 'ข้อมูล B4', 'รายละเอียดเพิ่มเติมของ B อีกรายการ', 1),
(6, 'A', 'ข้อมูล A5', 'ข้อมูล A6', 'รายละเอียดเพิ่มเติม A ล่าสุด', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD PRIMARY KEY (`workflow_step_id`),
  ADD KEY `workflow_id` (`workflow_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indexes for table `approval_status`
--
ALTER TABLE `approval_status`
  ADD PRIMARY KEY (`approved_status_id`);

--
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`workflow_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `record_id` (`record_id`);

--
-- Indexes for table `inspection_approvals`
--
ALTER TABLE `inspection_approvals`
  ADD PRIMARY KEY (`inspection_approved_id`),
  ADD KEY `inspect_id` (`inspection_id`),
  ADD KEY `approved_level` (`approved_level`),
  ADD KEY `approver_id` (`approver_id`),
  ADD KEY `approved_status_id` (`approved_status_id`);

--
-- Indexes for table `inspection_files`
--
ALTER TABLE `inspection_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `inspection_id` (`inspection_id`);

--
-- Indexes for table `inspection_periods`
--
ALTER TABLE `inspection_periods`
  ADD PRIMARY KEY (`inspection_id`),
  ADD KEY `plan_status` (`plan_status`),
  ADD KEY `inspection_periods_ibfk_1` (`period_id`);

--
-- Indexes for table `inspection_period_details`
--
ALTER TABLE `inspection_period_details`
  ADD PRIMARY KEY (`rec_id`),
  ADD KEY `inspect_period_id` (`inspection_id`);

--
-- Indexes for table `inspection_status`
--
ALTER TABLE `inspection_status`
  ADD PRIMARY KEY (`inspection_status_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `plan_status`
--
ALTER TABLE `plan_status`
  ADD PRIMARY KEY (`plan_status_id`);

--
-- Indexes for table `po_main`
--
ALTER TABLE `po_main`
  ADD PRIMARY KEY (`po_id`),
  ADD KEY `suppliers_id` (`supplier_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `po_period`
--
ALTER TABLE `po_period`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role` (`role_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `your_table_name`
--
ALTER TABLE `your_table_name`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  MODIFY `workflow_step_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `workflow_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `inspection_approvals`
--
ALTER TABLE `inspection_approvals`
  MODIFY `inspection_approved_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_files`
--
ALTER TABLE `inspection_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `inspection_periods`
--
ALTER TABLE `inspection_periods`
  MODIFY `inspection_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `inspection_period_details`
--
ALTER TABLE `inspection_period_details`
  MODIFY `rec_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `po_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `po_period`
--
ALTER TABLE `po_period`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `your_table_name`
--
ALTER TABLE `your_table_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `records` (`record_id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_files`
--
ALTER TABLE `inspection_files`
  ADD CONSTRAINT `inspection_files_ibfk_1` FOREIGN KEY (`inspection_id`) REFERENCES `inspection_periods` (`inspection_id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_periods`
--
ALTER TABLE `inspection_periods`
  ADD CONSTRAINT `inspection_periods_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `po_period` (`period_id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_period_details`
--
ALTER TABLE `inspection_period_details`
  ADD CONSTRAINT `inspection_period_details_ibfk_1` FOREIGN KEY (`inspection_id`) REFERENCES `inspection_periods` (`inspection_id`) ON DELETE CASCADE;

--
-- Constraints for table `po_period`
--
ALTER TABLE `po_period`
  ADD CONSTRAINT `po_period_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`po_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
