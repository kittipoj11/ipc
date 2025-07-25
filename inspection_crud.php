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
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $po->getAll();
    createTable($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectInspectionPeriodAll') {
    $rs = $inspection->getAllPeriodByPoId($_REQUEST['po_id']);
    createPeriodTable($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'updateInspectionPeriod') {
    $rs = $inspection->updateInspectionPeriod($_REQUEST);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'approveInspection') {
    $rs = $inspection->updateApprovalLevel_old($_REQUEST);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectInspectionFiles') {
    $rsInspectionFiles = $inspection->getInspectionFilesByInspectionId($_REQUEST['po_id'], $_REQUEST['period_id'], $_REQUEST['inspection_id']);
    echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insertInspectionFiles') {
    $inspection->insertInspectionFiles($_REQUEST);
    // echo json_encode(['status' => 'success', 'data' => $rsInspectionFiles]);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deleteInspectionFiles') {
    $inspection->deleteInspectionFiles($_REQUEST['file_id']);
} else {
    // fetchAll($inspection);
}


// }
//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function createTable($getRs)
{
    try {
        $html = '';
        foreach ($getRs as $row) {
            $html .= <<<EOD
                            <tr data-po_id='{$row['po_id']}'>
                                <td class="tdMain p-0 d-none">{$row['po_id']}</td>
                                <td class="tdMain p-0"><a class='link-opacity-100 pe-auto po_number' title='Edit' style='margin: 0px 5px 5px 5px' data-po_number='{$row['po_number']}'>{$row['po_number']}</a></td>
                                <td class="tdMain p-0">{$row['project_name']}</td>
                                <td class="tdMain p-0">{$row['supplier_name']}</td>
                                <td class="tdMain p-0">{$row['location_name']}</td>
                                <td class="tdMain p-0">{$row['working_name_th']}</td>
                                <td class="tdMain p-0 text-right">{$row['contract_value']}</td>
                                <td class="tdMain p-0 text-right">{$row['number_of_period']}</td>
                                <td class="tdMain p-0 action d-none" align='center'>
                                    <div class='btn-group-sm'>
                                        <a class='btn btn-warning btn-sm btnEdit' style='margin: 0px 5px 5px 5px' data-po_id='{$row['po_id']}'>
                                            <i class='fa-regular fa-pen-to-square'></i>
                                        </a>
                                        <a class='btn btn-danger btn-sm btnDelete' style='margin: 0px 5px 5px 5px' data-po_id='{$row['po_id']}'>
                                            <i class='fa-regular fa-trash-can'></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        EOD;
        }
        echo $html;
        // print_r($rs);
    } catch (PDOException $e) {
        echo 'Data not found!';
    }
}

function createPeriodTable($getRs)
{
    try {
        $rs = $getRs;
        $html = '';

        foreach ($rs as $row) {
            $html .= <<<EOD
                      <tr data-id={$row['inspection_id']}>
                        <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right po_id" value="{$row['po_id']}" readonly></td>
                        <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right period_id" value="{$row['period_id']}" readonly></td>
                        <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right inspection_id" value="{$row['inspection_id']}" readonly></td>
                        <td class="tdPeriod text-right py-0 px-1"><a class="link-opacity-100 pe-auto period_number" style="margin: 0px 5px 5px 5px">{$row['period_number']}</a></td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['workload_planned_percent']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['workload_actual_completed_percent']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['workload_remaining_percent']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment_less_previous']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment_remain']}</td>
                        <td class="tdPeriod text-left py-0 px-1">{$row['remark']}</td>
                      </tr>
                    EOD;
        }
        // ถ้าต้องการให้ refresh Table ใหม่ทั้งตาราง ให้ยกเลิก comment ส่วนนี้ด้วย
        // $html .= <<<EOD
        //         </tbody>
        //     </table>
        // EOD;
        echo $html;
        // $_SESSION['html'] = $html;
        // print_r($rs);
    } catch (PDOException $e) {
        echo 'Data not found!';
    }
}
