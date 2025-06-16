<?php
// @session_start();

require_once 'config.php';
require_once 'class/department_class.php';

$department = new Department();
// print_r($_REQUEST);
// exit;
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'insertdata') {
    $department->insertData($_REQUEST);
    // fetchAll($department);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'updatedata') {
    $department->updateData($_REQUEST);
    // fetchAll($department);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deletedata') {
    $department->deleteData($_REQUEST);
    // fetchAll($department);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $department->fetchAll();
    createTable($rs);
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'selectdata') {
    $rs = $department->fetchById($_REQUEST['department_id']);
    echo json_encode($rs);
} else {
    fetchAll($department);
}

//หลังทำการ Insert, Update หรือ Delete แล้วทำการ fetch ข้อมูลมาแสดงใหม่
function createTable($getRs) {
    try {
        $html = '';
        foreach ($getRs as $row) {
            $html .= <<<EOD
                            <tr id="{$row['department_id']}">
                                <td>{$row['department_id']}</td>
                                <td>{$row['department_name']}</td>
                                <td align='center'>
                                    <div class='btn-group-sm'>
                                        <a class='btn btn-warning btn-sm btnEdit' data-bs-toggle='modal'  data-bs-placement='right' title='Edit' data-bs-target='#openModal' data-id='{$row['department_id']}' style='margin: 0px 5px 5px 5px'>
                                            <i class='fa-regular fa-pen-to-square'></i>
                                        </a>
                                        <a class='btn btn-danger btn-sm btnDelete' data-bs-toggle='modal'  data-bs-placement='right' title='Delete' data-bs-target='#deleteModal' data-id='{$row['department_id']}' style='margin: 0px 5px 5px 5px'>
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
                                <th class="text-center">Department name</th>
                                <th class="text-center" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
            EOD;
        echo $html;
        foreach ($rs as $row) {
            $html = <<<EOD
                        <tr id="{$row['department_id']}">
                            <td>{$row['department_id']}</td>
                            <td>{$row['department_name']}</td>
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
