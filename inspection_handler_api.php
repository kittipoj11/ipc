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

$connection = new Connection();
$pdo = $connection->getDbConnection();
$po = new Po($pdo);
$inspection = new Inspection($pdo);

$requestData = json_decode(file_get_contents('php://input'), true);
// $_SESSION['req data1']=$requestData;

if (isset($requestData['action']) && $requestData['action'] == 'select') {
    $rs = $po->fetchAll();
    echo json_encode($rs);

} elseif (isset($requestData['action']) && $requestData['action'] == 'selectInspectionPeriodAll') {
    $rs = $inspection->getInspectionPeriodAllByPoId($requestData['po_id']);
    echo json_encode($rs);

} elseif (isset($requestData['action']) && $requestData['action'] == 'updateInspectionPeriod') {
    $rs = $inspection->updateInspectionPeriod($requestData);
    
} elseif (isset($requestData['action']) && $requestData['action'] == 'updateCurrentApprovalLevel') {
    $rs = $inspection->updateCurrentApprovalLevel($requestData);

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

