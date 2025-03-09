<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['record_id'])) {
    $recordId = $_POST['record_id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->beginTransaction();

        // ดึงรายชื่อไฟล์ก่อนลบ record เพื่อนำไปลบไฟล์จริงออกจากโฟลเดอร์ uploads
        $fileStmt = $pdo->prepare("SELECT file_path FROM files WHERE record_id = ?");
        $fileStmt->execute([$recordId]);
        $filesToDelete = $fileStmt->fetchAll(PDO::FETCH_COLUMN);

        // ลบ record จากตาราง records (files ในตาราง files จะถูกลบอัตโนมัติด้วย ON DELETE CASCADE)
        $stmt = $pdo->prepare("DELETE FROM records WHERE record_id = ?");
        $stmt->execute([$recordId]);

        // ลบไฟล์จริงออกจากโฟลเดอร์ uploads
        foreach ($filesToDelete as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Record and related files deleted successfully!']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>