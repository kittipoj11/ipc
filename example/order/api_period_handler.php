<?php
// api_period_handler.php (เวอร์ชันปรับปรุง)

// สำหรับการ Debug ในตอนพัฒนา
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// เรียกใช้ Class ที่จำเป็น
require_once 'class/connection_class.php';
require_once 'class/OrderPeriodRepository.php'; // ★★★ เรียกใช้ Class ใหม่ ★★★

// เตรียมโครงสร้างการตอบกลับ
$response = ['status' => 'error', 'message' => 'Invalid Request', 'data' => null];
$method = $_SERVER['REQUEST_METHOD'];

try {
    // 1. สร้าง Connection และ Repository object
    $pdo = (new Connection())->getDbConnection();
    $repo = new OrderPeriodRepository($pdo);

    if ($method === 'POST') {
        // --- ส่วนของการรับข้อมูลมาบันทึก ---
        $requestData = json_decode(file_get_contents('php://input'), true);

        if (!isset($requestData['header']) || !isset($requestData['details'])) {
            throw new Exception('Invalid data structure.');
        }

        // ★★★ เรียกใช้เมธอด save เดียว จบ! ★★★
        $savedPoId = $repo->save($requestData['header'], $requestData['details']);

        $response = [
            'status' => 'success', 
            'message' => 'บันทึกข้อมูล PO ID: ' . $savedPoId . ' เรียบร้อยแล้ว',
            'data' => ['po_id' => $savedPoId]
        ];
    }

    if ($method === 'GET') {
        // --- ส่วนของการดึงข้อมูลไปแสดงตอนเปิดหน้าครั้งแรก ---
        $poId = $_GET['po_id'] ?? 0;
        if (empty($poId)) throw new Exception("PO ID is required.");

        $header = $repo->getHeader((int)$poId);

        if (!$header) throw new Exception("PO not found.");

        $details = $repo->getDetails((int)$poId);
        $response = ['status' => 'success', 'data' => ['header' => $header, 'details' => $details]];
    } elseif ($method === 'POST') {
        // --- ส่วนของการบันทึกข้อมูล ---
        $requestData = json_decode(file_get_contents('php://input'), true);

        if (!isset($requestData['header']) || !isset($requestData['details'])) {
            throw new Exception('Invalid data structure.');
        }

        $savedPoId = $repo->save($requestData['header'], $requestData['details']);

        $response = [
            'status' => 'success', 
            'message' => 'บันทึกข้อมูล PO ID: ' . $savedPoId . ' เรียบร้อยแล้ว',
            'data' => ['po_id' => $savedPoId]
        ];
    }
} catch (Exception $e) {
    // ดักจับ Exception ที่อาจจะโยนมาจาก Repository
    $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit();
