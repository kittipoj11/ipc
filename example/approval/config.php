<?php
$db_host = 'localhost';
$db_name = 'ชื่อฐานข้อมูลของคุณ'; // ใส่ชื่อฐานข้อมูลของคุณ
$db_user = 'ชื่อผู้ใช้ฐานข้อมูลของคุณ'; // ใส่ชื่อผู้ใช้ฐานข้อมูลของคุณ
$db_pass = 'รหัสผ่านฐานข้อมูลของคุณ'; // ใส่รหัสผ่านฐานข้อมูลของคุณ

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $e->getMessage());
}
?>