<?php
$servername = "localhost";
$username_db = "root"; // แก้ไขตามการตั้งค่าของคุณ
$password_db = ""; // แก้ไขตามการตั้งค่าของคุณ
$dbname = "role_menu_db"; // แก้ไขตามการตั้งค่าของคุณ

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>