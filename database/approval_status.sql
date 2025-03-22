-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2025 at 04:00 AM
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
-- Table structure for table `approval_status`
--

CREATE TABLE `approval_status` (
  `approval_status_id` int(11) NOT NULL,
  `action_type_id` int(11) NOT NULL,
  `approval_status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `approval_status`
--

INSERT INTO `approval_status` (`approval_status_id`, `action_type_id`, `approval_status_name`) VALUES
(1, 1, 'Pending Approval'),
(2, 1, 'Approved'),
(3, 1, 'Rejected'),
(4, 2, 'Pending Submission'),
(5, 2, 'Submitted'),
(6, 2, 'Returned'),
(7, 3, 'Pending Confirmation'),
(8, 3, 'Confirmed'),
(9, 4, 'Pending Verification'),
(10, 4, 'Verified');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_status`
--
ALTER TABLE `approval_status`
  ADD PRIMARY KEY (`approval_status_id`),
  ADD UNIQUE KEY `approval_status_name` (`approval_status_name`),
  ADD KEY `action_type_id` (`action_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_status`
--
ALTER TABLE `approval_status`
  MODIFY `approval_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_status`
--
ALTER TABLE `approval_status`
  ADD CONSTRAINT `approval_status_ibfk_1` FOREIGN KEY (`action_type_id`) REFERENCES `action_types` (`action_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
