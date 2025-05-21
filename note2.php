<?php

// --- การเชื่อมต่อฐานข้อมูล (เหมือนตัวอย่างก่อนหน้า) ---
$db_host = 'localhost';
$db_name = 'your_database_name';
$db_user = 'your_database_user';
$db_pass = 'your_database_password';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $e->getMessage());
}

// --- ดึงข้อมูลเอกสาร ID ที่ต้องการทราบสถานะ ---
$document_id_to_check = 2; // ตัวอย่าง: ตรวจสอบสถานะของเอกสาร ID 2

// --- ค้นหาขั้นตอนการอนุมัติล่าสุดที่ยังไม่เสร็จสิ้นสำหรับเอกสารนี้ ---
$stmt = $pdo->prepare("SELECT daw.approval_sequence, daw.approver_user_id, daw.approval_status, r.role_name
                        FROM document_approval_workflows daw
                        LEFT JOIN users u ON daw.approver_user_id = u.user_id
                        LEFT JOIN roles r ON u.role_id = r.role_id
                        WHERE daw.document_id = ?
                        ORDER BY daw.approval_sequence DESC
                        LIMIT 1"); // ดึงรายการล่าสุด

$stmt->execute([$document_id_to_check]);
$current_approval_step = $stmt->fetch(PDO::FETCH_ASSOC);

if ($current_approval_step) {
    echo "<h2>สถานะการอนุมัติล่าสุดสำหรับเอกสาร ID: " . $document_id_to_check . "</h2>";
    echo "ลำดับการอนุมัติ: " . $current_approval_step['approval_sequence'] . "<br>";
    echo "สถานะ: " . $current_approval_step['approval_status'] . "<br>";

    if ($current_approval_step['approver_user_id']) {
        echo "ผู้อนุมัติล่าสุด: ผู้ใช้งาน ID " . $current_approval_step['approver_user_id'] . " (บทบาท: " . $current_approval_step['role_name'] . ")<br>";
    } else {
        // ถ้ายังไม่มีผู้อนุมัติในขั้นตอนนี้ อาจจะต้องดูที่ next_approver_role_id
        $stmt_next_role = $pdo->prepare("SELECT r.role_name
                                          FROM document_approval_workflows daw
                                          JOIN roles r ON daw.next_approver_role_id = r.role_id
                                          WHERE daw.document_id = ?
                                          ORDER BY daw.approval_sequence DESC
                                          LIMIT 1");
        $stmt_next_role->execute([$document_id_to_check]);
        $next_role = $stmt_next_role->fetch(PDO::FETCH_ASSOC);

        if ($next_role) {
            echo "รอการอนุมัติจากบทบาท: " . $next_role['role_name'] . "<br>";
        } else {
            echo "กระบวนการอนุมัติอาจจะสิ้นสุดแล้ว หรือมีข้อผิดพลาดในการตั้งค่า Workflow.<br>";
        }
    }
} else {
    echo "ไม่พบข้อมูลการอนุมัติสำหรับเอกสาร ID: " . $document_id_to_check . "<br>";
}

// --- อีกวิธี: ดึงขั้นตอนที่สถานะยังเป็น 'pending' ---
$stmt_pending = $pdo->prepare("SELECT daw.approval_sequence, r.role_name AS next_role
                                FROM document_approval_workflows daw
                                JOIN roles r ON daw.next_approver_role_id = r.role_id
                                WHERE daw.document_id = ? AND daw.approval_status = 'pending'
                                ORDER BY daw.approval_sequence ASC
                                LIMIT 1"); // ดึงขั้นตอนแรกที่ยัง pending

$stmt_pending->execute([$document_id_to_check]);
$next_approval = $stmt_pending->fetch(PDO::FETCH_ASSOC);

if ($next_approval) {
    echo "<hr><h2>ขั้นตอนการอนุมัติถัดไปสำหรับเอกสาร ID: " . $document_id_to_check . "</h2>";
    echo "ลำดับการอนุมัติ: " . $next_approval['approval_sequence'] . "<br>";
    echo "รอการอนุมัติจากบทบาท: " . $next_approval['next_role'] . "<br>";
} else {
    echo "<hr><h2>ขั้นตอนการอนุมัติถัดไปสำหรับเอกสาร ID: " . $document_id_to_check . "</h2>";
    echo "เอกสารนี้อาจจะได้รับการอนุมัติครบทุกขั้นตอนแล้ว หรือยังไม่มีขั้นตอนการอนุมัติที่รออยู่.<br>";
}

?>