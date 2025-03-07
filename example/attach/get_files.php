<?php
header('Content-Type: application/json');

try {
    // **เชื่อมต่อฐานข้อมูลด้วย PDO** (เหมือน upload.php)
    $dbHost = 'localhost'; // หรือ Host ของคุณ
    $dbName = 'inspection_db';
    $dbUser = 'root';
    $dbPass = '';

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query ข้อมูลไฟล์ทั้งหมดจากตาราง files
    $sql = "SELECT * FROM attach_files ORDER BY upload_date DESC"; // เรียงตามวันที่อัปโหลดล่าสุด
    $stmt = $pdo->query($sql);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch ข้อมูลเป็น Associative Array

    // ส่งข้อมูลไฟล์กลับเป็น JSON
    echo json_encode(['status' => 'success', 'files' => $files]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
