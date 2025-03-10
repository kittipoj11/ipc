<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['record_id'])) {
    $recordId = $_POST['record_id'];

    try {
        // เริ่ม transaction
        $pdo->beginTransaction();

        // ดึงข้อมูลไฟล์ที่จะลบ
        $stmtFilesToDelete = $pdo->prepare("SELECT file_path FROM files WHERE record_id = ?");
        $stmtFilesToDelete->execute([$recordId]);
        $filesToDelete = $stmtFilesToDelete->fetchAll(PDO::FETCH_ASSOC);

        // ลบไฟล์ออกจาก server
        foreach ($filesToDelete as $file) {
            $filePath = $file['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath); // ลบไฟล์
            }
        }

        // ลบข้อมูลไฟล์จากฐานข้อมูล (ON DELETE CASCADE จะจัดการให้เมื่อลบ record)
        // ลบ record หลัก
        $stmtDeleteRecord = $pdo->prepare("DELETE FROM records WHERE record_id = ?");
        $stmtDeleteRecord->execute([$recordId]);

        // commit transaction
        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Record and associated files deleted successfully.']);

    } catch (Exception $e) {
        // rollback transaction
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>