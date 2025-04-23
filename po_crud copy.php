<?php
@session_start();

require_once 'config.php';
require_once 'class/po_class.php';

$po = new Po();
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'select') {
    $rs = $po->getPoMainAll();
    createPOTable($rs);
}
function createPOTable($getRs) {
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



