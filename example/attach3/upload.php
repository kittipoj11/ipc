<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['record_name'])) {
    $recordName = $_POST['record_name'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO records (record_name) VALUES (?)");
        $stmt->execute([$recordName]);
        $recordId = $pdo->lastInsertId();

        if (isset($_FILES['files'])) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                $fileType = $_FILES['files']['type'][$key];
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Invalid file type.");
                }

                if ($_FILES['files']['size'][$key] > $maxFileSize) {
                    throw new Exception("File size exceeds 2MB limit.");
                }

                $originalFileName = $_FILES['files']['name'][$key];
                $fileName = uniqid() . '_' . $originalFileName;
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmp_name, $filePath)) {
                    $fileStmt = $pdo->prepare("INSERT INTO files (record_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
                    $fileStmt->execute([$recordId, $originalFileName, $filePath, $fileType]);
                } else {
                    throw new Exception("Failed to upload file: " . $originalFileName);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Files uploaded successfully!']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>