<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['record_name'])) {
    $recordName = $_POST['record_name'];

    try {
        // เริ่ม transaction
        $pdo->beginTransaction();

        // บันทึก record หลัก
        $stmtRecord = $pdo->prepare("INSERT INTO records (record_name) VALUES (?)");
        $stmtRecord->execute([$recordName]);
        $recordId = $pdo->lastInsertId();

        if (isset($_FILES['files'])) {
            $uploadDir = 'uploads/'; // โฟลเดอร์สำหรับเก็บไฟล์

            // ตรวจสอบว่าโฟลเดอร์ uploads มีอยู่หรือไม่ ถ้าไม่มีให้สร้าง
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) { // สร้างโฟลเดอร์และตั้ง permission (0777 คือ read, write, execute สำหรับทุก user)
                    throw new Exception("Failed to create uploads directory.");
                }
            }

            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                $fileType = $_FILES['files']['type'][$key];
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Invalid file type.");
                }

                $fileSize = $_FILES['files']['size'][$key];
                if ($fileSize > 2000000) { // 2MB limit
                    throw new Exception("File size exceeds 2MB.");
                }

                $originalFileName = $_FILES['files']['name'][$key];
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                $newFileName = uniqid() . '.' . $fileExtension;
                $filePath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmp_name, $filePath)) {
                    // บันทึกข้อมูลไฟล์ลงฐานข้อมูล
                    $stmtFile = $pdo->prepare("INSERT INTO files (record_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
                    $stmtFile->execute([$recordId, $originalFileName, $filePath, $fileType]);
                } else {
                    throw new Exception("Failed to upload file.");
                }
            }
        }

        // commit transaction
        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Record and files uploaded successfully.']);

    } catch (Exception $e) {
        // rollback transaction
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>