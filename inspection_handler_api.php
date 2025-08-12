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

    $inspection = new Inspection($pdo);
    $ipc = new Ipc($pdo);
    $workflow = new Workflows($pdo);
    $inspectionService = new InspectionService($pdo, $inspection, $ipc,$workflow);

    switch ($requestData['action']) {
        case 'save':
            // $savedInspectionId = $inspection->save($requestData['periodData'], $requestData['detailsData']);
            $savedInspectionId = $inspectionService->saveInspection($requestData['periodData'], $requestData['detailsData']);
            $response = [
                'status' => 'success',
                'message' => 'บันทึกข้อมูล PO ID: ' . $savedInspectionId . ' เรียบร้อยแล้ว',
                'data' => ['inspection_id' => $savedInspectionId]
            ];
            echo json_encode($response);
            break;

        case 'select':
            $rs = $inspection->getAllPo();
            echo json_encode($rs);
            break;

        case 'selectInspectionPeriodAll':
            $rs = $inspection->getAllPeriodByPoId($requestData['po_id']);
            echo json_encode($rs);
            break;

        case 'approveInspection':
            $inspectionService->approveInspection($requestData['approvalData'], $requestData['ipcData']);
            $response = [
                'status' => 'success',
                'message' => 'บันทึกข้อมูล PO ID: ' . $savedInspectionId . ' เรียบร้อยแล้ว',
                'data' => ['inspection_id' => $savedInspectionId]
            ];
            echo json_encode($response);
            break;
        case 'selectInspectionFiles':
            $rsInspectionFiles = $inspection->getInspectionFilesByInspectionId($requestData['po_id'], $requestData['period_id'], $requestData['inspection_id']);
            echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);
            break;
        case 'insertInspectionFiles':
            $inspection->insertInspectionFiles($requestData);
            break;
        case 'deleteInspectionFiles':
            $inspection->deleteInspectionFiles($requestData['file_id']);
            break;
        default:
    }
}
