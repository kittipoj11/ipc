<?php
require_once '../config.php';
require_once '../class/connection_class.php';
require_once '../class/department_class.php';

// 1. สร้าง Connection
$connection = new Connection();
$pdo = $connection->getDbConnection(); // ดึง PDO object ออกมา

// 2. "ส่ง" PDO object เข้าไปใน class
$department = new Department($pdo);

// 3. 
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'create') {
    $id = $department->create($_REQUEST);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($id);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'update') {
    $result=$department->update($_REQUEST['department_id'],$_REQUEST);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($result);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    // เรียกใช้เมธอดจาก class ซึ่งจะคืนค่าเป็น true/false
    $result=$department->delete($_REQUEST['department_id']);
    // นำผลลัพธ์ (true/false) มาสร้างเป็น response array ที่สื่อความหมาย
    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'ลบ ID: ' . $_REQUEST['department_id'] . ' สำเร็จ';
    } else {
        // status เป็น error อยู่แล้ว
        $response['message'] = 'การลบผู้ใช้ล้มเหลว หรือไม่พบผู้ใช้ ID: ' . $_REQUEST['department_id'];
    }
    // ★★★ ส่วนของการส่งค่ากลับไปให้ AJAX ★★★
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. แปลง array เป็น JSON string แล้ว echo ออกไป(ส่งผลลัพธ์กลับไปเป็น JSON)
    echo json_encode($response);

    // 6. จบการทำงานทันที

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $department->getAll();
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($rs);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectdata') {
    $rs = $department->getById($_REQUEST['department_id']);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($rs);

} else {
    fetchAll($department);
}

/*
ทำไมต้องใช้ header() และ json_encode()?
header('Content-Type: application/json');

หน้าที่: เป็นการ "ติดป้าย" บอกเบราว์เซอร์หรือโค้ดที่เรียกใช้ (AJAX) ว่า "ข้อมูลที่ฉันกำลังจะส่งคืนไปให้นี้ ไม่ใช่หน้าเว็บ HTML ทั่วไปนะ แต่เป็นข้อมูลในรูปแบบ JSON"
ประโยชน์: เมื่อ AJAX ได้รับการตอบกลับ (Response) มันจะรู้ทันทีว่าต้องจัดการข้อมูลนี้ในรูปแบบ JSON ทำให้ Library อย่าง jQuery หรือ fetch API ของ JavaScript 
สามารถแปลงข้อมูลกลับเป็น JavaScript Object ได้โดยอัตโนมัติและง่ายดาย
echo json_encode($response_array);

หน้าที่: แปลงข้อมูลของฝั่ง PHP (ซึ่งมักจะเป็น Array หรือ Object) ให้อยู่ในรูปแบบ "ข้อความ" (String) ที่มีโครงสร้างแบบ JSON
ประโยชน์: JavaScript ไม่สามารถเข้าใจโครงสร้าง Array ของ PHP ได้โดยตรง แต่ ทั้ง PHP และ JavaScript เข้าใจภาษา JSON เหมือนกัน 
JSON จึงทำหน้าที่เป็น "ภาษากลาง" ในการแลกเปลี่ยนข้อมูลระหว่าง Server (PHP) และ Client (JavaScript)

สิ่งสำคัญคือต้องวางโค้ด 2 บรรทัดนี้ให้ถูกที่ครับ มันไม่ควรอยู่ใน Class (user_class) 
แต่ควรอยู่ในไฟล์ที่ทำหน้าที่เป็น API Endpoint (ไฟล์ที่ AJAX เรียกมา)
ในที่นี้ไฟล์ department_crud.php เป็นไฟล์ที่ AJAX เรียก
*/
