<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $work_type = $_POST['work_type'];
    $created_by = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO work_items (title, description, work_type, created_by) VALUES (:title, :description, :work_type, :created_by)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':work_type', $work_type);
        $stmt->bindParam(':created_by', $created_by);
        $stmt->execute();
        $work_item_id = $pdo->lastInsertId();

        // ดึงข้อมูลผู้อนุมัติตามลำดับจาก workflow_steps
        $stmtWorkflow = $pdo->prepare("SELECT approver_id, approval_level FROM workflow_steps WHERE workflow_id = (SELECT id FROM workflows WHERE work_type = :work_type) ORDER BY approval_level ASC");
        $stmtWorkflow->bindParam(':work_type', $work_type);
        $stmtWorkflow->execute();
        $workflowSteps = $stmtWorkflow->fetchAll(PDO::FETCH_ASSOC);

        // เพิ่มรายการอนุมัติเริ่มต้นในตาราง work_item_approvals
        foreach ($workflowSteps as $step) {
            $stmtApproval = $pdo->prepare("INSERT INTO work_item_approvals (work_item_id, approval_level, approver_id) VALUES (:work_item_id, :approval_level, :approver_id)");
            $stmtApproval->bindParam(':work_item_id', $work_item_id);
            $stmtApproval->bindParam(':approval_level', $step['approval_level']);
            $stmtApproval->bindParam(':approver_id', $step['approver_id']);
            $stmtApproval->execute();
        }

        echo json_encode(['success' => true, 'message' => 'สร้างงานสำเร็จ!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการสร้างงาน: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
