<?php
// api_period_handler.php (เวอร์ชันปรับปรุง)

// สำหรับการ Debug ในตอนพัฒนา
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
    $repo = new OrderPeriodRepository($pdo);

    if ($method === 'POST') {
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
        $poId = $_GET['po_id'] ?? 0;
        if (empty($poId)) throw new Exception("PO ID is required.");

        $header = $repo->getHeader((int)$poId);
        $details = $repo->getDetails((int)$poId);

        if (!$header) throw new Exception("PO not found.");

        $response = ['status' => 'success', 'data' => ['header' => $header, 'details' => $details]];
    } elseif ($method === 'POST') {
        // --- ส่วนของการบันทึกข้อมูล ---
        $requestData = json_decode(file_get_contents('php://input'), true);

        if (!isset($requestData['header']) || !isset($requestData['details'])) {
            throw new Exception('Invalid data structure.');
        }

        // ในระบบจริง poId ควรมาจาก session หรือค่าที่น่าเชื่อถือ
        $headerData = $requestData['header'];
        $detailsData = $requestData['details'];
        $poId = $headerData['po_id'] ?? 0;


        if (empty($poId)) throw new Exception("PO ID is missing in header data.");

        // ★★★ เรียกใช้เมธอดจาก Repository ★★★
        $isSuccess = $repo->processBatch((int)$poId, $headerData, $detailsData);

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
