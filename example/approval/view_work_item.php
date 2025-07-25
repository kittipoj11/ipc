<?php
session_start();
require_once 'config.php';
// สำหรับแสดงรายละเอียดงานและให้ผู้อนุมัติทำการอนุมัติหรือปฏิเสธ
// สมมติว่ามีการล็อกอินผู้ใช้แล้ว และมี $_SESSION['user_id']
if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ไม่พบ ID งาน");
}

$work_item_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

try {
    // ดึงข้อมูลงานและสถานะการอนุมัติปัจจุบันสำหรับผู้ใช้คนนี้
    $stmt = $pdo->prepare("SELECT wi.*, wia.approval_level, wia.approval_status
                           FROM work_items wi
                           JOIN work_item_approvals wia ON wi.id = wia.work_item_id
                           WHERE wi.id = :work_item_id AND wia.approver_id = :user_id
                           AND wi.current_approval_level = wia.approval_level AND wia.approval_status = 'Pending'");
    $stmt->bindParam(':work_item_id', $work_item_id);
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
    $work_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$work_item) {
        echo "<p>ไม่พบงานนี้ หรือไม่ถึงรอบการอนุมัติของคุณ</p>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

        $pdo->beginTransaction();
        try {
            if ($action === 'approve') {
                // อัปเดตสถานะการอนุมัติใน work_item_approvals
                $stmtUpdateApproval = $pdo->prepare("UPDATE work_item_approvals SET approval_status = 'Approved', approved_at = NOW(), approval_comment = :comment WHERE work_item_id = :work_item_id AND approval_level = :approval_level AND approver_id = :user_id");
                $stmtUpdateApproval->bindParam(':work_item_id', $work_item_id);
                $stmtUpdateApproval->bindParam(':approval_level', $work_item['approval_level']);
                $stmtUpdateApproval->bindParam(':user_id', $current_user_id);
                $stmtUpdateApproval->bindParam(':comment', $comment);
                $stmtUpdateApproval->execute();

                // ตรวจสอบว่าเป็นขั้นตอนการอนุมัติสุดท้ายหรือไม่
                $stmtNextLevel = $pdo->prepare("SELECT MIN(approval_level) AS next_level 
                                                FROM workflow_steps 
                                                WHERE workflow_id = (SELECT workflow_id 
                                                                    FROM workflow_steps 
                                                                    WHERE approver_id = :user_id 
                                                                    AND approval_level = :current_level) 
                                                AND approval_level > :current_level");
                $stmtNextLevel->bindParam(':user_id', $current_user_id);
                $stmtNextLevel->bindParam(':current_level', $work_item['approval_level']);
                $stmtNextLevel->execute();
                $nextLevelResult = $stmtNextLevel->fetch(PDO::FETCH_ASSOC);

                if ($nextLevelResult && $nextLevelResult['next_level']) {
                    // อัปเดต current_approval_level ใน work_items ไปยังระดับถัดไป
                    $stmtUpdateWorkItem = $pdo->prepare("UPDATE work_items SET current_approval_level = :next_level WHERE id = :work_item_id");
                    $stmtUpdateWorkItem->bindParam(':next_level', $nextLevelResult['next_level']);
                    $stmtUpdateWorkItem->bindParam(':work_item_id', $work_item_id);
                    $stmtUpdateWorkItem->execute();
                } else {
                    // ถ้าไม่มีระดับถัดไป ให้เปลี่ยนสถานะเป็น 'Closed'
                    $stmtUpdateWorkItem = $pdo->prepare("UPDATE work_items SET status = 'Closed' WHERE id = :work_item_id");
                    $stmtUpdateWorkItem->bindParam(':work_item_id', $work_item_id);
                    $stmtUpdateWorkItem->execute();
                }

                echo "<p style='color: green;'>อนุมัติงานสำเร็จ! <a href='approval_list.php'>กลับไปยังรายการรออนุมัติ</a></p>";
                $pdo->commit();
            } elseif ($action === 'reject') {
                // อัปเดตสถานะการอนุมัติเป็น 'Rejected' สำหรับขั้นตอนนี้
                $stmtRejectApproval = $pdo->prepare("UPDATE work_item_approvals SET approval_status = 'Rejected', approved_at = NOW(), approval_comment = :comment WHERE work_item_id = :work_item_id AND approval_level = :approval_level AND approver_id = :user_id");
                $stmtRejectApproval->bindParam(':work_item_id', $work_item_id);
                $stmtRejectApproval->bindParam(':approval_level', $work_item['approval_level']);
                $stmtRejectApproval->bindParam(':user_id', $current_user_id);
                $stmtRejectApproval->bindParam(':comment', $comment);
                $stmtRejectApproval->execute();

                // หา approval_level ก่อนหน้า
                $current_level = $work_item['approval_level'];
                $stmtPreviousLevel = $pdo->prepare("SELECT MAX(approval_level) AS previous_level
                                                   FROM workflow_steps
                                                   WHERE workflow_id = (SELECT workflow_id FROM workflow_steps WHERE approver_id = :user_id AND approval_level = :current_level)
                                                     AND approval_level < :current_level");
                $stmtPreviousLevel->bindParam(':user_id', $current_user_id);
                $stmtPreviousLevel->bindParam(':current_level', $current_level);
                $stmtPreviousLevel->execute();
                $previousLevelResult = $stmtPreviousLevel->fetch(PDO::FETCH_ASSOC);

                if ($previousLevelResult && $previousLevelResult['previous_level'] >= 1) {
                    $previous_level = $previousLevelResult['previous_level'];

                    // อัปเดต current_approval_level ใน work_items ให้กลับไปที่ระดับก่อนหน้า
                    $stmtUpdateWorkItemLevel = $pdo->prepare("UPDATE work_items SET current_approval_level = :previous_level, status = 'Pending' WHERE id = :work_item_id");
                    $stmtUpdateWorkItemLevel->bindParam(':previous_level', $previous_level);
                    $stmtUpdateWorkItemLevel->bindParam(':work_item_id', $work_item_id);
                    $stmtUpdateWorkItemLevel->execute();

                    // เปลี่ยนสถานะการอนุมัติใน work_item_approvals ของระดับก่อนหน้าให้เป็น 'Pending' อีกครั้ง (ถ้าเคยอนุมัติแล้ว)
                    $stmtResetPreviousApproval = $pdo->prepare("UPDATE work_item_approvals
                                                                SET approval_status = 'Pending', approved_at = NULL, approval_comment = NULL
                                                                WHERE work_item_id = :work_item_id AND approval_level = :previous_level");
                    $stmtResetPreviousApproval->bindParam(':work_item_id', $work_item_id);
                    $stmtResetPreviousApproval->bindParam(':previous_level', $previous_level);
                    $stmtResetPreviousApproval->execute();

                    echo "<p style='color: orange;'>ปฏิเสธงานแล้ว ระบบได้ส่งงานกลับไปยังผู้อนุมัติในขั้นตอนก่อนหน้า</p>";
                } else {
                    // หากไม่พบขั้นตอนก่อนหน้า (เช่น ปฏิเสธในขั้นตอนแรก) หรือเกิดข้อผิดพลาด
                    $stmtUpdateWorkItemStatus = $pdo->prepare("UPDATE work_items SET status = 'Rejected' WHERE id = :work_item_id");
                    $stmtUpdateWorkItemStatus->bindParam(':work_item_id', $work_item_id);
                    $stmtUpdateWorkItemStatus->execute();
                    echo "<p style='color: red;'>ปฏิเสธงานแล้ว และไม่พบขั้นตอนก่อนหน้า ระบบได้ตั้งสถานะเป็น 'Rejected'</p>";
                }

                $pdo->commit();
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<p style='color: red;'>เกิดข้อผิดพลาดในการดำเนินการอนุมัติ: " . $e->getMessage() . "</p>";
        }
    }

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>รายละเอียดงาน</title>
    </head>

    <body>
        <h1>รายละเอียดงาน</h1>
        <h2><?php echo htmlspecialchars($work_item['title']); ?></h2>
        <p><strong>ประเภทงาน:</strong> <?php echo htmlspecialchars($work_item['work_type']); ?></p>
        <p><strong>รายละเอียด:</strong><br><?php echo nl2br(htmlspecialchars($work_item['description'])); ?></p>

        <form method="post">
            <input type="hidden" name="work_item_id" value="<?php echo $work_item['id']; ?>">
            <div>
                <label for="comment">ความคิดเห็น (ถ้ามี):</label><br>
                <textarea id="comment" name="comment"></textarea>
            </div>
            <br>
            <button type="submit" name="action" value="approve">อนุมัติ</button>
            <button type="submit" name="action" value="reject">ปฏิเสธ</button>
        </form>
        <p><a href="approval_list.php">กลับไปยังรายการรออนุมัติ</a></p>
    </body>

    </html>
<?php

} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูลงาน: " . $e->getMessage());
}
?>