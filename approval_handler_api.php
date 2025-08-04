<?php
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'config.php';
require_once 'class/connection_class.php';

// รับค่า action จาก frontend
$action = $_POST['action'] ?? '';

// แยกการทำงานตาม action ที่ส่งมา
switch ($action) {
    case 'submit_document_a':
        // Logic การ Submit เอกสาร A ครั้งแรก
        // 1. UPDATE documents SET status = 'pending_approval', current_step = 1
        // 2. ค้นหา approver_user_id จาก workflow_steps WHERE step_number = 1
        // 3. UPDATE documents SET current_approver_id = [ID ที่เจอ]
        // 4. บันทึกใน approval_history
        break;

    case 'approve':
        handleApproval($pdo);
        break;

    case 'reject':
        handleRejection($pdo);
        break;

    // ... case อื่นๆ
}

function handleApproval($pdo) {
    $document_id = $_POST['document_id'];
    $user_id = $_POST['user_id']; // ID ของ user ที่ login อยู่
    $comments = $_POST['comments'];

    // 1. ตรวจสอบสิทธิ์ และดึงข้อมูล workflow มาด้วย
    // SELECT d.current_step, d.created_by, w.on_completion_trigger, w.id as workflow_id
    // FROM documents d 
    // JOIN workflows w ON d.workflow_id = w.id
    // WHERE d.id = ?

    // 2. บันทึก history
    // INSERT INTO approval_history (document_id, user_id, action, comments) VALUES (?, ?, 'approve', ?)

    // 3. หา step ต่อไป
    $current_step = "..."; // ดึงมาจากข้อ 1
    $next_step_number = $current_step + 1;

    // 4. ค้นหาผู้อนุมัติคนถัดไป
    // SELECT approver_user_id FROM workflow_steps WHERE workflow_id = ? AND step_number = ?
    
    // 5. ตรวจสอบว่าเป็น step สุดท้ายหรือไม่
    if (true/* ไม่เจอผู้อนุมัติคนถัดไป */) {
        // ... อัปเดตสถานะเอกสารเป็น completed ...

        // เป็นการอนุมัติขั้นสุดท้าย
        // UPDATE documents SET status = 'completed', current_approver_id = NULL WHERE id = ?
        
        // ถ้าเป็นเอกสาร A ให้สร้างเอกสาร B
        $document_type = "..."; // ดึงมาจากข้อ 1
        if ($document_type === 'A') {
            createDocumentB($pdo, $document_id);
        }
        echo json_encode(['status' => 'success', 'message' => 'Document Completed!']);

    } else {
        // ยังมี step ต่อไป
        $next_approver_id = "..."; // ดึงมาจากข้อ 4
        // UPDATE documents SET current_step = ?, current_approver_id = ? WHERE id = ?
        echo json_encode(['status' => 'success', 'message' => 'Approved and sent to the next approver.']);
    }
}

function handleRejection($pdo) {
    $document_id = $_POST['document_id'];
    $user_id = $_POST['user_id'];
    $comments = $_POST['comments'];

    // 1. ตรวจสอบสิทธิ์
    // SELECT current_step, created_by FROM documents WHERE id = ?

    // 2. บันทึก history
    // INSERT INTO approval_history (document_id, user_id, action, comments) VALUES (?, ?, 'reject', ?)

    // 3. หา step ก่อนหน้า
    $current_step = ...; // ดึงมาจากข้อ 1
    if ($current_step == 1) {
        // ส่งกลับไปหาผู้สร้าง (admin)
        $previous_approver_id = ...; // ดึง created_by จากข้อ 1
        // UPDATE documents SET status = 'rejected', current_step = 0, current_approver_id = ? WHERE id = ?
    } else {
        // ส่งกลับไป step ก่อนหน้า
        $previous_step_number = $current_step - 1;
        // SELECT approver_user_id FROM workflow_steps WHERE ... step_number = ?
        $previous_approver_id = ...;
        // UPDATE documents SET status = 'rejected', current_step = ?, current_approver_id = ? WHERE id = ?
    }
    echo json_encode(['status' => 'success', 'message' => 'Document has been rejected.']);
}

function createDocumentB($pdo, $source_document_a_id) {
    // 1. ดึงข้อมูลจากเอกสาร A เพื่อสร้างเอกสาร B
    // SELECT data FROM documents WHERE id = ?

    // 2. สร้างเอกสาร B ในตาราง documents
    // INSERT INTO documents (document_type, data, status, current_step, created_by, ...) VALUES ('B', ?, 'pending_approval', 1, ...)

    // 3. ค้นหาผู้อนุมัติคนแรกของ Workflow เอกสาร B
    // SELECT approver_user_id FROM workflow_steps WHERE workflow_id = [ID ของ workflow B] AND step_number = 1

    // 4. อัปเดต current_approver_id ของเอกสาร B ที่เพิ่งสร้าง
    // UPDATE documents SET current_approver_id = ? WHERE id = [ID ของเอกสาร B ใหม่]
}

?>