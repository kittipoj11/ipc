CREATE DATABASE IF NOT EXISTS `parking_booking_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `parking_booking_system`;

-- ตารางสำหรับเก็บข้อมูลงานอีเวนต์ (ไม่มีคอลัมน์ JSON แล้ว)
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (ตารางใหม่) ตารางสำหรับเก็บข้อมูลลานจอดของแต่ละงาน
CREATE TABLE `event_parking_lots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `lot_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lot_per_event` (`event_id`,`lot_name`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `event_parking_lots_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางสำหรับเก็บช่วงเวลาที่เปิดให้จอง (เหมือนเดิม)
CREATE TABLE `booking_slots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `slot_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slot_per_event` (`event_id`,`slot_date`,`start_time`),
  CONSTRAINT `booking_slots_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางสำหรับเก็บข้อมูลการจองของ Vendor (เหมือนเดิม)
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` int(11) NOT NULL,
  `parking_lot` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `num_cars` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendor_one_booking_per_day` (`vendor_name`,`event_id`,`booking_date`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;