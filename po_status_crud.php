<?php
// @session_start();
require_once 'config.php';
require_once 'class/connection_class.php';
require_once 'class/po_status_class.php';

// 1. สร้าง Connection
$connection = new Connection();
$pdo = $connection->getDbConnection(); // ดึง PDO object ออกมา

// 2. "ส่ง" PDO object เข้าไปใน class
$po_status = new Po_Status($pdo);

// 3. 
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'create') {
    $id = $po_status->create($_REQUEST);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($id);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'update') {
    $result=$po_status->update($_REQUEST['po_status_id'],$_REQUEST);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($result);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    $result=$po_status->delete($_REQUEST['po_status_id']);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($result);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $po_status->getAll();
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($rs);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectdata') {
    $rs = $po_status->getById($_REQUEST['po_status_id']);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($rs);

} else {
    fetchAll($po_status);
}

//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function getAll($getObj)
{
    try {
        $rs = $getObj->getAll();

        // foreach ($rs as $key => $row) :
        $html = <<<EOD
                    <table id="example1" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 100px;">#</th>
                                <th class="text-center">Po_Status name</th>
                                <th class="text-center" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
            EOD;
        echo $html;
        foreach ($rs as $row) {
            $html = <<<EOD
                        <tr id="{$row['po_status_id']}">
                            <td>{$row['po_status_id']}</td>
                            <td>{$row['po_status_name']}</td>
                            <td align='center'>
                                <div class='btn-group-sm'>
                                    <a class='btn btn-warning btn-sm btnEdit' data-bs-toggle='modal'  data-bs-placement='right' title='Edit' data-bs-target='#openModal' style='margin: 0px 5px 5px 5px'>
                                        <i class='fa-regular fa-pen-to-square'></i>
                                    </a>
                                    <a class='btn btn-danger btn-sm btnDelete' data-bs-toggle='modal'  data-bs-placement='right' title='Delete' data-bs-target='#deleteModal' style='margin: 0px 5px 5px 5px'>
                                        <i class='fa-regular fa-trash-can'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        EOD;
            echo $html;
        }
        $html = <<<EOD
                </tbody>
            </table>
        EOD;
        echo $html;
        // print_r($rs);
    } catch (PDOException $e) {
        echo 'Data not found!';
    }
}
