-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 10:13 AM
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
-- Table structure for table `workflow_steps`
--

CREATE TABLE `workflow_steps` (
  `workflow_step_id` int(11) UNSIGNED NOT NULL,
  `workflow_id` int(11) UNSIGNED DEFAULT NULL,
  `approval_level` int(11) DEFAULT NULL,
  `approver_id` int(11) UNSIGNED DEFAULT NULL,
  `action_type_id` int(11) NOT NULL,
  `first_status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `workflow_steps`
--

INSERT INTO `workflow_steps` (`workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `action_type_id`, `first_status_id`) VALUES
(1, 1, 1, 1, 1, 1),
(2, 1, 2, 3, 4, 7),
(3, 1, 3, 4, 1, 1),
(4, 1, 4, 1, 2, 3),
(5, 1, 5, 5, 4, 7),
(6, 1, 6, 3, 4, 7),
(7, 1, 7, 6, 2, 3),
(8, 1, 8, 7, 4, 7),
(9, 2, 1, 1, 1, 1),
(10, 2, 2, 3, 4, 7),
(11, 3, 1, 4, 1, 1),
(12, 3, 2, 1, 2, 3),
(13, 3, 3, 5, 4, 7),
(14, 3, 4, 3, 4, 7),
(15, 3, 5, 6, 2, 3),
(16, 3, 6, 7, 4, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD PRIMARY KEY (`workflow_step_id`),
  ADD KEY `workflow_id` (`workflow_id`),
  ADD KEY `approver_id` (`approver_id`),
  ADD KEY `fk_workflow_step_action_type` (`action_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  MODIFY `workflow_step_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD CONSTRAINT `fk_workflow_step_action_type` FOREIGN KEY (`action_type_id`) REFERENCES `action_type` (`action_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
