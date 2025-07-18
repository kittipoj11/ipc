<?php
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'config.php';
require_once 'class/connection_class.php';
require_once 'class/ipc_class.php';

$connection = new Connection;
$pdo = $connection->getDbConnection();
$ipc = new ipc($pdo);

$requestData = json_decode(file_get_contents('php://input'), true);
// $_SESSION['req data1']=$requestData;

if (isset($requestData['action']) && $requestData['action'] == 'select') {
    $rs = $ipc->getAllPo();
    echo json_encode($rs);

} elseif (isset($requestData['action']) && $requestData['action'] == 'selectIpcPeriodAll') {
    $rs = $ipc->getAllPeriodByPoId($requestData['po_id']);
    echo json_encode($rs);
    
} else {
    // fetchAll($ipc);
}
