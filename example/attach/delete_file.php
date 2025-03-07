<?php
// เชื่อมต่อฐานข้อมูล (แก้ไขข้อมูลการเชื่อมต่อให้ถูกต้อง)
$host = 'localhost';
$dbname = 'inspection_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $e->getMessage()]);
    exit();
}

if (isset($_POST['attach_id']) && isset($_POST['file_path'])) {
    $attachId = $_POST['attach_id'];
    $filePath = $_POST['file_path'];

    try {
        // 1. ลบไฟล์จาก file system
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถลบไฟล์จาก file system ได้: ' . $filePath]);
                exit();
            }
        }

        // 2. ดึงข้อมูล file_paths เดิมจากฐานข้อมูล
        $stmt = $pdo->prepare("SELECT file_paths FROM attach_files WHERE attach_id = ?");
        $stmt->execute([$attachId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $filePathsArray = json_decode($result['file_paths'], true);
            if (is_array($filePathsArray)) {
                // 3. กรอง file_paths array เพื่อลบ path ที่ต้องการลบออก
                $updatedFilePaths = array_filter($filePathsArray, function($path) use ($filePath) {
                    return $path !== $filePath;
                });

                // 4. อัปเดต file_paths ในฐานข้อมูล (หากยังเหลือไฟล์) หรือลบเรคอร์ด (หากไม่มีไฟล์เหลือ)
                if (!empty($updatedFilePaths)) {
                    $updatedFilePathsJson = json_encode(array_values($updatedFilePaths)); // re-index array and encode
                    $stmt = $pdo->prepare("UPDATE attach_files SET file_paths = ? WHERE attach_id = ?");
                    $stmt->execute([$updatedFilePathsJson, $attachId]);
                } else {
                    $stmt = $pdo->prepare("DELETE FROM attach_files WHERE attach_id = ?");
                    $stmt->execute([$attachId]);
                }
                echo json_encode(['status' => 'success', 'message' => 'ลบไฟล์และข้อมูลเรียบร้อยแล้ว.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ข้อมูล file_paths ในฐานข้อมูลไม่ถูกต้อง.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบ record ID ที่ระบุในฐานข้อมูล.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูลจากฐานข้อมูล: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ถูกต้อง.']);
}
?>
