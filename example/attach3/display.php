<?php
require_once 'db_config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM records ORDER BY record_id DESC");
    $records = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $record = $row;
        $fileStmt = $pdo->prepare("SELECT file_id, file_name, file_path, file_type FROM files WHERE record_id = ?");
        $fileStmt->execute([$record['record_id']]);
        $record['files'] = $fileStmt->fetchAll(PDO::FETCH_ASSOC);
        $records[] = $record;
    }

    echo json_encode(['status' => 'success', 'data' => $records]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>