<?php
// **** แก้ไขค่าเหล่านี้ให้ตรงกับของคุณ ****
$host = 'localhost';
$dbname = 'role_permission_menu_db'; // ชื่อฐานข้อมูลที่สร้างในขั้นตอนที่ 2
$user = 'root';        // Username สำหรับเข้าฐานข้อมูล
$pass = '';            // Password สำหรับเข้าฐานข้อมูล

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $e->getMessage());
}