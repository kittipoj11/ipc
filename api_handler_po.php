<?php
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'config.php';
require_once 'class/connection_class.php';
require_once 'class/po_class.php';

$connection = new Connection;
$pdo = $connection->getDbConnection();
$po = new Po($pdo);


$requestData = json_decode(file_get_contents('php://input'), true);
// $_SESSION['req data']=$requestData;


if (isset($requestData['action']) && $requestData['action'] == 'save') {
    if (!isset($requestData['headerData']) || !isset($requestData['periodsData'])) {
        throw new Exception('Invalid data structure.');
    }
    // ★★★ เรียกใช้เมธอด save เดียว จบ! ★★★
    $savedPoId = $po->save($requestData['headerData'], $requestData['periodsData']);
    $_SESSION['savedPoId']=$savedPoId;

    $response = [
        'status' => 'success',
        'message' => 'บันทึกข้อมูล PO ID: ' . $savedPoId . ' เรียบร้อยแล้ว',
        'data' => ['po_id' => $savedPoId]
    ];
    echo "1";
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    $id = $po->delete($_REQUEST['po_id']);
    // header('Content-Type: application/json');//ประกาศอยู่ด้านบนแล้ว
    echo json_encode($id);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $po->fetchAll();
    // header('Content-Type: application/json');//ประกาศอยู่ด้านบนแล้ว
    echo json_encode($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectperiod') {
    $rs = $po->fetchAllPeriodByPoId($_REQUEST['po_id']);
    createPeriodTable($rs);
} else {
    // header('Content-Type: application/json');//ประกาศอยู่ด้านบนแล้ว
    echo json_encode($id);
}
// }
//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่

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
