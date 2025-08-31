<?php
// ตั้งค่า Timezone
date_default_timezone_set('Asia/Bangkok');

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$db_host = 'localhost';
$db_name = 'parking_booking_system';
$db_user = 'root'; // <-- แก้ไขเป็น username ของคุณ
$db_pass = '';     // <-- แก้ไขเป็น password ของคุณ
$charset = 'utf8mb4';

// ตั้งค่า Data Source Name (DSN)
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

// ตั้งค่า Options สำหรับ PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // สร้าง PDO instance สำหรับการเชื่อมต่อ
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    // กรณีเชื่อมต่อล้มเหลว
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
