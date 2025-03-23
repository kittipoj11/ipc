<?php
@session_start();

require_once 'config.php';
require_once 'class/po_class.php';

// $_SESSION['_POST'] = $_POST;
// if (isset($_REQUEST['submit'])) {
$po = new Po();
// print_r($_REQUEST);
// exit;
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insert') {
    $po->insertData($_REQUEST);
    // getPoMainAll($po);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'update') {
    $po->updateData($_REQUEST);
    // getPoMainAll($po);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    $po->deleteData($_REQUEST);
    // getPoMainAll($po);
// } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
//     $rs = $po->getRecordById($_REQUEST['plan_status_id']);
//     echo json_encode($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectperiod') {
    $rs = $po->getPoPeriodByPoId($_REQUEST['po_id']);
    createPeriodTable($rs);

} else {
    // getPoMainAll($po);
}
// }
//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function createPOTable($po) {}


function createPeriodTable($getRs)
{
    try {
        $rs = $getRs;
        $html = '';
        // ถ้าต้องการให้ refresh Table ใหม่ทั้งหมด ให้ยกเลิก comment ส่วนนี้
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
        foreach ($rs as $row) {
            $html .= <<<EOD
                            <tr>
                                <td class="p-0 d-none">{$row['period_id']}</td>
                                <td class="text-center py-0 px-1">{$row['period_number']}</td>
                                <td class="text-right py-0 px-1">{$row['workload_planned_percent']}</td>
                                <td class="text-right py-0 px-1">{$row['interim_payment']}</td>
                                <td class="text-right py-0 px-1">{$row['interim_payment_percent']}</td>
                                <td class="text-left py-0 px-1">{$row['remark']}</td>
                            </tr>
                        EOD;
        }
        // ถ้าต้องการให้ refresh Table ใหม่ทั้งหมด ให้ยกเลิก comment ส่วนนี้ด้วย
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
