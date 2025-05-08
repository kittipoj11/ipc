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
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $po->getPoMainAll();
    createTable($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectperiod') {
    $rs = $po->getPoPeriodByPoId($_REQUEST['po_id']);
    createPeriodTable($rs);

} else {
    // getPoMainAll($po);
}
// }
//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function createTable($getRs) {
    try {
        $html = '';
        foreach ($getRs as $row) {
            $html .= <<<EOD
                            <tr data-id='{$row['po_id']}'>
                                <td class="tdMain p-0 d-none">{$row['po_id']}</td>
                                <td class="tdMain p-0"><a class='link-opacity-100 pe-auto po_number' title='Edit' style='margin: 0px 5px 5px 5px' data-id='{$row['po_number']}'>{$row['po_number']}</a></td>
                                <td class="tdMain p-0">{$row['project_name']}</td>
                                <td class="tdMain p-0">{$row['supplier_name']}</td>
                                <td class="tdMain p-0">{$row['location_name']}</td>
                                <td class="tdMain p-0">{$row['working_name_th']}</td>
                                <td class="tdMain p-0 text-right">{$row['contract_value_before']}</td>
                                <td class="tdMain p-0 text-right">{$row['contract_value']}</td>
                                <td class="tdMain p-0 text-right">{$row['number_of_period']}</td>
                                <td class="tdMain p-0 action" align='center'>
                                    <div class='btn-group-sm'>
                                        <a class='btn btn-warning btn-sm btnEdit' style='margin: 0px 5px 5px 5px' data-id='{$row['po_id']}'>
                                            <i class='fa-regular fa-pen-to-square'></i>
                                        </a>
                                        <a class='btn btn-danger btn-sm btnDelete' style='margin: 0px 5px 5px 5px' data-id='{$row['po_id']}'>
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
