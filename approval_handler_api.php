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


// --- ฟังก์ชัน Helper สำหรับบันทึกประวัติ ---
function recordHistory($pdo, $doc_id, $user_id, $action, $comments = '') {
    $sql = "INSERT INTO approval_history (document_id, user_id, action, comments) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$doc_id, $user_id, $action, $comments]);
}

// --- ส่วนจัดการ Action หลัก ---
$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 1; // สมมติ user_id=1 คือ Admin ที่ล็อกอินอยู่

if ($user_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required.']);
    exit();
}

switch ($action) {
    case 'submit_document':
        $doc_id = $_POST['document_id'];
        submitDocument($pdo, $doc_id, $user_id);
        break;
    case 'approve':
        $doc_id = $_POST['document_id'];
        $comments = $_POST['comments'];
        approveDocument($pdo, $doc_id, $user_id, $comments);
        break;
    case 'reject':
        $doc_id = $_POST['document_id'];
        $comments = $_POST['comments'];
        rejectDocument($pdo, $doc_id, $user_id, $comments);
        break;
}

// --- ฟังก์ชันการทำงาน ---

function submitDocument($pdo, $doc_id, $user_id) {
    $stmt = $pdo->prepare("SELECT workflow_id FROM documents WHERE id = ? AND created_by = ? AND status = 'draft'");
    $stmt->execute([$doc_id, $user_id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) { /* ... error handling ... */ return; }

    $stmt = $pdo->prepare("SELECT approver_user_id FROM workflow_steps WHERE workflow_id = ? AND step_number = 1");
    $stmt->execute([$doc['workflow_id']]);
    $first_step = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$first_step) { /* ... error handling ... */ return; }

    $stmt = $pdo->prepare("UPDATE documents SET status = 'pending_approval', current_step = 1, current_approver_id = ? WHERE id = ?");
    $stmt->execute([$first_step['approver_user_id'], $doc_id]);

    // **บันทึกประวัติการ Submit**
    recordHistory($pdo, $doc_id, $user_id, 'submitted');

    echo json_encode(['status' => 'success', 'message' => 'Document submitted successfully.']);
}


function approveDocument($pdo, $doc_id, $user_id, $comments) {
    $sql = "SELECT d.current_step, d.workflow_id, w.next_workflow_id
            FROM documents d JOIN workflows w ON d.workflow_id = w.id
            WHERE d.id = ? AND d.current_approver_id = ? AND d.status = 'pending_approval'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$doc_id, $user_id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doc) { /* ... error handling ... */ return; }

    // **บันทึกประวัติการ Approve ก่อน**
    recordHistory($pdo, $doc_id, $user_id, 'approved', $comments);

    $next_step_number = $doc['current_step'] + 1;
    $stmt = $pdo->prepare("SELECT approver_user_id FROM workflow_steps WHERE workflow_id = ? AND step_number = ?");
    $stmt->execute([$doc['workflow_id'], $next_step_number]);
    $next_step = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($next_step) {
        // ยังมี step ต่อไป
        $stmt = $pdo->prepare("UPDATE documents SET current_step = ?, current_approver_id = ? WHERE id = ?");
        $stmt->execute([$next_step_number, $next_step['approver_user_id'], $doc_id]);
        echo json_encode(['status' => 'success', 'message' => 'Approved and forwarded.']);
    } else {
        // อนุมัติขั้นสุดท้าย
        $stmt = $pdo->prepare("UPDATE documents SET status = 'completed', current_approver_id = NULL WHERE id = ?");
        $stmt->execute([$doc_id]);

        if (!empty($doc['next_workflow_id'])) {
            $new_workflow_id = $doc['next_workflow_id'];
            $stmt = $pdo->prepare("SELECT approver_user_id FROM workflow_steps WHERE workflow_id = ? AND step_number = 1");
            $stmt->execute([$new_workflow_id]);
            $new_first_approver = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("INSERT INTO documents (workflow_id, data, status, current_step, current_approver_id, created_by) VALUES (?, '{}', 'pending_approval', 1, ?, ?)");
            $stmt->execute([$new_workflow_id, $new_first_approver['approver_user_id'], $user_id]);
            $new_doc_id = $pdo->lastInsertId();

            // **บันทึกประวัติการสร้างเอกสารใหม่อัตโนมัติ**
            recordHistory($pdo, $new_doc_id, $user_id, 'created_auto');
        }
        echo json_encode(['status' => 'success', 'message' => 'Final approval complete!']);
    }
}


function rejectDocument($pdo, $doc_id, $user_id, $comments) {
    if (empty($comments)) {
        echo json_encode(['status' => 'error', 'message' => 'Comments are required for rejection.']);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT current_step, created_by, workflow_id FROM documents WHERE id = ? AND current_approver_id = ?");
    $stmt->execute([$doc_id, $user_id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) { /* ... error handling ... */ return; }

    // **บันทึกประวัติการ Reject ก่อน**
    recordHistory($pdo, $doc_id, $user_id, 'rejected', $comments);
    
    $previous_step_number = $doc['current_step'] - 1;

    if ($previous_step_number < 1) {
        // ส่งกลับไปหาผู้สร้าง
        $stmt = $pdo->prepare("UPDATE documents SET status = 'rejected', current_step = 0, current_approver_id = created_by WHERE id = ?");
        $stmt->execute([$doc_id]);
    } else {
        // ส่งกลับไปหาผู้อนุมัติคนก่อนหน้า
        $stmt = $pdo->prepare("SELECT approver_user_id FROM workflow_steps WHERE workflow_id = ? AND step_number = ?");
        $stmt->execute([$doc['workflow_id'], $previous_step_number]);
        $previous_approver = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("UPDATE documents SET status = 'rejected', current_step = ?, current_approver_id = ? WHERE id = ?");
        $stmt->execute([$previous_step_number, $previous_approver['approver_user_id'], $doc_id]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Document rejected.']);


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