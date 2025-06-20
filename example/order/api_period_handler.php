<?php
// api_period_handler.php

header('Content-Type: application/json');

require_once 'class/connection_class.php';
// สมมติว่ามี OrderPeriodRepository สำหรับจัดการข้อมูลงวดงาน
// require_once 'class/OrderPeriodRepository.php';

// เตรียมโครงสร้างการตอบกลับ
$response = ['status' => 'error', 'message' => 'Invalid data provided.'];

// รับข้อมูล JSON ที่เป็น Array มาจาก Request Body
$periodsData = json_decode(file_get_contents('php://input'), true);

if (is_array($periodsData) && !empty($periodsData)) {
    
    $connection = new Connection();
    $pdo = $connection->getDbConnection();
    // $periodRepo = new OrderPeriodRepository($pdo);

    // ★★★ เริ่มต้น Transaction ★★★
    $pdo->beginTransaction();

    try {
        // วนลูปทำงานกับข้อมูลแต่ละแถวที่ส่งมา
        foreach ($periodsData as $record) {
            $action = $record['action'] ?? 'none';

            switch ($action) {
                case 'insert':
                    // สมมติมีเมธอด create ใน Repository
                    // $periodRepo->create($record);
                    error_log("Action: INSERT, Data: " . json_encode($record)); // ตัวอย่างการ log
                    break;
                case 'update':
                    // สมมติมีเมธอด update ใน Repository
                    // $periodRepo->update($record['period_id'], $record);
                    error_log("Action: UPDATE, ID: {$record['period_id']}, Data: " . json_encode($record)); // ตัวอย่างการ log
                    break;
                case 'delete':
                     // สมมติมีเมธอด delete ใน Repository
                    // $periodRepo->delete($record['period_id']);
                    error_log("Action: DELETE, ID: {$record['period_id']}"); // ตัวอย่างการ log
                    break;
            }
        }

        // ★★★ ถ้าทุกอย่างสำเร็จ ให้ Commit Transaction ★★★
        $pdo->commit();
        $response = ['status' => 'success', 'message' => 'บันทึกข้อมูลงวดงานทั้งหมดเรียบร้อยแล้ว'];

    } catch (Exception $e) {
        // ★★★ ถ้ามีข้อผิดพลาดแม้แต่รายการเดียว ให้ Rollback ทั้งหมด ★★★
        $pdo->rollBack();
        $response['message'] = 'เกิดข้อผิดพลาดระหว่างการบันทึก: ' . $e->getMessage();
        // ส่ง HTTP Status Code 500 เพื่อบอกว่ามีข้อผิดพลาดร้ายแรงที่ฝั่ง Server
        http_response_code(500);
    }
}

echo json_encode($response);
exit();

/*
สรุปขั้นตอน
1. Frontend (HTML): เตรียมตารางและปุ่มกด
2. Frontend (JS): เมื่อกดปุ่ม "บันทึก" JavaScript จะวนลูปทุก <tr> ใน <tbody id="periods-tbody">
3. Frontend (JS): ในแต่ละรอบของ Loop จะดึงข้อมูลจาก input ภายในแถวนั้นๆ มาสร้างเป็น object แล้วเก็บลงใน Array periodsData
4. Frontend (JS): เมื่อ Loop จบ จะได้ periodsData เป็น Array ของ Object ที่พร้อมส่ง
5. Frontend (JS): ใช้ fetch ส่ง periodsData ทั้งก้อนไปที่ api_period_handler.php ผ่าน POST request ในรูปแบบ JSON
6. Backend (PHP): รับข้อมูล JSON มาแปลงกลับเป็น Array ของ PHP
7. Backend (PHP): เริ่ม Transaction เพื่อรับประกันว่าถ้าผิดพลาดจะยกเลิกทั้งหมด
8. Backend (PHP): วนลูป Array ที่ได้มา แล้วใช้ switch-case เพื่อเรียกเมธอดที่ถูกต้อง (create, update, delete) สำหรับแต่ละรายการ
9. Backend (PHP): ถ้า Loop จบโดยไม่มีปัญหา ให้ commit Transaction แต่ถ้าเกิด Error ให้ rollBack
10.Backend (PHP): ส่ง response ที่เป็น JSON กลับไปบอกผลลัพธ์ให้ JavaScript ทราบ
*/