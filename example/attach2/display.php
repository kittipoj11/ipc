<?php
require_once 'db_config.php';

try {
    $stmtRecords = $pdo->query("SELECT * FROM records ORDER BY created_at DESC");
    $records = $stmtRecords->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($records as $record) {
        $recordId = $record['record_id'];
        $stmtFiles = $pdo->prepare("SELECT * FROM files WHERE record_id = ?");
        $stmtFiles->execute([$recordId]);
        $files = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);

        $record['files'] = $files;
        $data[] = $record;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch records: ' . $e->getMessage()]);
}
?>