<?php
// @session_start();
require_once '../config.php';
require_once '../class/connection_class.php';
require_once '../class/approval_type_class.php';

// 1. สร้าง Connection
$connection = new Connection();
$pdo = $connection->getDbConnection(); // ดึง PDO object ออกมา

// 2. "ส่ง" PDO object เข้าไปใน class
$approval_type = new Approval_Type($pdo);

// 3. 
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insertdata') {
    $id = $approval_type->create($_REQUEST);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($id);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'updatedata') {
    $result=$approval_type->update($_REQUEST['approval_type_id'],$_REQUEST);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($result);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deletedata') {
    $result=$approval_type->delete($_REQUEST['approval_type_id']);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($result);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $approval_type->fetchAll();
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($rs);

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectdata') {
    $rs = $approval_type->fetchById($_REQUEST['approval_type_id']);
    // 4. กำหนด Content-Type เป็น application/json
    header('Content-Type: application/json');

    // 5. ส่งผลลัพธ์กลับไปเป็น JSON
    echo json_encode($rs);

} else {
    fetchAll($approval_type);
}

//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function fetchAll($getObj)
{
    try {
        $rs = $getObj->fetchAll();

        // foreach ($rs as $key => $row) :
        $html = <<<EOD
                    <table id="example1" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 100px;">#</th>
                                <th class="text-center">Approval_Type name</th>
                                <th class="text-center" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
            EOD;
        echo $html;
        foreach ($rs as $row) {
            $html = <<<EOD
                        <tr id="{$row['approval_type_id']}">
                            <td>{$row['approval_type_id']}</td>
                            <td>{$row['approval_type_name']}</td>
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
