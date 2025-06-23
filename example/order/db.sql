CREATE TABLE `order_periods` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `period_no` int(11) NOT NULL,
  `work_percent` decimal(5,2) DEFAULT NULL,
  `interim_payments` decimal(10,2) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- เพิ่มข้อมูลตัวอย่างสำหรับ order_id = 1
INSERT INTO `order_periods` (`order_id`, `period_no`, `work_percent`, `interim_payments`, `remarks`) VALUES
(1, 1, 30.00, 50000.00, 'งวดที่ 1 วางมัดจำ'),
(1, 2, 40.00, 80000.00, 'งวดที่ 2 ส่งมอบงานครึ่งแรก'),
(1, 3, 30.00, 30000.00, 'งวดที่ 3 ปิดโครงการ');