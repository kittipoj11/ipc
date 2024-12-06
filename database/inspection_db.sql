-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2024 at 10:12 AM
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
-- Table structure for table `inspect_detail`
--

CREATE TABLE `inspect_detail` (
  `inspect_no` varchar(50) NOT NULL,
  `po_no` varchar(50) NOT NULL,
  `location_code` int(11) DEFAULT NULL,
  `working_name` varchar(255) DEFAULT NULL,
  `working_date_from` date DEFAULT NULL,
  `working_date_to` date DEFAULT NULL,
  `working_day` smallint(6) DEFAULT NULL,
  `period` tinyint(4) NOT NULL,
  `is_deposit` tinyint(1) DEFAULT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `net_value_current_claim` decimal(10,2) DEFAULT NULL,
  `less_previous_interim_payment` decimal(10,2) DEFAULT NULL,
  `total_value_interim_payment` decimal(10,2) DEFAULT NULL,
  `remain_value_interim_payment` decimal(10,2) DEFAULT NULL,
  `total_value_retension` decimal(10,2) DEFAULT NULL,
  `completed_work_volume_plan` decimal(10,2) DEFAULT NULL,
  `completed_work_volume_current` decimal(10,2) DEFAULT NULL,
  `completed_work_volume_remain` decimal(10,2) DEFAULT NULL,
  `status_plan` varchar(1) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT NULL,
  `is_retention` tinyint(1) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `list_json` longtext DEFAULT NULL,
  `create_by` varchar(50) DEFAULT NULL,
  `create_date` date DEFAULT NULL,
  `inspector` varchar(50) DEFAULT NULL,
  `inspect_date` date DEFAULT NULL,
  `approver1` varchar(50) DEFAULT NULL,
  `approve_date1` date DEFAULT NULL,
  `approver2` varchar(50) DEFAULT NULL,
  `approve_date2` date DEFAULT NULL,
  `approver3` varchar(50) DEFAULT NULL,
  `approve_date3` date DEFAULT NULL,
  `approver4` varchar(50) DEFAULT NULL,
  `approve_date4` date DEFAULT NULL,
  `approver5` varchar(50) DEFAULT NULL,
  `approve_date5` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspect_header`
--

CREATE TABLE `inspect_header` (
  `inspect_no` varchar(50) NOT NULL,
  `po_no` varchar(50) NOT NULL,
  `contract_value` decimal(10,2) DEFAULT NULL,
  `remain_value_interim_payment` decimal(10,2) DEFAULT NULL,
  `total_value_retension` decimal(10,2) DEFAULT NULL,
  `create_by` varchar(50) DEFAULT NULL,
  `create_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_code` int(11) NOT NULL,
  `location_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `po`
--

CREATE TABLE `po` (
  `po_no` varchar(50) NOT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `suppliers_code` int(11) DEFAULT NULL,
  `contract_value` decimal(10,2) DEFAULT NULL,
  `retention_percent` decimal(4,2) DEFAULT NULL,
  `retention_value` decimal(10,2) DEFAULT 5.00,
  `create_by` varchar(50) DEFAULT NULL,
  `create_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `suppliers_code` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '''''',
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `department_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inspect_detail`
--
ALTER TABLE `inspect_detail`
  ADD PRIMARY KEY (`po_no`,`period`);

--
-- Indexes for table `inspect_header`
--
ALTER TABLE `inspect_header`
  ADD PRIMARY KEY (`po_no`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_code`);

--
-- Indexes for table `po`
--
ALTER TABLE `po`
  ADD PRIMARY KEY (`po_no`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`suppliers_code`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_code` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `suppliers_code` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
