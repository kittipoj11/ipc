<?php
// กำหนดข้อมูลการเชื่อมต่อฐานข้อมูล
$host = "localhost"; // หรือ IP address ของฐานข้อมูล
$dbname = "your_database"; // ชื่อฐานข้อมูล
$username = "your_username"; // ชื่อผู้ใช้ฐานข้อมูล
$password = "your_password"; // รหัสผ่านฐานข้อมูล
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    throw new \PDOException("Error connecting to database: " . $e->getMessage());
}

// คำสั่ง SQL สำหรับดึงข้อมูลทั้งหมดจากตาราง users
$sql = "SELECT id, name FROM users";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$data = $stmt->fetchAll();

// กำหนด Content-Type เป็น application/json เพื่อให้ jQuery รู้ว่าข้อมูลที่ส่งกลับเป็น JSON
header('Content-Type: application/json');

// แปลงอาร์เรย์เป็น JSON และส่งกลับ
echo json_encode($data);
?>