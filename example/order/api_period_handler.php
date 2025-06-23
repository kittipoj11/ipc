<?php
// api_period_handler.php (เวอร์ชันปรับปรุง)

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// เรียกใช้ Class ที่จำเป็น
require_once 'class/connection_class.php';
require_once 'class/OrderPeriodRepository.php'; // ★★★ เรียกใช้ Class ใหม่ ★★★

$response = ['status' => 'error', 'message' => 'Invalid Request', 'data' => null];
$method = $_SERVER['REQUEST_METHOD'];

try {
    // 1. สร้าง Connection และ Repository object
    $pdo = (new Connection())->getDbConnection();
    $periodRepo = new OrderPeriodRepository($pdo);

    if ($method === 'GET') {
        // --- ส่วนของการดึงข้อมูล ---
        $orderId = $_GET['order_id'] ?? 0;
        
        // ★★★ เรียกใช้เมธอดจาก Repository ★★★
        $periods = $periodRepo->getForOrder((int)$orderId);
        
        $response = ['status' => 'success', 'data' => $periods];

    } elseif ($method === 'POST') {
        // --- ส่วนของการบันทึกข้อมูล ---
        $requestData = json_decode(file_get_contents('php://input'), true);

        if (!is_array($requestData)) {
            throw new Exception('Invalid JSON data provided.');
        }

        // ในระบบจริง order_id ควรมาจาก session หรือค่าที่น่าเชื่อถือ
        // ในที่นี้เราจะสมมติว่ามันถูกส่งมาด้วยเพื่อความง่าย
        $orderId = 1; 

        // ★★★ เรียกใช้เมธอดจาก Repository ★★★
        $isSuccess = $periodRepo->processBatch($orderId, $requestData);

        if ($isSuccess) {
            $response = ['status' => 'success', 'message' => 'บันทึกข้อมูลทั้งหมดเรียบร้อยแล้ว'];
        } else {
            // โดยปกติถ้าล้มเหลว มันจะโยน Exception ไปที่ catch block
            $response['message'] = 'การบันทึกล้มเหลวโดยไม่ทราบสาเหตุ';
        }
    }

} catch (Exception $e) {
    // ดักจับ Exception ที่อาจจะโยนมาจาก Repository
    $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit();