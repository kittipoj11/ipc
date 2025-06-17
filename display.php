<?php

@session_start();

require_once 'config.php';
require_once 'class/po_class.php';

// $_SESSION['_REQUEST'] = $_REQUEST;
// if (isset($_REQUEST['submit'])) {

$obj = new Po();
// $rsInspectionFiles = $po->getInspectionFilesByInspectionId($po_id, $period_id, $inspection_id);
// $rsInspectionFiles = $obj->getInspectionFilesByInspectionId(1,1,1);

echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);
// echo 'xxx';

