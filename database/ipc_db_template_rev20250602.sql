-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 12:09 PM
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
-- Database: `ipc_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_type`
--

CREATE TABLE `action_type` (
  `action_type_id` int(11) NOT NULL,
  `action_type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `action_type`
--

INSERT INTO `action_type` (`action_type_id`, `action_type_name`) VALUES
(1, 'submit'),
(2, 'verify'),
(3, 'confirm'),
(4, 'approve');

-- --------------------------------------------------------

--
-- Table structure for table `approval_status`
--

CREATE TABLE `approval_status` (
  `approval_status_id` int(11) NOT NULL,
  `approval_status_name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `approval_status`
--

INSERT INTO `approval_status` (`approval_status_id`, `approval_status_name`, `is_deleted`) VALUES
(0, 'Rejected', 0),
(1, 'Pending', 0),
(2, 'Approved', 0);

-- --------------------------------------------------------

--
-- Table structure for table `approval_type`
--

CREATE TABLE `approval_type` (
  `approval_type_id` int(11) NOT NULL,
  `approval_type_name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `approval_type`
--

INSERT INTO `approval_type` (`approval_type_id`, `approval_type_name`, `is_deleted`) VALUES
(1, 'submit', 0),
(2, 'verify', 0),
(3, 'confirm', 0),
(4, 'approve', 0);

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
-- Table structure for table `document_type`
--

CREATE TABLE `document_type` (
  `document_type_id` int(11) NOT NULL,
  `document_type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `document_type`
--

INSERT INTO `document_type` (`document_type_id`, `document_type_name`) VALUES
(1, 'ตรวจรับงาน'),
(2, 'IPC');

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
  `po_id` int(11) DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `workload_planned_percent` decimal(5,2) DEFAULT NULL,
  `workload_actual_completed_percent` decimal(5,2) DEFAULT NULL,
  `workload_remaining_percent` decimal(5,2) DEFAULT NULL,
  `workload_accumulated_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `interim_payment` decimal(19,2) DEFAULT NULL,
  `interim_payment_percent` decimal(5,2) DEFAULT NULL,
  `interim_payment_less_previous` decimal(19,2) DEFAULT NULL,
  `interim_payment_less_previous_percent` decimal(5,2) DEFAULT NULL,
  `interim_payment_accumulated` decimal(19,2) DEFAULT NULL,
  `interim_payment_accumulated_percent` decimal(5,2) DEFAULT NULL,
  `interim_payment_remain` decimal(19,2) DEFAULT NULL,
  `interim_payment_remain_percent` decimal(5,2) DEFAULT NULL,
  `retention_value` decimal(19,2) DEFAULT NULL,
  `plan_status_id` int(11) DEFAULT -1,
  `is_paid` tinyint(1) DEFAULT NULL,
  `is_retention` tinyint(1) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `inspection_status` int(11) NOT NULL DEFAULT 1,
  `current_approval_level` int(11) NOT NULL DEFAULT 1,
  `disbursement` tinyint(1) NOT NULL DEFAULT -1,
  `workflow_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inspection_periods`
--

-- --------------------------------------------------------

--
-- Table structure for table `inspection_period_approvals`
--

CREATE TABLE `inspection_period_approvals` (
  `inspection_approval_id` int(11) UNSIGNED NOT NULL,
  `inspection_id` int(11) UNSIGNED DEFAULT NULL,
  `period_id` int(11) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `approval_level` int(11) UNSIGNED DEFAULT NULL,
  `approver_id` int(11) UNSIGNED DEFAULT NULL,
  `approval_type_id` int(11) NOT NULL,
  `approval_type_text` varchar(255) NOT NULL,
  `approval_status_id` int(11) UNSIGNED DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `approval_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inspection_period_approvals`
--


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

--
-- Dumping data for table `inspection_period_details`
--

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
(0, 'Failed', 0),
(1, 'Pending Inspection', 0),
(2, 'Inspected', 0),
(3, 'Passed', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ipc_periods`
--

CREATE TABLE `ipc_periods` (
  `ipc_id` int(11) UNSIGNED NOT NULL,
  `inspection_id` int(11) UNSIGNED NOT NULL,
  `period_id` int(11) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `project_name` varchar(255) DEFAULT NULL,
  `agreement_date` datetime DEFAULT NULL,
  `contractor` varchar(255) DEFAULT NULL,
  `contract_value` decimal(19,2) DEFAULT NULL,
  `total_value_of_interim_payment` decimal(19,2) DEFAULT NULL,
  `less_previous_interim_payment` decimal(19,2) DEFAULT NULL,
  `net_value_of_current_claim` decimal(19,2) DEFAULT NULL,
  `less_retension_exclude_vat` decimal(19,2) DEFAULT NULL,
  `net_amount_due_for_payment` decimal(19,2) DEFAULT NULL,
  `total_value_of_retention` decimal(19,2) DEFAULT NULL,
  `total_value_of_certification_made` decimal(19,2) DEFAULT NULL,
  `resulting_balance_of_contract_sum_outstanding` decimal(19,2) DEFAULT NULL,
  `submit_by` varchar(255) DEFAULT NULL,
  `approved1_by` varchar(255) DEFAULT NULL,
  `approved2_by` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `workflow_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipc_period_approvals`
--

CREATE TABLE `ipc_period_approvals` (
  `ipc_approval_id` int(11) UNSIGNED NOT NULL,
  `ipc_id` int(11) UNSIGNED DEFAULT NULL,
  `inspection_id` int(11) UNSIGNED DEFAULT NULL,
  `period_id` int(11) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `approval_level` int(11) UNSIGNED DEFAULT NULL,
  `approver_id` int(11) UNSIGNED DEFAULT NULL,
  `approval_type_id` int(11) DEFAULT NULL,
  `approval_type_text` varchar(255) DEFAULT NULL,
  `approval_status_id` int(11) UNSIGNED DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `approval_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
(2, 'Aktiv', 0),
(3, 'Challenger', 0),
(4, 'IMP Exhibition', 1),
(5, 'Forum', 0);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL COMMENT 'รหัสสิทธิ์การใช้งาน',
  `permission_name` varchar(255) NOT NULL COMMENT 'ชื่อสิทธิ์การใช้งาน (เช่น ''view_dashboard'', ''manage_users'', ''edit_products'')',
  `menu_name` varchar(255) NOT NULL,
  `content_filename` varchar(255) NOT NULL DEFAULT 'content_filename',
  `function_name` varchar(255) NOT NULL DEFAULT 'function_name',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `menu_name`, `content_filename`, `function_name`, `is_deleted`) VALUES
(1, 'ข้อมูลระบบ', 'system', 'content_filename', 'system', 0),
(2, 'ข้อมูลพื้นฐานทั่วไป', 'general_basic', 'content_filename', 'function_name', 0),
(3, 'Purchase Order', 'purchase_order', 'po.php', 'po', 0),
(4, 'ตรวจรับงาน', 'inspection', 'content_filename', 'function_name', 0),
(5, 'IPC', 'ipc', 'content_filename', 'function_name', 0),
(6, 'การจัดการผู้ใช้', 'manage_user', 'content_filename', 'function_name', 0),
(7, 'รายงาน', 'report', 'report', 'function_name', 0),
(8, 'po', 'po', 'po.php', 'po', 0);

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
(0, 'ล่าช้ากว่าแผนงาน (Delayed)', 0),
(1, 'ตามแผนงาน (On Schedule)', 0),
(2, 'เร็วกว่าแผนงาน (Ahead of Schedule)', 0);

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
  `po_status` int(11) NOT NULL DEFAULT 1,
  `workflow_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `po_main`
--


-- --------------------------------------------------------

--
-- Table structure for table `po_periods`
--

CREATE TABLE `po_periods` (
  `period_id` int(11) NOT NULL,
  `po_id` int(11) UNSIGNED DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `interim_payment` decimal(19,2) DEFAULT NULL,
  `interim_payment_percent` decimal(4,2) NOT NULL,
  `period_status` int(11) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `workload_planned_percent` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `po_periods`
--

-- --------------------------------------------------------

--
-- Table structure for table `po_status`
--

CREATE TABLE `po_status` (
  `po_status_id` int(11) NOT NULL,
  `po_status_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `po_status`
--

INSERT INTO `po_status` (`po_status_id`, `po_status_name`, `is_deleted`) VALUES
(1, 'Draft (อยู่ในขั้นตอนการสร้าง)', 0),
(2, 'Pending (มีการ submit inspection period แล้วอย่างน้อย 1 period)', 0),
(3, 'Closed (ใบสั่งซื้อนี้มีการอนุมัติ Inspection period เสร็จสมบูรณ์ทั้งหมดแล้ว)', 0),
(4, 'Cancelled (ถูกยกเลิกด้วยเหตุผลบางประการ)', 0);

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `record_id` int(11) NOT NULL,
  `record_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
(0, 'System Admin'),
(1, 'System Admin'),
(2, 'Assistant Manager'),
(3, 'Manager'),
(4, 'Director'),
(5, 'Managing Director');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(0, 1),
(0, 2),
(0, 3),
(0, 4),
(0, 5),
(0, 6),
(0, 7),
(0, 8),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 3),
(2, 4),
(2, 5),
(2, 7),
(3, 3),
(3, 4),
(3, 5),
(3, 7),
(4, 4),
(4, 5),
(4, 7),
(5, 4),
(5, 5),
(5, 7);

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
(2, 'บริษัทไมโครซอฟต์จำกัด', 0),
(3, 'Jasmeen', 0),
(4, 'abc', 1);

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
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_code`, `username`, `password`, `full_name`, `role_id`, `department_id`, `is_deleted`) VALUES
(1, '05389', 'systemadmin', 'admin', 'System Administrator', 0, 1, 0),
(2, '00001', 'admin', 'admin', 'Administrator', 1, 1, 0),
(3, '05389', 'nathapat', '1111', 'Nathapat Soontornpurmsap', 2, 1, 0),
(4, '05389', 'A000', '1111', 'Mr. Zero', 3, 1, 0),
(5, '00002', 'A001', '1111', 'Mr. One', 2, 1, 0),
(6, '00003', 'A002', '1111', 'Mr. Two', 3, 1, 0),
(7, '00004', 'A003', '1111', 'เลขา Three', 2, 1, 0),
(8, '00005', 'A004', '1111', 'MD. Four', 5, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `workflow_id` int(11) UNSIGNED NOT NULL,
  `workflow_name` varchar(255) DEFAULT NULL,
  `document_type_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`workflow_id`, `workflow_name`, `document_type_id`, `is_deleted`) VALUES
(1, 'ตรวจรับงาน', 1, 0),
(2, 'IPC', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `workflow_status`
--

CREATE TABLE `workflow_status` (
  `workflow_status_id` int(11) NOT NULL,
  `workflow_status_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `workflow_status`
--

INSERT INTO `workflow_status` (`workflow_status_id`, `workflow_status_name`, `is_deleted`) VALUES
(-2, 'Cancelled', 0),
(-1, 'Rejected', 0),
(0, 'Pending', 0),
(1, 'Submitted', 0),
(2, 'In progress', 0),
(3, 'Verified', 0),
(4, 'Approved', 0),
(5, 'Completed', 0),
(6, 'Closed', 0);

-- --------------------------------------------------------

--
-- Table structure for table `workflow_steps`
--

CREATE TABLE `workflow_steps` (
  `workflow_step_id` int(11) UNSIGNED NOT NULL,
  `workflow_id` int(11) UNSIGNED DEFAULT NULL,
  `approval_level` int(11) DEFAULT NULL,
  `approver_id` int(11) UNSIGNED DEFAULT NULL,
  `approval_type_id` int(11) NOT NULL,
  `approval_type_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `workflow_steps`
--

INSERT INTO `workflow_steps` (`workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`) VALUES
(1, 1, 1, 1, 1, 'submit'),
(2, 1, 2, 3, 4, 'approve'),
(3, 2, 1, 4, 1, 'submit'),
(4, 2, 2, 1, 2, 'confirm'),
(5, 2, 3, 5, 4, 'approve'),
(6, 2, 4, 3, 4, 'approve'),
(7, 2, 5, 6, 2, 'confirm'),
(8, 2, 6, 7, 4, 'approve');

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
-- Indexes for table `action_type`
--
ALTER TABLE `action_type`
  ADD PRIMARY KEY (`action_type_id`);

--
-- Indexes for table `approval_status`
--
ALTER TABLE `approval_status`
  ADD PRIMARY KEY (`approval_status_id`);

--
-- Indexes for table `approval_type`
--
ALTER TABLE `approval_type`
  ADD PRIMARY KEY (`approval_type_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `document_type`
--
ALTER TABLE `document_type`
  ADD PRIMARY KEY (`document_type_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `record_id` (`record_id`);

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
  ADD KEY `plan_status` (`plan_status_id`),
  ADD KEY `inspection_periods_ibfk_1` (`period_id`);

--
-- Indexes for table `inspection_period_approvals`
--
ALTER TABLE `inspection_period_approvals`
  ADD PRIMARY KEY (`inspection_approval_id`),
  ADD KEY `inspection_approvals_ibfk_1` (`inspection_id`);

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
-- Indexes for table `ipc_periods`
--
ALTER TABLE `ipc_periods`
  ADD PRIMARY KEY (`ipc_id`),
  ADD KEY `ipc_periods_ibfk_1` (`period_id`);

--
-- Indexes for table `ipc_period_approvals`
--
ALTER TABLE `ipc_period_approvals`
  ADD PRIMARY KEY (`ipc_approval_id`),
  ADD KEY `ipc_approvals_ibfk_1` (`ipc_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

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
-- Indexes for table `po_periods`
--
ALTER TABLE `po_periods`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `po_status`
--
ALTER TABLE `po_status`
  ADD PRIMARY KEY (`po_status_id`);

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
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`);

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
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`workflow_id`);

--
-- Indexes for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD PRIMARY KEY (`workflow_step_id`),
  ADD KEY `workflow_id` (`workflow_id`),
  ADD KEY `approver_id` (`approver_id`),
  ADD KEY `fk_workflow_step_action_type` (`approval_type_id`);

--
-- Indexes for table `your_table_name`
--
ALTER TABLE `your_table_name`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_type`
--
ALTER TABLE `action_type`
  MODIFY `action_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `approval_status`
--
ALTER TABLE `approval_status`
  MODIFY `approval_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `approval_type`
--
ALTER TABLE `approval_type`
  MODIFY `approval_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document_type`
--
ALTER TABLE `document_type`
  MODIFY `document_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_files`
--
ALTER TABLE `inspection_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_periods`
--
ALTER TABLE `inspection_periods`
  MODIFY `inspection_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_period_approvals`
--
ALTER TABLE `inspection_period_approvals`
  MODIFY `inspection_approval_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_period_details`
--
ALTER TABLE `inspection_period_details`
  MODIFY `rec_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipc_periods`
--
ALTER TABLE `ipc_periods`
  MODIFY `ipc_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipc_period_approvals`
--
ALTER TABLE `ipc_period_approvals`
  MODIFY `ipc_approval_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสสิทธิ์การใช้งาน', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `po_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po_periods`
--
ALTER TABLE `po_periods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po_status`
--
ALTER TABLE `po_status`
  MODIFY `po_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `workflow_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  MODIFY `workflow_step_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  ADD CONSTRAINT `inspection_periods_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `po_periods` (`period_id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_period_approvals`
--
ALTER TABLE `inspection_period_approvals`
  ADD CONSTRAINT `inspection_approvals_ibfk_1` FOREIGN KEY (`inspection_id`) REFERENCES `inspection_periods` (`inspection_id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_period_details`
--
ALTER TABLE `inspection_period_details`
  ADD CONSTRAINT `inspection_period_details_ibfk_1` FOREIGN KEY (`inspection_id`) REFERENCES `inspection_periods` (`inspection_id`) ON DELETE CASCADE;

--
-- Constraints for table `po_periods`
--
ALTER TABLE `po_periods`
  ADD CONSTRAINT `po_periods_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`po_id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD CONSTRAINT `fk_workflow_step_action_type` FOREIGN KEY (`approval_type_id`) REFERENCES `approval_type` (`approval_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
