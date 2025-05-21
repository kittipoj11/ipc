<?php

// --- การเชื่อมต่อฐานข้อมูล ---
$db_host = 'localhost'; // เปลี่ยนเป็น Host ของฐานข้อมูลคุณ
$db_name = 'your_database_name'; // เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ
$db_user = 'your_database_user'; // เปลี่ยนเป็นชื่อผู้ใช้ฐานข้อมูลของคุณ
$db_pass = 'your_database_password'; // เปลี่ยนเป็นรหัสผ่านฐานข้อมูลของคุณ

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $e->getMessage());
}

// --- 1. เอกสาร A ถูกสร้างโดย admin ---
$admin_user_id = 101; // สมมติว่า ID ของ Admin คือ 101
$document_a_title = "เอกสารสำคัญ A";

$stmt = $pdo->prepare("INSERT INTO documents (document_type, document_title, created_by_user_id) VALUES (?, ?, ?)");
$stmt->execute(['A', $document_a_title, $admin_user_id]);
$document_a_id = $pdo->lastInsertId();

echo "สร้างเอกสาร A หมายเลข: " . $document_a_id . " โดย Admin ID: " . $admin_user_id . "<br>";

// --- 2. เอกสาร A ต้องถูก submit โดย admin ก่อน จึงจะส่งออกไปหา Approver ---
// สมมติว่ามีการอัปเดตสถานะเอกสารก่อนส่ง (อาจมีฟิลด์ 'status' ในตาราง documents)
$stmt = $pdo->prepare("UPDATE documents SET submission_date = NOW() WHERE document_id = ?");
$stmt->execute([$document_a_id]);

$approver_role_id = 2; // สมมติว่า ID ของบทบาท Approver คือ 2

// หาผู้ใช้งานที่มีบทบาทเป็น Approver (อาจมีหลายคน ระบบต้องเลือกว่าจะส่งให้ใคร)
// ในตัวอย่างนี้จะสมมติว่ามี Approver ID = 102
$approver_user_id = 102;

$stmt = $pdo->prepare("INSERT INTO document_approval_workflows (document_id, approval_sequence, approver_user_id) VALUES (?, ?, ?)");
$stmt->execute([$document_a_id, 1, $approver_user_id]);

echo "ส่งเอกสาร A หมายเลข: " . $document_a_id . " ให้ Approver ID: " . $approver_user_id . "<br>";

// --- 3. เมื่อ Approver เห็นเอกสาร A นี้แล้วทำการ approve แล้วระบบจะสร้างเอกสาร B ขึ้นมา ---
// สมมติว่า Approver ID 102 ทำการอนุมัติเอกสาร A
$stmt = $pdo->prepare("UPDATE document_approval_workflows SET approval_status = 'approved', approval_timestamp = NOW() WHERE document_id = ? AND approver_user_id = ?");
$stmt->execute([$document_a_id, $approver_user_id]);

$user1_id = 201; // สมมติว่า ID ของ User1 คือ 201
$document_b_title = "คำขอสำหรับเอกสาร B";

$stmt = $pdo->prepare("INSERT INTO documents (document_type, document_title, created_by_user_id) VALUES (?, ?, ?)");
$stmt->execute(['B', $document_b_title, $user1_id]);
$document_b_id = $pdo->lastInsertId();

echo "Approver ID: " . $approver_user_id . " อนุมัติเอกสาร A และสร้างเอกสาร B หมายเลข: " . $document_b_id . " โดย User ID: " . $user1_id . "<br>";

// --- 4. เอกสาร B ถูก submit โดย user1 และถูกส่งต่อไปให้ Manager ---
$stmt = $pdo->prepare("UPDATE documents SET submission_date = NOW() WHERE document_id = ?");
$stmt->execute([$document_b_id]);

$manager_role_id = 5; // สมมติว่า ID ของบทบาท Manager คือ 5

// สร้าง Workflow สำหรับ User1 (ในฐานะผู้ Submit)
$stmt = $pdo->prepare("INSERT INTO document_approval_workflows (document_id, approval_sequence, approver_user_id, approval_status) VALUES (?, ?, ?, ?)");
$stmt->execute([$document_b_id, 1, $user1_id, 'approved']); // ถือว่าการ Submit คือการ Approve ในขั้นแรก

// สร้าง Workflow สำหรับ Manager
$stmt = $pdo->prepare("INSERT INTO document_approval_workflows (document_id, approval_sequence, next_approver_role_id, approval_status) VALUES (?, ?, ?, ?)");
$stmt->execute([$document_b_id, 2, $manager_role_id, 'pending']);

echo "User ID: " . $user1_id . " สร้างและ Submit เอกสาร B หมายเลข: " . $document_b_id . " ส่งต่อไปให้ Manager (Role ID: " . $manager_role_id . ")<br>";

// --- 5. เอกสาร B ถูก approve ในขั้นแรกโดย Assistant Manager และถูกส่งต่อไปให้ Manager ---
$assistant_manager_role_id = 4; // สมมติว่า ID ของบทบาท Assistant Manager คือ 4

// หา Assistant Manager ที่ต้องอนุมัติ (สมมติว่า ID = 202)
$assistant_manager_user_id = 202;

// สมมติว่า Assistant Manager อนุมัติ
$stmt = $pdo->prepare("UPDATE document_approval_workflows SET approver_user_id = ?, approval_status = 'approved', approval_timestamp = NOW() WHERE document_id = ? AND next_approver_role_id = ? AND approval_sequence = 2");
$stmt->execute([$assistant_manager_user_id, $document_b_id, $assistant_manager_role_id]);

// สร้าง Workflow สำหรับ Manager (ขั้นที่ 3)
$stmt = $pdo->prepare("INSERT INTO document_approval_workflows (document_id, approval_sequence, next_approver_role_id, approval_status) VALUES (?, ?, ?, ?)");
$stmt->execute([$document_b_id, 3, $manager_role_id, 'pending']);

echo "Assistant Manager ID: " . $assistant_manager_user_id . " อนุมัติเอกสาร B ส่งต่อไปให้ Manager (Role ID: " . $manager_role_id . ")<br>";

// --- 6. เอกสาร B ถูก approve ในขั้นที่ 2 โดย Manager และถูกส่งต่อไปให้ Diractor ---
$manager_user_id = 203; // สมมติว่า ID ของ Manager คือ 203
$director_role_id = 6; // สมมติว่า ID ของบทบาท Director คือ 6

// สมมติว่า Manager อนุมัติ
$stmt = $pdo->prepare("UPDATE document_approval_workflows SET approver_user_id = ?, approval_status = 'approved', approval_timestamp = NOW() WHERE document_id = ? AND next_approver_role_id = ? AND approval_sequence = 3");
$stmt->execute([$manager_user_id, $document_b_id, $manager_role_id]);

// สร้าง Workflow สำหรับ Director (ขั้นที่ 4)
$stmt = $pdo->prepare("INSERT INTO document_approval_workflows (document_id, approval_sequence, next_approver_role_id, approval_status) VALUES (?, ?, ?, ?)");
$stmt->execute([$document_b_id, 4, $director_role_id, 'pending']);

echo "Manager ID: " . $manager_user_id . " อนุมัติเอกสาร B ส่งต่อไปให้ Director (Role ID: " . $director_role_id . ")<br>";

// --- 7. เอกสาร B ถูก approve ในขั้นสุดท้าย โดย Diractor ---
$director_user_id = 204; // สมมติว่า ID ของ Director คือ 204

// สมมติว่า Director อนุมัติ
$stmt = $pdo->prepare("UPDATE document_approval_workflows SET approver_user_id = ?, approval_status = 'approved', approval_timestamp = NOW(), next_approver_role_id = NULL WHERE document_id = ? AND next_approver_role_id = ? AND approval_sequence = 4");
$stmt->execute([$director_user_id, $document_b_id, $director_role_id]);

echo "Director ID: " . $director_user_id . " อนุมัติเอกสาร B ขั้นตอนสุดท้าย<br>";

// --- 8. จบการทำงาน ---
echo "กระบวนการทำงานสิ้นสุดสำหรับเอกสาร B หมายเลข: " . $document_b_id . "<br>";

?>