<?php
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'config.php';
require_once 'class/connection_class.php';
require_once 'class/po_class.php';
require_once 'class/inspection_class_copy.php';
require_once 'class/ipc_class.php';
require_once 'class/approval_service_class.php';

$requestData = json_decode(file_get_contents('php://input'), true);

$connection = new Connection();
$pdo = $connection->getDbConnection();
$inspection = new Inspection($pdo);
$ipc = new Ipc($pdo);
$inspectionService = new InspectionService($pdo, $inspection, $ipc);

if (isset($requestData['action']) && $requestData['action'] == 'select') {
    $rs = $inspection->getPoMainAll();
    echo json_encode($rs);

} elseif (isset($requestData['action']) && $requestData['action'] == 'selectInspectionPeriodAll') {
    $rs = $inspection->getAllPeriodByPoId($requestData['po_id']);
    echo json_encode($rs);

} elseif (isset($requestData['action']) && $requestData['action'] == 'save') {
    if (!isset($requestData['periodData']) || !isset($requestData['detailsData'])) {
        throw new Exception('Invalid data structure.');
    }
    $savedInspectionId = $inspection->save($requestData['periodData'], $requestData['detailsData']);
    
    $response = [
        'status' => 'success',
        'message' => 'บันทึกข้อมูล PO ID: ' . $savedInspectionId . ' เรียบร้อยแล้ว',
        'data' => ['inspection_id' => $savedInspectionId]
    ];
    // echo "1";
    echo json_encode($response);
    
} elseif (isset($requestData['action']) && $requestData['action'] == 'approveInspection') {
    // $savedInspectionId = $inspection->updateApprovalLevel_old($requestData['approvalData'],$requestData['ipcData']);
    $inspectionService->approveInspection($requestData['approvalData'],$requestData['ipcData']);
    $response = [
        'status' => 'success',
        'message' => 'บันทึกข้อมูล PO ID: ' . $savedInspectionId . ' เรียบร้อยแล้ว',
        'data' => ['inspection_id' => $savedInspectionId]
    ];
    // echo "1";
    echo json_encode($response);


} elseif (isset($requestData['action']) && $requestData['action'] == 'selectInspectionFiles') {
    $rsInspectionFiles = $inspection->getInspectionFilesByInspectionId($requestData['po_id'], $requestData['period_id'], $requestData['inspection_id']);
    echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);

} elseif (isset($requestData['action']) && $requestData['action'] == 'insertInspectionFiles') {
    $inspection->insertInspectionFiles($requestData);
    // echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);

} elseif (isset($requestData['action']) && $requestData['action'] == 'deleteInspectionFiles') {
    $inspection->deleteInspectionFiles($requestData['file_id']);

} else {
    // fetchAll($inspection);
}

