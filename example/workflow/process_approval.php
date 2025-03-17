<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit();
}

if (isset($_GET['action']) && ($_GET['action'] === 'approve' || $_GET['action'] === 'reject') && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['work_item_id'])) {
    $action = $_GET['action'];
    $work_item_id = $_POST['work_item_id'];
    $current_user_id = $_SESSION['user_id'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    try {
        $pdo->beginTransaction();

        // ดึงข้อมูลงานและสถานะการอนุมัติปัจจุบันอีกครั้งเพื่อความแน่ใจ
        $stmtCheck = $pdo->prepare("SELECT wi.*, wia.approval_level
                                  FROM work_items wi
                                  JOIN work_item_approvals wia ON wi.id = wia.work_item_id
                                  WHERE wi.id = :work_item_id AND wia.approver_id = :user_id
                                  AND wi.current_approval_level = wia.approval_level AND wia.approval_status = 'Pending'");
        $stmtCheck->bindParam(':work_item_id', $work_item_id);
        $stmtCheck->bindParam(':user_id', $current_user_id);
        $stmtCheck->execute();
        $work_item = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$work_item) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบงานนี้ หรือไม่ถึงรอบการอนุมัติของคุณ']);
            exit();
        }

        if ($action === 'approve') {
            // โค้ดส่วนการอนุมัติ (เหมือนเดิม) ...
            $stmtUpdateApproval = $pdo->prepare("UPDATE work_item_approvals SET approval_status = 'Approved', approved_at = NOW(), approval_comment = :comment WHERE work_item_id = :work_item_id AND approval_level = :approval_level AND approver_id = :user_id");
            $stmtUpdateApproval->bindParam(':work_item_id', $work_item_id);
            $stmtUpdateApproval->bindParam(':approval_level', $work_item['approval_level']);
            $stmtUpdateApproval->bindParam(':user_id', $current_user_id);
            $stmtUpdateApproval->bindParam(':comment', $comment);
            $stmtUpdateApproval->execute();

            $stmtNextLevel = $pdo->prepare("SELECT MIN(approval_level) AS next_level FROM workflow_steps WHERE workflow_id = (SELECT workflow_id FROM workflow_steps WHERE approver_id = :user_id AND approval_level = :current_level) AND approval_level > :current_level");
            $stmtNextLevel->bindParam(':user_id', $current_user_id);
            $stmtNextLevel->bindParam(':current_level', $work_item['approval_level']);
            $stmtNextLevel->execute();
            $nextLevelResult = $stmtNextLevel->fetch(PDO::FETCH_ASSOC);

            if ($nextLevelResult && $nextLevelResult['next_level']) {
                $stmtUpdateWorkItem = $pdo->prepare("UPDATE work_items SET current_approval_level = :next_level WHERE id = :work_item_id");
                $stmtUpdateWorkItem->bindParam(':next_level', $nextLevelResult['next_level']);
                $stmtUpdateWorkItem->bindParam(':work_item_id', $work_item_id);
                $stmtUpdateWorkItem->execute();
            } else {
                $stmtUpdateWorkItem = $pdo->prepare("UPDATE work_items SET status = 'Closed' WHERE id = :work_item_id");
                $stmtUpdateWorkItem->bindParam(':work_item_id', $work_item_id);
                $stmtUpdateWorkItem->execute();
            }
            echo json_encode(['success' => true, 'message' => 'อนุมัติงานสำเร็จ!']);
        } elseif ($action === 'reject') {
            // โค้ดส่วนการปฏิเสธ (เหมือนเดิม) ...
            $stmtRejectApproval = $pdo->prepare("UPDATE work_item_approvals SET approval_status = 'Rejected', approved_at = NOW(), approval_comment = :comment WHERE work_item_id = :work_item_id AND approval_level = :approval_level AND approver_id = :user_id");
            $stmtRejectApproval->bindParam(':work_item_id', $work_item_id);
            $stmtRejectApproval->bindParam(':approval_level', $work_item['approval_level']);
            $stmtRejectApproval->bindParam(':user_id', $current_user_id);
            $stmtRejectApproval->bindParam(':comment', $comment);
            $stmtRejectApproval->execute();

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
                $stmtUpdateWorkItemLevel = $pdo->prepare("UPDATE work_items SET current_approval_level = :previous_level, status = 'Pending' WHERE id = :work_item_id");
                $stmtUpdateWorkItemLevel->bindParam(':previous_level', $previous_level);
                $stmtUpdateWorkItemLevel->bindParam(':work_item_id', $work_item_id);
                $stmtUpdateWorkItemLevel->execute();
                $stmtResetPreviousApproval = $pdo->prepare("UPDATE work_item_approvals
                                                            SET approval_status = 'Pending', approved_at = NULL, approval_comment = NULL
                                                            WHERE work_item_id = :work_item_id AND approval_level = :previous_level");
                $stmtResetPreviousApproval->bindParam(':work_item_id', $work_item_id);
                $stmtResetPreviousApproval->bindParam(':previous_level', $previous_level);
                $stmtResetPreviousApproval->execute();
                echo json_encode(['success' => true, 'message' => 'ปฏิเสธงานแล้ว ระบบได้ส่งงานกลับไปยังผู้อนุมัติในขั้นตอนก่อนหน้า']);
            } else {
                $stmtUpdateWorkItemStatus = $pdo->prepare("UPDATE work_items SET status = 'Rejected' WHERE id = :work_item_id");
                $stmtUpdateWorkItemStatus->bindParam(':work_item_id', $work_item_id);
                $stmtUpdateWorkItemStatus->execute();
                echo json_encode(['success' => true, 'message' => 'ปฏิเสธงานแล้ว และไม่พบขั้นตอนก่อนหน้า ระบบได้ตั้งสถานะเป็น \'Rejected\'']);
            }
        }

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
