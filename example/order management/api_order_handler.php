<?php
// api_order_handler.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'class/connection_class.php';
require_once 'class/OrderRepository.php';

$response = ['status' => 'error', 'message' => 'Invalid Request', 'data' => null];
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = (new Connection())->getDbConnection();
    $repo = new OrderRepository($pdo);

    if ($method === 'GET') {
        $poId = $_GET['po_id'] ?? 0;
        if (empty($poId)) throw new Exception("PO ID is required.");
        $header = $repo->getHeader((int)$poId);
        if (!$header) throw new Exception("PO not found.");

        $response = [
            'status' => 'success',
            'data' => [
                'header' => $header,
                'items' => $repo->getOrderItems((int)$poId),
                'periods' => $repo->getOrderPeriods((int)$poId)
            ]
        ];
    } elseif ($method === 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);
        if (!isset($requestData['header'], $requestData['items'], $requestData['periods'])) {
            throw new Exception('Invalid data structure.');
        }

        $savedPoId = $repo->save($requestData['header'], $requestData['items'], $requestData['periods']);
        $response = ['status' => 'success', 'message' => 'บันทึกข้อมูล PO ID: ' . $savedPoId . ' เรียบร้อยแล้ว', 'data' => ['po_id' => $savedPoId]];
    }
} catch (Exception $e) {
    $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit();
