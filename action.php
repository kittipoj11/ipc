<?php
require_once 'class/connection_class.php';

$connection = new Connection;
$pdo = $connection->getDbConnection();
$token  = $_GET['token'] ?? '';
$status = $_GET['status'] ?? '';

if ($token && in_array($status, ['approved', 'rejected'])) {

    $sql = "UPDATE requests SET status=:status WHERE token=:token";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "✅ สถานะถูกอัปเดตเป็น: $status";
    } else {
        echo "❌ Token ไม่ถูกต้อง หรืออัปเดตไม่สำเร็จ";
    }
} else {
    echo "❌ ข้อมูลไม่ถูกต้อง";
}

// Update requests
// Update Inspection