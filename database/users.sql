-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 12:59 PM
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_code` varchar(5) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role_id` int(11) UNSIGNED DEFAULT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_code`, `username`, `password`, `full_name`, `role_id`, `department_id`, `filename`, `is_deleted`) VALUES
(1, '05389', 'systemadmin', 'admin', 'System Administrator', 0, 1, 'uploads/signatures/signature.jpg', 0),
(2, '00001', 'admin', 'admin', 'Administrator', 1, 1, 'uploads/signatures/signature.jpg', 0),
(3, '05389', 'nathapat', '1111', 'Nathapat Soontornpurmsap', 2, 1, 'uploads/signatures/nathapats.jpg', 0),
(4, '05389', 'A000', '1111', 'User 1(Ast.Manager)', 2, 1, 'uploads/signatures/user1.jpg', 0),
(5, '00002', 'A001', '1111', 'User 2(Manager)', 3, 1, 'uploads/signatures/user2.jpg', 0),
(6, '00003', 'A002', '1111', 'User 3(D)', 4, 1, 'uploads/signatures/user3.jpg', 0),
(7, '00004', 'A003', '1111', 'User 4(MD)', 5, 1, 'uploads/signatures/user4.jpg', 0),
(8, '00005', 'A004', '1111', 'User 5(Officer)', 6, 1, 'uploads/signatures/user5.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_unique` (`username`,`user_code`) USING BTREE,
  ADD KEY `role` (`role_id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
