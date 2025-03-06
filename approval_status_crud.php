<?php
// @session_start();

require_once 'config.php';
require_once 'class/approval_status_class.php';

$obj = new Approval_status();
// print_r($_REQUEST);
// exit;
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insertdata') {
    $obj->insertData($_REQUEST);
    getRecordAll($obj);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'updatedata') {
    $obj->updateData($_REQUEST);
    getRecordAll($obj);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deletedata') {
    $obj->deleteData($_REQUEST);
    getRecordAll($obj);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectdata') {
    $rs = $obj->getRecordById($_REQUEST['approval_status_id']);
    echo json_encode($rs);
} else {
    getRecordAll($obj);
}

//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function getRecordAll($getObj)
{
    try {
        $rs = $getObj->getRecordAll();

        // foreach ($rs as $key => $row) :
        $html = <<<EOD
                    <table id="example1" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 100px;">#</th>
                                <th class="text-center">Approval_status name</th>
                                <th class="text-center" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
            EOD;
        echo $html;
        foreach ($rs as $row) {
            $html = <<<EOD
                        <tr id="{$row['approval_status_id']}">
                            <td>{$row['approval_status_id']}</td>
                            <td>{$row['approval_status_name']}</td>
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
