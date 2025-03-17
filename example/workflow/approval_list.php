<?php
session_start();
require_once 'config.php';

// สมมติว่ามีการล็อกอินผู้ใช้แล้ว และมี $_SESSION['user_id']
$_SESSION['user_id'] = 10; // ID ของผู้ใช้ 'approver1'

if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

$current_user_id = $_SESSION['user_id'];

try {
    // ดึงรายการงานที่รออนุมัติสำหรับผู้ใช้คนปัจจุบัน
    $stmt = $pdo->prepare("SELECT wia.work_item_id, wi.title, wi.description, wi.work_type
                           FROM work_item_approvals wia
                           JOIN work_items wi ON wia.work_item_id = wi.id
                           WHERE wia.approver_id = :user_id AND wia.approval_status = 'Pending'
                           AND wi.current_approval_level = wia.approval_level");
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
    $pending_approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>รายการรออนุมัติ</title>
</head>

<body>
    <h1>รายการรออนุมัติ</h1>
    <?php if ($pending_approvals): ?>
        <ul>
            <?php foreach ($pending_approvals as $approval): ?>
                <li>
                    <a href="view_work_item.php?id=<?php echo $approval['work_item_id']; ?>">
                        <?php echo htmlspecialchars($approval['title']); ?> (<?php echo htmlspecialchars($approval['work_type']); ?>)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>ไม่มีรายการรออนุมัติสำหรับคุณในขณะนี้</p>
    <?php endif; ?>
    <p><a href="index.php">สร้างงานใหม่</a></p>
</body>

</html>