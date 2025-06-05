<?php
$servername = "localhost";
$username_db = "your_db_user"; // แก้ไขตามการตั้งค่าของคุณ
$password_db = "your_db_password"; // แก้ไขตามการตั้งค่าของคุณ
$dbname = "your_role_db"; // แก้ไขตามการตั้งค่าของคุณ

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>