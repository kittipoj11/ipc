<?php
// @session_start();

require_once 'config.php';
require_once 'class/plan_status_class.php';

$plan_status = new Plan_status();
// print_r($_REQUEST);
// exit;
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insertdata') {
    $plan_status->insertData($_REQUEST);
    // fetchAll($plan_status);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'updatedata') {
    $plan_status->updateData($_REQUEST);
    // fetchAll($plan_status);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deletedata') {
    $plan_status->deleteData($_REQUEST);
    // fetchAll($plan_status);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $plan_status->fetchAll();
    createTable($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectdata') {
    $rs = $plan_status->fetchById($_REQUEST['plan_status_id']);
    echo json_encode($rs);
} else {
    fetchAll($plan_status);
}

//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function createTable($getRs) {
    try {
        $html = '';
        foreach ($getRs as $row) {
            $html .= <<<EOD
                        <tr id="{$row['plan_status_id']}">
                            <td>{$row['plan_status_id']}</td>
                            <td>{$row['plan_status_name']}</td>
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
          }
        echo $html;
        // print_r($rs);
    } catch (PDOException $e) {
        echo 'Data not found!';
    }
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
                                <th class="text-center">Plan_status name</th>
                                <th class="text-center" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
            EOD;
        echo $html;
        foreach ($rs as $row) {
            $html = <<<EOD
                        <tr id="{$row['plan_status_id']}">
                            <td>{$row['plan_status_id']}</td>
                            <td>{$row['plan_status_name']}</td>
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
