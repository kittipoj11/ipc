<?php
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'config.php';
require_once 'class/connection_class.php';
require_once 'class/po_class.php';
require_once 'class/inspection_class.php';
require_once 'class/ipc_class.php';
require_once 'class/workflows_class.php';
require_once 'class/inspection_service_class.php';

$requestData = json_decode(file_get_contents('php://input'), true);

$userId = $_SESSION['user_id'] ?? 0;


if (isset($requestData['action']) && $userId > 0) {
    $connection = new Connection();
    $pdo = $connection->getDbConnection();

    $po = new Po($pdo);
    $inspection = new Inspection($pdo);
    $ipc = new Ipc($pdo);
    $workflow = new Workflows($pdo);
    $inspectionService = new InspectionService($pdo, $po, $inspection, $ipc, $workflow);

    switch ($requestData['action']) {
        case 'approve':
            // $ipcId = $inspection->save($requestData['periodData'], $requestData['detailsData']);
            $ipcId = $inspectionService->approveIpc($requestData['ipcId']);
            $response = [
                'status' => 'success',
                'message' => 'อนุมัติ IPC ID: ' . $ipcId . ' เรียบร้อยแล้ว',
                'data' => ['ipc_id' => $ipcId]
            ];
            echo json_encode($response);
            break;

        case 'reject':
            // $ipcId = $inspection->save($requestData['periodData'], $requestData['detailsData']);
            $ipcId = $inspectionService->rejectInspection($requestData['ipcId'], $requestData['comments']);
            $response = [
                'status' => 'success',
                'message' => 'ไม่อนุมัติ IPC ID: ' . $ipcId,
                'data' => ['ipc_id' => $ipcId]
            ];
            echo json_encode($response);
            break;

        case 'select':
            $rs = $ipc->getPoMainAll();
            echo json_encode($rs);
            break;

        case 'selectIpcPeriodAll':
            $rs = $ipc->getIpcAllByPoId($requestData['po_id']);
            echo json_encode($rs);
            break;
            
        case 'previewIpc':
            $rs = $ipc->getIpcByIpcId($requestData['ipcId']);
            $_SESSION['rs'] = $rs;
            echo json_encode($rs);
            break;

        case 'previewInspection':
            $rs = $ipc->getIpcByIpcId($requestData['ipcId']);
            $_SESSION['rs'] = $rs;
            echo json_encode($rs);
            break;
        default:
    }
}
