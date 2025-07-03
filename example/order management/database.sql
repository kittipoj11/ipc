-- ลบตารางเก่าทิ้งถ้ามีอยู่แล้ว เพื่อให้รันสคริปต์ซ้ำได้
DROP TABLE IF EXISTS `order_periods`;
DROP TABLE IF EXISTS `purchase_orders`;

-- 1. ตารางสำหรับข้อมูลหลัก (Header)
CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `contract_value` decimal(15,2) DEFAULT 0.00,
  `working_date_from` date DEFAULT NULL,
  `working_date_to` date DEFAULT NULL,
  PRIMARY KEY (`po_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. ตารางสำหรับข้อมูลงวดงาน (Details)
CREATE TABLE `order_periods` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(11) NOT NULL,
  `period_no` int(11) NOT NULL,
  `work_percent` decimal(5,2) DEFAULT 0.00,
  `interim_payments` decimal(15,2) DEFAULT 0.00,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`period_id`),
  KEY `po_id_fk` (`po_id`),
  CONSTRAINT `po_id_fk` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. เพิ่มข้อมูลตัวอย่าง
INSERT INTO `purchase_orders` (`po_id`, `po_number`, `project_name`, `contract_value`, `working_date_from`, `working_date_to`) VALUES
(1001, 'PO-2025-001', 'โครงการก่อสร้าง A (ตัวอย่าง)', 160000.00, '2025-01-15', '2025-12-31');

INSERT INTO `order_periods` (`po_id`, `period_no`, `work_percent`, `interim_payments`, `remarks`) VALUES
(1001, 1, 30.00, 50000.00, 'งวดที่ 1 วางมัดจำ'),
(1001, 2, 40.00, 80000.00, 'งวดที่ 2 ส่งมอบงานครึ่งแรก'),
(1001, 3, 30.00, 30000.00, 'งวดที่ 3 ปิดโครงการ');