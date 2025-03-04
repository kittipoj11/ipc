-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2025 at 11:26 AM
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
-- Table structure for table `approval_levels`
--

CREATE TABLE `approval_levels` (
  `level_id` int(10) UNSIGNED NOT NULL,
  `workflow_id` int(10) UNSIGNED DEFAULT NULL,
  `level_order` int(11) DEFAULT NULL,
  `approver_role` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `approval_levels`
--

INSERT INTO `approval_levels` (`level_id`, `workflow_id`, `level_order`, `approver_role`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 3),
(3, 1, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `approval_status`
--

CREATE TABLE `approval_status` (
  `approval_status_id` int(10) UNSIGNED NOT NULL,
  `approval_status_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `approval_status`
--

INSERT INTO `approval_status` (`approval_status_id`, `approval_status_name`, `is_deleted`) VALUES
(0, 'ไม่อนุมัติ', 0),
(1, 'รออนุมัติ', 0),
(2, 'อนุมัติ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflow`
--

CREATE TABLE `approval_workflow` (
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `workflow_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `approval_workflow`
--

INSERT INTO `approval_workflow` (`workflow_id`, `workflow_name`, `is_deleted`) VALUES
(1, 'การอนุมัติแบบปกติ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(10) UNSIGNED NOT NULL,
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
-- Table structure for table `inspection_periods`
--

CREATE TABLE `inspection_periods` (
  `inspection_id` int(10) UNSIGNED NOT NULL,
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
  `plan_status` int(10) UNSIGNED DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT NULL,
  `is_retention` tinyint(1) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `workflow_id` int(10) UNSIGNED DEFAULT NULL,
  `current_status` varchar(255) DEFAULT NULL,
  `current_level` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inspection_periods`
--

INSERT INTO `inspection_periods` (`inspection_id`, `period_id`, `workload_planned_percent`, `workload_actual_completed_percent`, `workload_remaining_percent`, `interim_payment`, `interim_payment_percent`, `interim_payment_less_previous`, `interim_payment_less_previous_percent`, `interim_payment_accumulated`, `interim_payment_accumulated_percent`, `interim_payment_remain`, `interim_payment_remain_percent`, `retention_value`, `plan_status`, `is_paid`, `is_retention`, `remark`, `workflow_id`, `current_status`, `current_level`) VALUES
(1, 1, 30.00, 30.00, 70.00, 1.00, NULL, 1.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 1, '1', 1),
(17, 1, NULL, NULL, NULL, 1.00, NULL, 2.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 1, '1', 1),
(20, 2, 60.00, 60.00, 40.00, 3.00, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 1, '1', 1),
(21, 3, 100.00, 100.00, 0.00, 4.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 1, '1', 1),
(22, 2, NULL, NULL, NULL, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 1, '1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `inspection_period_details`
--

CREATE TABLE `inspection_period_details` (
  `rec_id` int(10) UNSIGNED NOT NULL,
  `inspection_id` int(10) UNSIGNED DEFAULT NULL,
  `order_no` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inspection_period_details`
--

INSERT INTO `inspection_period_details` (`rec_id`, `inspection_id`, `order_no`, `details`, `remark`) VALUES
(1, 1, 1, 'งานเดินท่อ', NULL),
(2, 1, 2, 'งานติดตั้งโคมไฟ LED', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inspect_approvals`
--

CREATE TABLE `inspect_approvals` (
  `approval_id` int(10) UNSIGNED NOT NULL,
  `inspect_id` int(10) UNSIGNED DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `level_id` int(10) UNSIGNED DEFAULT NULL,
  `approver_id` int(10) UNSIGNED DEFAULT NULL,
  `approval_status_id` int(10) UNSIGNED DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspect_main`
--

CREATE TABLE `inspect_main` (
  `inspect_id` int(10) UNSIGNED NOT NULL,
  `po_id` int(10) UNSIGNED DEFAULT NULL,
  `remain_value_interim_payment` decimal(19,2) DEFAULT NULL,
  `total_retention_value` decimal(19,2) DEFAULT NULL,
  `inspect_status` int(10) UNSIGNED DEFAULT NULL,
  `create_by` int(10) UNSIGNED DEFAULT NULL,
  `create_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inspect_main`
--

INSERT INTO `inspect_main` (`inspect_id`, `po_id`, `remain_value_interim_payment`, `total_retention_value`, `inspect_status`, `create_by`, `create_date`) VALUES
(1, 1, 869161.00, NULL, 1, NULL, NULL),
(2, 2, 107000.00, NULL, 1, 5389, NULL),
(3, 5, 107000.00, NULL, 1, 5389, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inspect_status`
--

CREATE TABLE `inspect_status` (
  `inspect_status_id` int(10) UNSIGNED NOT NULL,
  `inspect_status_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inspect_status`
--

INSERT INTO `inspect_status` (`inspect_status_id`, `inspect_status_name`, `is_deleted`) VALUES
(0, 'ไม่ผ่าน', 0),
(1, 'รอตรวจ', 0),
(2, 'ตรวจแล้ว', 0),
(3, 'ผ่าน', 0);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(10) UNSIGNED NOT NULL,
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
  `plan_status_id` int(10) UNSIGNED NOT NULL,
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
  `po_id` int(10) UNSIGNED NOT NULL,
  `po_number` varchar(255) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
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

--
-- Dumping data for table `po_main`
--

INSERT INTO `po_main` (`po_id`, `po_number`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`, `deposit_percent`, `deposit_value`, `working_date_from`, `working_date_to`, `working_day`, `create_by`, `create_date`, `number_of_period`, `remain_value_interim_payment`, `total_retention_value`, `inspect_status`) VALUES
(1, 'IMPO23020769', 'Statue of Load Indra Riding on Erawan Elephant', 1, 3, 'งานติดตั้งโคมไฟตกแต่ง LED และวางระบบควบคุม', 'Install of LED decoration lamps', 1, 869161.00, 812300.00, 86861.00, NULL, 0.00, 0.00, '2025-02-01', '2025-02-08', 8, 'nathapats', '2025-01-15 10:03:35', 3, 0.00, 0.00, 0),
(2, 'IMP002', 'ข่าวช่องวันเสาร์-อาทิตย์', 1, 3, 'ซันเซต ๑', 'Sunset 1', 1, 107000.00, 100000.00, 7000.00, NULL, 10.00, 10700.00, '2025-02-01', '2025-02-08', 8, '05389', NULL, 2, 0.00, 0.00, 0),
(4, 'xxx', 'xxx', 1, 1, 'xxx', 'xxx', 1, 11.00, 11.00, 11.00, 1, 11.00, 1.00, '0000-00-00', '0000-00-00', 0, '', NULL, 0, 0.00, 0.00, 0),
(5, 'IMP001', 'Statue of Load Indra Riding on Erawan Elephant', 1, 2, 'งานติดตั้งโคมไฟตกแต่ง LED และวางระบบควบคุมx', 'Install of LED decoration lampsx', 1, 107000.00, 100000.00, 7000.00, 0, 10.00, 10700.00, '2025-03-01', '2025-03-31', 31, '05389', NULL, 4, 0.00, 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `po_period`
--

CREATE TABLE `po_period` (
  `period_id` int(10) UNSIGNED NOT NULL,
  `po_id` int(10) UNSIGNED DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `interim_payment` decimal(19,2) DEFAULT NULL,
  `interim_payment_percent` decimal(4,2) NOT NULL,
  `period_status` int(11) DEFAULT NULL,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `po_period`
--

INSERT INTO `po_period` (`period_id`, `po_id`, `period_number`, `interim_payment`, `interim_payment_percent`, `period_status`, `remark`) VALUES
(1, 1, 1, 1.00, 0.00, NULL, '1'),
(17, 2, 1, 10.00, 0.00, NULL, 'จ่าย QR'),
(20, 1, 2, 2.00, 0.00, NULL, 'จ่าย QR'),
(21, 1, 3, 3.00, 0.00, NULL, 'จ่าย QR'),
(22, 2, 2, 20.00, 0.00, NULL, 'จ่าย QR');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
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
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `is_deleted`) VALUES
(1, 'บริษัทวินสตาร์คอร์ปจำกัด', 0),
(2, 'xxx', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_code` varchar(5) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role_id` int(10) UNSIGNED DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_levels`
--
ALTER TABLE `approval_levels`
  ADD PRIMARY KEY (`level_id`),
  ADD KEY `workflow_id` (`workflow_id`),
  ADD KEY `approver_role` (`approver_role`);

--
-- Indexes for table `approval_status`
--
ALTER TABLE `approval_status`
  ADD PRIMARY KEY (`approval_status_id`);

--
-- Indexes for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  ADD PRIMARY KEY (`workflow_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `inspection_periods`
--
ALTER TABLE `inspection_periods`
  ADD PRIMARY KEY (`inspection_id`),
  ADD KEY `plan_status` (`plan_status`);

--
-- Indexes for table `inspection_period_details`
--
ALTER TABLE `inspection_period_details`
  ADD PRIMARY KEY (`rec_id`),
  ADD KEY `inspect_period_id` (`inspection_id`);

--
-- Indexes for table `inspect_approvals`
--
ALTER TABLE `inspect_approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `inspect_id` (`inspect_id`),
  ADD KEY `level_id` (`level_id`),
  ADD KEY `approver_id` (`approver_id`),
  ADD KEY `approval_status_id` (`approval_status_id`);

--
-- Indexes for table `inspect_main`
--
ALTER TABLE `inspect_main`
  ADD PRIMARY KEY (`inspect_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `inspect_status` (`inspect_status`);

--
-- Indexes for table `inspect_status`
--
ALTER TABLE `inspect_status`
  ADD PRIMARY KEY (`inspect_status_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_levels`
--
ALTER TABLE `approval_levels`
  MODIFY `level_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  MODIFY `workflow_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inspection_periods`
--
ALTER TABLE `inspection_periods`
  MODIFY `inspection_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `inspection_period_details`
--
ALTER TABLE `inspection_period_details`
  MODIFY `rec_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inspect_approvals`
--
ALTER TABLE `inspect_approvals`
  MODIFY `approval_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspect_main`
--
ALTER TABLE `inspect_main`
  MODIFY `inspect_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `po_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `po_period`
--
ALTER TABLE `po_period`
  MODIFY `period_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `po_main`
--
ALTER TABLE `po_main`
  ADD CONSTRAINT `po_main_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `po_main_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`);

--
-- Constraints for table `po_period`
--
ALTER TABLE `po_period`
  ADD CONSTRAINT `po_period_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`po_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
