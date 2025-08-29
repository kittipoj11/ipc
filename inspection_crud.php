<?php

@session_start();

require_once 'config.php';
require_once 'class/connection_class.php';
require_once 'class/po_class.php';
require_once 'class/inspection_class.php';

//$_SESSION['_REQUEST'] = $_REQUEST;
// if (isset($_REQUEST['submit'])) {

$connection = new Connection();
$pdo = $connection->getDbConnection();
$po = new Po($pdo);
$inspection = new Inspection($pdo);
// print_r($_REQUEST);
// exit;
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectInspectionFiles') {
    $rsInspectionFiles = $inspection->getInspectionFilesByInspectionId($_REQUEST['inspection_id']);
    echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insertInspectionFiles') {
    $inspection->insertInspectionFiles($_REQUEST);
    // echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deleteInspectionFiles') {
    $inspection->deleteInspectionFiles($_REQUEST['file_id']);
} else {
    // fetchAll($inspection);
}



