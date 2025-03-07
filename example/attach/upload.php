<?php
header('Content-Type: application/json'); // กำหนด Content-Type เป็น JSON สำหรับ AJAX Response

try {
    // กำหนด Directory ที่จะเก็บไฟล์ (ต้องสร้าง Directory นี้ใน Server)
    $uploadDir = 'uploads/';

    // ตรวจสอบว่ามี Directory 'uploads' หรือยัง ถ้าไม่มีให้สร้าง
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // สร้าง Directory พร้อมสิทธิ์การเข้าถึง
    }

    // ตรวจสอบว่ามีการส่งไฟล์มาหรือไม่
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('ไม่พบไฟล์ที่อัปโหลด หรือเกิดข้อผิดพลาดในการอัปโหลด');
    }

    $file = $_FILES['file'];

    // ตรวจสอบนามสกุลไฟล์ (อนุญาตเฉพาะ PDF และ รูปภาพ)
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
    $fileName = $file['name'];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // นามสกุลไฟล์เล็ก
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('ประเภทไฟล์ไม่ถูกต้อง อนุญาตเฉพาะ PDF, JPG, JPEG, PNG, GIF เท่านั้น');
    }

    // สร้างชื่อไฟล์ใหม่ ป้องกันชื่อไฟล์ซ้ำ (ใช้ timestamp + ชื่อไฟล์เดิม)
    $newFileName = time() . '_' . $fileName;
    $filePath = $uploadDir . $newFileName;

    // ย้ายไฟล์ไปยัง Directory ที่ต้องการ
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('ไม่สามารถย้ายไฟล์ไปยัง Directory ที่กำหนดได้');
    }

    // **เชื่อมต่อฐานข้อมูลด้วย PDO**
    $dbHost = 'localhost'; // หรือ Host ของคุณ
    $dbName = 'inspection_db';
    $dbUser = 'root';
    $dbPass = '';

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // เตรียมคำสั่ง SQL สำหรับ Insert ข้อมูล
    $sql = "INSERT INTO attach_files (file_name, file_path, file_type) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // กำหนดประเภท MIME Type จากนามสกุลไฟล์
    $mimeType = mime_content_type($filePath);

    // Execute คำสั่ง SQL
    $stmt->execute([$fileName, $filePath, $mimeType]);

    // ส่ง Response กลับเป็น JSON (สำเร็จ)
    echo json_encode(['status' => 'success', 'message' => 'อัปโหลดและบันทึกข้อมูลไฟล์สำเร็จ']);
} catch (Exception $e) {
    // ส่ง Response กลับเป็น JSON (ล้มเหลว)
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
