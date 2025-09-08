-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 04:37 AM
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

--
-- Dumping data for table `inspection_files`
--

INSERT INTO `inspection_files` (`file_id`, `inspection_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 1, 'IPC_cover.jpg', 'uploads/68aecf2896539.jpg', 'image/jpeg', '2025-08-27 09:26:00'),
(2, 1, 'Quick Hinge Adjustment Guide.jpg', 'uploads/68aecf4f99809.jpg', 'image/jpeg', '2025-08-27 09:26:39'),
(3, 1, 'IPC_cover.jpg', 'uploads/68afe0f20656b.jpg', 'image/jpeg', '2025-08-28 04:54:10'),
(7, 3, 'IPC_cover.jpg', 'uploads/68b1496ea147d.jpg', 'image/jpeg', '2025-08-29 06:32:14'),
(9, 2, 'Quick Hinge Adjustment Guide.jpg', 'uploads/68b149e83ef7e.jpg', 'image/jpeg', '2025-08-29 06:34:16'),
(11, 4, 'IPC_cover.jpg', 'uploads/68b14b4d77899.jpg', 'image/jpeg', '2025-08-29 06:40:13'),
(13, 4, 'image3.jpg', 'uploads/68b14b7cda190.jpg', 'image/jpeg', '2025-08-29 06:41:00'),
(15, 3, 'Untitled.jpg', 'uploads/68b14c61e9dad.jpg', 'image/jpeg', '2025-08-29 06:44:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inspection_files`
--
ALTER TABLE `inspection_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `inspection_id` (`inspection_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inspection_files`
--
ALTER TABLE `inspection_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inspection_files`
--
ALTER TABLE `inspection_files`
  ADD CONSTRAINT `inspection_files_ibfk_1` FOREIGN KEY (`inspection_id`) REFERENCES `inspection` (`inspection_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
