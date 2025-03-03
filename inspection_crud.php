<?php
@session_start();

require_once 'config.php';
require_once 'class/inspection_class.php';

$_SESSION['_REQUEST'] = $_REQUEST;
// if (isset($_REQUEST['submit'])) {

$obj = new Inspection();
// print_r($_REQUEST);
// exit;
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insert') {
    $obj->insertData($_REQUEST);
    // getAllRecord($obj);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'update') {
    $obj->updateData($_REQUEST);
    // getAllRecord($obj);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    $obj->deleteData($_REQUEST);
    // getAllRecord($obj);
// } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
//     $rs = $obj->getRecordById($_REQUEST['plan_status_id']);
//     echo json_encode($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectperiod') {
    $rs = $obj->getPeriodAll($_REQUEST['po_id']);
    createPeriodTable($rs);
} else {
    // getAllRecord($obj);
}
// }
//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function createPOTable($obj) {}

function createPeriodTable($getRs)
{
    try {
        $rs = $getRs;
        $html = '';
        // ถ้าต้องการให้ refresh Table ใหม่ตาราง ให้ยกเลิก comment ส่วนนี้
        // $html .= <<<EOD
        //             <table id="example1" class="table table-bordered table-striped table-sm">
        //                 <thead>
        //                     <tr>
        //                         <th class="text-center" style="width: 100px;">#</th>
        //                         <th class="text-center">Plan_status name</th>
        //                         <th class="text-center" style="width: 120px;">Action</th>
        //                     </tr>
        //                 </thead>
        //                 <tbody id="tbody-period">
        //     EOD;

        // ส่วนของ #tbody-period
        // foreach ($rs as $row) {
        //     $html .= <<<EOD
        //                     <tr data-id='{$row['po_period_id']}'>
        //                         <td class="tdPeriod text-center py-0 px-1"><a class='link-opacity-100 pe-auto' style='margin: 0px 5px 5px 5px'>{$row['period']}</a></td>
        //                         <td class="tdPeriod text-right py-0 px-1">{$row['workload_planned_percent']}</td>
        //                         <td class="tdPeriod text-right py-0 px-1">{$row['workload_actual_completed_percent']}</td>
        //                         <td class="tdPeriod text-right py-0 px-1">{$row['workload_remaining_percent']}</td>
        //                         <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment']}</td>
        //                         <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment_less_previous']}</td>
        //                         <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment_remain']}</td>
        //                         <td class="tdPeriod text-left py-0 px-1">{$row['remark']}</td>
        //                     </tr>
        //                 EOD;
        // }

        foreach ($rs as $row) {
            $html .= <<<EOD
                      <tr data-id={$row['po_period_id']}>
                        <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right po_id" value="{$row['po_id']}" readonly></td>
                        <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right po_period_id" value="{$row['po_period_id']}" readonly></td>
                        <td class="tdPeriod text-right py-0 px-1"><a class="link-opacity-100 pe-auto period" style="margin: 0px 5px 5px 5px">{$row['period']}</a></td>
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
        // print_r($rs);
    } catch (PDOException $e) {
        echo 'Data not found!';
    }
}
