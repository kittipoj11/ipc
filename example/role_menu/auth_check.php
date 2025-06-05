<?php
if (session_status() == PHP_SESSION_NONE) { // ตรวจสอบว่า session_start() ถูกเรียกไปแล้วหรือยัง
    session_start();
}
require_once __DIR__ . '/../db_connect.php'; // ใช้ __DIR__ เพื่อให้ path ถูกต้องเสมอ

// ตรวจสอบว่า Login หรือยัง
if (!isset($_SESSION['role_id'])) {
    header("Location: " . (basename($_SERVER['PHP_SELF']) === 'index.php' ? '' : '../') . "login.php"); // ปรับ path สำหรับ logout
    exit();
}

// ฟังก์ชันตรวจสอบสิทธิ์การเข้าถึงหน้าปัจจุบัน
function hasAccessToPage($pdo, $role_id, $page_filename) {
    $stmt = $pdo->prepare("SELECT COUNT(*)
                           FROM role_permissions rp
                           JOIN pages p ON rp.page_id = p.page_id
                           WHERE rp.role_id = :role_id AND p.page_filename = :page_filename");
    $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
    $stmt->bindParam(':page_filename', $page_filename, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// ไม่ต้องตรวจสอบสิทธิ์สำหรับ index.php และ login.php ในไฟล์นี้
// การตรวจสอบสิทธิ์ของแต่ละหน้าจะทำในหน้า A.php, B.php, C.php เอง
?>