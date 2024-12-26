-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2024 at 08:32 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `approval_status`
--

CREATE TABLE `approval_status` (
  `approval_status_id` int(10) UNSIGNED NOT NULL,
  `approval_status_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflow`
--

CREATE TABLE `approval_workflow` (
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `workflow_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(10) UNSIGNED NOT NULL,
  `department_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  `working_date_from` date DEFAULT NULL,
  `working_date_to` date DEFAULT NULL,
  `working_day` int(11) DEFAULT NULL,
  `remain_value_interim_payment` decimal(19,2) DEFAULT NULL,
  `total_retention_value` decimal(19,2) DEFAULT NULL,
  `inspect_status` int(10) UNSIGNED DEFAULT NULL,
  `create_by` int(10) UNSIGNED DEFAULT NULL,
  `create_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspect_period`
--

CREATE TABLE `inspect_period` (
  `inspect_period_id` int(10) UNSIGNED NOT NULL,
  `inspect_id` int(10) UNSIGNED DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `inspect_period_detail`
--

CREATE TABLE `inspect_period_detail` (
  `inspect_period_detail_id` int(10) UNSIGNED NOT NULL,
  `inspect_period_id` int(10) UNSIGNED DEFAULT NULL,
  `order_no` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspect_status`
--

CREATE TABLE `inspect_status` (
  `inspect_status_id` int(10) UNSIGNED NOT NULL,
  `inspect_status_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(10) UNSIGNED NOT NULL,
  `location_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_status`
--

CREATE TABLE `plan_status` (
  `plan_status_id` int(10) UNSIGNED NOT NULL,
  `plan_status_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `po`
--

CREATE TABLE `po` (
  `po_id` int(10) UNSIGNED NOT NULL,
  `po_no` varchar(255) DEFAULT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `suppliers_id` int(10) UNSIGNED DEFAULT NULL,
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
  `create_by` int(10) UNSIGNED DEFAULT NULL,
  `create_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(10) UNSIGNED NOT NULL,
  `project_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `suppliers_id` int(10) UNSIGNED NOT NULL,
  `supplier_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` int(10) UNSIGNED DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
-- Indexes for table `inspect_period`
--
ALTER TABLE `inspect_period`
  ADD PRIMARY KEY (`inspect_period_id`),
  ADD KEY `inspect_id` (`inspect_id`),
  ADD KEY `plan_status` (`plan_status`);

--
-- Indexes for table `inspect_period_detail`
--
ALTER TABLE `inspect_period_detail`
  ADD PRIMARY KEY (`inspect_period_detail_id`),
  ADD KEY `inspect_period_id` (`inspect_period_id`);

--
-- Indexes for table `inspect_status`
--
ALTER TABLE `inspect_status`
  ADD PRIMARY KEY (`inspect_status_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `plan_status`
--
ALTER TABLE `plan_status`
  ADD PRIMARY KEY (`plan_status_id`);

--
-- Indexes for table `po`
--
ALTER TABLE `po`
  ADD PRIMARY KEY (`po_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `suppliers_id` (`suppliers_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`suppliers_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role` (`role`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_levels`
--
ALTER TABLE `approval_levels`
  MODIFY `level_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_status`
--
ALTER TABLE `approval_status`
  MODIFY `approval_status_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  MODIFY `workflow_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspect_approvals`
--
ALTER TABLE `inspect_approvals`
  MODIFY `approval_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspect_main`
--
ALTER TABLE `inspect_main`
  MODIFY `inspect_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspect_period`
--
ALTER TABLE `inspect_period`
  MODIFY `inspect_period_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspect_period_detail`
--
ALTER TABLE `inspect_period_detail`
  MODIFY `inspect_period_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspect_status`
--
ALTER TABLE `inspect_status`
  MODIFY `inspect_status_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_status`
--
ALTER TABLE `plan_status`
  MODIFY `plan_status_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po`
--
ALTER TABLE `po`
  MODIFY `po_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `suppliers_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_levels`
--
ALTER TABLE `approval_levels`
  ADD CONSTRAINT `approval_levels_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `approval_workflow` (`workflow_id`),
  ADD CONSTRAINT `approval_levels_ibfk_2` FOREIGN KEY (`approver_role`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `inspect_approvals`
--
ALTER TABLE `inspect_approvals`
  ADD CONSTRAINT `inspect_approvals_ibfk_1` FOREIGN KEY (`inspect_id`) REFERENCES `inspect_main` (`inspect_id`),
  ADD CONSTRAINT `inspect_approvals_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `approval_levels` (`level_id`),
  ADD CONSTRAINT `inspect_approvals_ibfk_3` FOREIGN KEY (`approver_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `inspect_approvals_ibfk_4` FOREIGN KEY (`approval_status_id`) REFERENCES `approval_status` (`approval_status_id`);

--
-- Constraints for table `inspect_main`
--
ALTER TABLE `inspect_main`
  ADD CONSTRAINT `inspect_main_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po` (`po_id`),
  ADD CONSTRAINT `inspect_main_ibfk_2` FOREIGN KEY (`inspect_status`) REFERENCES `inspect_status` (`inspect_status_id`);

--
-- Constraints for table `inspect_period`
--
ALTER TABLE `inspect_period`
  ADD CONSTRAINT `inspect_period_ibfk_1` FOREIGN KEY (`inspect_id`) REFERENCES `inspect_main` (`inspect_id`),
  ADD CONSTRAINT `inspect_period_ibfk_2` FOREIGN KEY (`plan_status`) REFERENCES `plan_status` (`plan_status_id`);

--
-- Constraints for table `inspect_period_detail`
--
ALTER TABLE `inspect_period_detail`
  ADD CONSTRAINT `inspect_period_detail_ibfk_1` FOREIGN KEY (`inspect_period_id`) REFERENCES `inspect_period` (`inspect_period_id`);

--
-- Constraints for table `po`
--
ALTER TABLE `po`
  ADD CONSTRAINT `po_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`),
  ADD CONSTRAINT `po_ibfk_2` FOREIGN KEY (`suppliers_id`) REFERENCES `suppliers` (`suppliers_id`),
  ADD CONSTRAINT `po_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
