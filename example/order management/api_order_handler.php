<?php
// api_order_handler.php
ini_set('display_errors', 1); error_reporting(E_ALL);
header('Content-Type: application/json');

require_once 'class/connection_class.php';
require_once 'class/OrderRepository.php'; // เปลี่ยนชื่อ Class ที่เรียกใช้

$response = ['status' => 'error', 'message' => 'Invalid Request', 'data' => null];
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = (new Connection())->getDbConnection();
    $repo = new OrderRepository($pdo); // สร้าง object จาก Class ใหม่

    if ($method === 'GET') {
        $poId = $_GET['po_id'] ?? 0;
        if (empty($poId)) throw new Exception("PO ID is required.");

        $header = $repo->getHeader((int)$poId);
        if (!$header) throw new Exception("PO not found.");

        $items = $repo->getOrderItems((int)$poId); // ดึง Items
        $periods = $repo->getOrderPeriods((int)$poId); // ดึง Periods

        // ส่งข้อมูลทั้ง 3 ส่วนกลับไป
        $response = ['status' => 'success', 'data' => ['header' => $header, 'items' => $items, 'periods' => $periods]];

    } elseif ($method === 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);
        if (!isset($requestData['header']) || !isset($requestData['items']) || !isset($requestData['periods'])) {
            throw new Exception('Invalid data structure. "header", "items", and "periods" are required.');
        }

        // ส่งข้อมูลทั้ง 3 ส่วนเข้าไปในเมธอด save
        $savedPoId = $repo->save($requestData['header'], $requestData['items'], $requestData['periods']);

        $response = ['status' => 'success', 'message' => 'บันทึกข้อมูล PO ID: ' . $savedPoId . ' เรียบร้อยแล้ว', 'data' => ['po_id' => $savedPoId]];
    }

} catch (Exception $e) {
    $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit();