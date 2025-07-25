<?php
@session_start();

require_once 'config.php';
require_once 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <?php include 'header_main.php'; ?>

  <!-- Bootstrap 5.3.3 add by Poj-->
  <link rel="stylesheet" href="plugins/bootstrap-5.3.3/dist/css/bootstrap.min.css">
  <!-- ใช้แสดง icon ปุ่ม Insert, Update, Delete และ icon เมนูต่างๆบน sidebar-->
  <link rel="stylesheet" href="plugins/fontawesome-free-6.5.1-web/css/all.min.css" type="text/css">
  <!-- Google Font: Source Sans Pro -->
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"> -->
  <!-- flaticon dist\bootstrap-icons-1.11.3\font\bootstrap-icons.min.css-->
  <!-- <link rel="stylesheet" href="plugins/dist/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css"> -->
  <!-- flaticon -->
  <link rel="stylesheet" href="plugins/uicons-regular-rounded/css/uicons-regular-rounded.css">
  <!-- DataTables -->
  <!-- <link rel="stylesheet" href="plugins/DataTables/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/DataTables/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/DataTables/datatables-buttons/css/buttons.bootstrap4.min.css"> -->
  <!-- Theme style -->
  <link rel="stylesheet" href="plugins/dist/css/adminlte.min.css">

  <style>
    :root {
      --my-gold-color: gold;
      --my-primary-color: var(--bs-primary);
      /* สีเทา */
      --my-greenyellow-color: greenyellow;
      /* สีเหลือง */
      --my-text-color: #333;
      /* สีดำ */
      --my-background-color: #f8f9fa;
      /* สีเทาอ่อน */
    }

    table tr th {
      cursor: default;
    }

    /* .dropdown-menu {
      width: fit-content;
    } */
    .dropdown-item:hover {
      /* background-color: var(--bs-primary); */
      background-color: var(--my-primary-color);
      color: white;
      /* เพิ่มสีข้อความให้เป็นสีขาวเพื่อให้ตัดกับพื้นหลัง */
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

  <!-- Page Wrapper -->
  <div class="wrapper">

    <?php include 'sidebar.php'; ?>
    <?php include 'navbar.php'; ?>

    <!-- Main Content Start -->
    <?php
    require_once  'class/connection_class.php';
    require_once  'class/inspection_class.php';
    require_once  'class/plan_status_class.php';

    // $_SESSION['Request'] = $_REQUEST;
    $connection = new Connection;
    $pdo = $connection->getDbConnection();

    $po_id = $_REQUEST['po_id'];
    $period_id = $_REQUEST['period_id'];
    $inspection_id = $_REQUEST['inspection_id'];

    $inspection = new Inspection($pdo);
    $rsInspection = $inspection->getPeriodByPeriodId($period_id);
    // $_SESSION['current_approval_level isset'] =  isset($rsInspection['period']['current_approval_level']);
    // $_SESSION['current_approval_level'] =  $rsInspection['period']['current_approval_level'];
    // $_SESSION['approval_date isset'] =  array_key_exists('approval_date', $rsInspection['periodApprovals']) ;
    $_SESSION['rsInspection'] =  $rsInspection;

    $plan_status = new Plan_status($pdo);
    $rsPlanStatus = $plan_status->getAll();

    // 
    // ถ้า approval_level ตรงกับ current_approval_level และ approver_id ตรงกับ user_id ที่ login
    // 
    $content_header = 'Inspection(ตรวจรับงาน)';
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-12  d-flex justify-content-between">
          <h6 class="m-1 fw-bold text-uppercase"><?= $content_header ?></h6>
        </div>
      </section>
      <!-- /.container-fluid content-header-->

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <form name="myForm" id="myForm" action="" method="post">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <div class="col d-flex align-items-center">
                      <input type="text" class="form-control d-none" name="po_id" id="po_id" value="<?= $rsInspection['period']['po_id'] ?>">
                      <input type="text" class="form-control d-none" name="period_id" id="period_id" value="<?= $rsInspection['period']['period_id'] ?>">
                      <input type="text" class="form-control d-none" name="inspection_id" id="inspection_id" value="<?= $rsInspection['period']['inspection_id'] ?>">

                      <!-- <h6 class="m-1 fw-bold">< ?= "[type " . gettype($rsInspection['periodApprovals']) . "]" ?></h6> -->
                      <h6 class="m-1 fw-bold"><?= $rsInspection['header']['po_number'] . " : " . $rsInspection['header']['supplier_id'] . " - " . $rsInspection['header']['supplier_name'] ?></h6>
                      <h6 class="m-1 fw-bold"><?= "[งวดงานที่ " . $rsInspection['period']['period_number'] . "]" ?></h6>
                      <button type="button" name="btnAttach" id="btnAttach" class="btn btn-primary btn-sm m-1">
                        <i class="fi fi-rr-clip"></i>
                      </button>
                    </div>

                    <!-- <div class="dropdown"> -->
                    <?php
                    if (array_key_exists('approval_date', $rsInspection['periodApprovals']) && is_null($rsInspection['periodApprovals']['approval_date'])
                      && isset($rsInspection['periodApprovals']['approver_id']) && $rsInspection['periodApprovals']['approver_id'] == $_SESSION['user_id']):
                    // if (is_null($rsInspection['periodApprovals']['approval_date'])
                    //   && isset($rsInspection['periodApprovals']['approver_id']) && $rsInspection['periodApprovals']['approver_id'] == $_SESSION['user_id']):
                    ?>
                      <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Action
                        </button>


                        <ul class="dropdown-menu py-0" id="action_type" data-current-approval-level="<?= $rsInspection['period']['current_approval_level'] ?>" 
                        data-max-approval-level="<?= $rsInspection['maxPeriodApproval']['max_approval_level'] ?>">
                          <!-- <li><a class="dropdown-item p-1" href="#">Confirm</a></li> -->
                          <?php
                          if (isset($rsInspection['period']['current_approval_level']) && $rsInspection['period']['current_approval_level'] == 1):
                          ?>
                            <li><button class="dropdown-item approval_next" id="document_submit">Submit</a>
                            </li>
                          <?php
                          elseif (isset($rsInspection['period']['current_approval_level']) && $rsInspection['period']['current_approval_level'] == 2):
                          ?>
                            <li><button class="dropdown-item approval_next" id="document_approve">Approve</a>
                            </li>
                            <li><button class="dropdown-item approval_reject" id="document_reject">Reject</button>
                            </li>
                          <?php endif; ?>

                        </ul>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="card-body m-0 p-0">

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="supplier_name" class="input-group-text">ผู้รับเหมา</label>
                        <input type="text" class="form-control" name="supplier_name" id="supplier_name" value="<?= $rsInspection['header']['supplier_name'] ?>" readonly>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-6 input-group input-group-sm">
                        <label for="project_name" class="input-group-text">โครงการ</label>
                        <input type="text" class="form-control" name="project_name" id="project_name" readonly value="<?= $rsInspection['header']['project_name'] ?>">
                      </div>

                      <div class="col-6 input-group input-group-sm">
                        <label for="location_name" class="input-group-text">สถานที่</label>
                        <input type="text" class="form-control" name="location_name" id="location_name" readonly value="<?= $rsInspection['header']['location_name'] ?>">
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-6 input-group input-group-sm">
                        <label for="working_name_th" class="input-group-text">งาน</label>
                        <input type="text" class="form-control" name="working_name_th" id="working_name_th" readonly value="<?= $rsInspection['header']['working_name_th'] ?> (<?= $rsInspection['header']['working_name_en'] ?>)">
                      </div>

                    </div>

                    <div class="row m-1">
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_from" class="input-group-text">ระยะเวลาดำเนินการ</label>
                          <input type="date" class="form-control" name="working_date_from" id="working_date_from" readonly value="<?php echo isset($rsInspection['header']['working_date_from']) ? htmlspecialchars($rsInspection['header']['working_date_from']) : ''; ?>">

                        </div>
                      </div>
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_to" class="input-group-text "> ถึง </label>
                          <input type="date" class="form-control" name="working_date_to" id="working_date_to" readonly value="<?php echo isset($rsInspection['header']['working_date_to']) ? htmlspecialchars($rsInspection['header']['working_date_to']) : ''; ?>">
                        </div>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <label for="working_day" class="input-group-text">รวม</label>
                        <input type="number" class="form-control" name="working_day" id="working_day" readonly value="<?php echo isset($rsInspection['header']['working_day']) ? htmlspecialchars($rsInspection['header']['working_day']) : ''; ?>">
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="po_number" class="input-group-text">เลขที่ PO</label>
                        <input type="text" class="form-control" name="po_number" id="po_number" value=<?= $rsInspection['header']['po_number'] ?> readonly>
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="contract_value" class="input-group-text">มูลค่างานตาม PO</label>
                        <input type="number" class="form-control" name="contract_value" id="contract_value" readonly value=<?= $rsInspection['header']['contract_value'] ?>>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <?php
                        $display_include_vat = $rsInspection['header']['is_include_vat'] ? "(Including VAT 7% )" : "";
                        ?>
                        <label for="vat" class="input-group-text d-none">Includeing VAT</label>
                        <input type="text" class="form-control border border-0" name="vat" id="vat" readonly value="<?= $display_include_vat ?>">
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1">
                      <div class="col-3 border-end border-dark-subtle m-0 p-0">
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="period_number" class="input-group-text">เบิกงวดงานที่ </label>
                            <input type="text" class="form-control" name="period_number" id="period_number" readonly value="<?= $rsInspection['period']['period_number'] ?>">
                          </div>
                        </div>
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <div class="form-check">
                              <?php
                              $checked_attr = $rsInspection['header']['is_deposit'] ? "checked" : "";
                              ?>
                              <input class="form-check-input" type="checkbox" name="is_deposit" readonly <?= $checked_attr ?>>
                            </div>
                            <label class="form-check-label" for="deposit_percent">มีเงินมัดจำ </label>
                            <input type="number" class="form-control" name="deposit_percent" id="deposit_percent" readonly value=<?= $rsInspection['header']['deposit_percent'] ?>>%
                          </div>
                        </div>
                      </div>

                      <div class="col-9">
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment" class="col-3 input-group-text">ยอดเบิกเงินงวดปัจจุบัน</label>
                            <input type="number" step="1.00" class="col-3 form-control" name="interim_payment" id="interim_payment" value="<?= $rsInspection['period']['interim_payment'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="number" step="0.01" class="col-2 form-control" name="interim_payment_percent" id="interim_payment_percent" readonly value=<?= $rsInspection['period']['interim_payment_percent'] ?>>
                            <label class="input-group-text">%</label>
                          </div>
                        </div>

                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment_less_previous" class="col-3 input-group-text">ยอดเบิกเงินงวดสะสมไม่รวมปัจจุบัน</label>
                            <input type="number" step="1" class="col-3 form-control" name="interim_payment_less_previous" id="interim_payment_less_previous" readonly value="<?= $rsInspection['period']['previous_interim_payment_accumulated'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="number" step="0.01" class="col-2 form-control" name="interim_payment_less_previous_percent" id="interim_payment_less_previous_percent" readonly value="<?= $rsInspection['period']['interim_payment_less_previous_percent'] ?>">
                            <label class="input-group-text">%</label>
                          </div>
                        </div>

                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment_accumulated" class="col-3 input-group-text">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</label>
                            <input type="number" step="1" class="col-3 form-control" name="interim_payment_accumulated" id="interim_payment_accumulated" readonly value="<?= $rsInspection['period']['interim_payment_accumulated'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="number" step="0.01" class="col-2 form-control" name="interim_payment_accumulated_percent" id="interim_payment_accumulated_percent" readonly value="<?= $rsInspection['period']['interim_payment_accumulated_percent'] ?>">
                            <label class="input-group-text">%</label>
                          </div>
                        </div>

                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment_remain" class="col-3 input-group-text">ยอดเงินงวดคงเหลือ</label>
                            <input type="number" step="1" class="col-3 form-control" name="interim_payment_remain" id="interim_payment_remain" readonly value="<?= $rsInspection['period']['interim_payment_remain'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="number" step="0.01" class="col-2 form-control" name="interim_payment_remain_percent" id="interim_payment_remain_percent" readonly value="<?= $rsInspection['period']['interim_payment_remain_percent'] ?>">
                            <label class="input-group-text">%</label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1 mb-3">
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="workload_planned_percent" class="input-group-text ">ปริมาณที่ต้องแล้วเสร็จตามแผนงาน</label>
                          <input type="number" step="0.01" class="form-control " name="workload_planned_percent" id="workload_planned_percent" readonly value="<?php echo isset($rsInspection['period']['workload_planned_percent']) ? htmlspecialchars($rsInspection['period']['workload_planned_percent']) : ''; ?>">
                          <label for="workload_planned_percent" class="input-group-text ">%</label>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="workload_actual_completed_percent" class="input-group-text ">ปริมาณที่แล้วเสร็จจริง</label>
                          <input type="number" step="0.01" class="form-control " name="workload_actual_completed_percent" id="workload_actual_completed_percent" value="<?php echo isset($rsInspection['period']['workload_actual_completed_percent']) ? htmlspecialchars($rsInspection['period']['workload_actual_completed_percent']) : ''; ?>">
                          <label for="workload_actual_completed_percent" class="input-group-text ">%</label>
                        </div>
                      </div>

                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="workload_remaining_percent" class="input-group-text">ปริมาณงานคงเหลือ</label>
                          <input type="number" step="0.01" class="form-control" name="workload_remaining_percent" id="workload_remaining_percent" readonly value="<?php echo isset($rsInspection['period']['workload_remaining_percent']) ? htmlspecialchars($rsInspection['period']['workload_remaining_percent']) : ''; ?>">
                          <label for="workload_remaining_percent" class="input-group-text ">%</label>
                        </div>
                      </div>
                    </div>

                    <div class="card border border-1 border-dark m-1">
                      <h6 class="m-1 fw-bold">รายการรายละเอียดการตรวจสอบ</h6>
                      <!-- <div class="card-header" style="display: flex;"> -->
                      <div class="m-1">
                        <a id="btnAdd" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="right" title="เพิ่มงวดงาน">
                          Add Order
                        </a>

                        <a id="btnDeleteLast" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="ลบงวดงานล่าสุด">
                          Delete last order
                        </a>
                        <a id="btnClear" class="btn btn-danger btn-sm d-none" data-toggle="tooltip" data-placement="right" title="ลบงวดงานทั้งหมด">
                          Clear all order
                        </a>
                      </div>

                      <div class="card-body p-0">
                        <!-- สร้าง Table ตามปกติ -->
                        <table class="table table-bordered justify-content-center text-center" id="tableOrder">
                          <thead>
                            <tr>
                              <th class="p-1" width="5%">ลำดับที่</th>
                              <th class="p-1" width="20%">รายละเอียดการตรวจสอบ</th>
                              <th class="p-1">หมายเหตุ</th>
                              <th class="p-1" width="5%">Crud</th>
                              <th class="p-1 d-nonex" width="5%">rec_id</th>
                            </tr>
                          </thead>

                          <tbody id="tbody-order">
                            <?php foreach ($rsInspection['periodDetails'] as $row) { ?>
                              <tr data-crud='select' data-rec-id=<?php echo isset($row['rec_id']) ? htmlspecialchars($row['rec_id']) : ''; ?>>
                                <!-- กำหนดลำดับ Auto 1, 2, 3, ... -->
                                <td class="input-group-sm p-0"><input type="number" name="order_no" class="form-control" value="<?php echo isset($row['order_no']) ? htmlspecialchars($row['order_no']) : ''; ?>" readonly>
                                </td>
                                <td class="input-group-sm p-0"><input type="text" name="detail" class="form-control" value="<?php echo isset($row['details']) ? htmlspecialchars($row['details']) : ''; ?>">
                                </td>
                                <td class="input-group-sm p-0"><input type="text" name="remark" class="form-control" value="<?php echo isset($row['remark']) ? htmlspecialchars($row['remark']) : ''; ?>">
                                </td>
                                <td class="input-group-sm p-0">
                                  <input type="text" name="crud" class="form-control" value="select">
                                </td>
                                <td class="input-group-sm p-0 d-nonex"><input type="text" name="rec_id" class="form-control" value="<?php echo isset($row['rec_id']) ? htmlspecialchars($row['rec_id']) : ''; ?>" readonly></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <div class="card">
                      <div class="card-header d-none">
                      </div>

                      <div class="card-body">
                        <div class="row m-1 mb-3">
                          <div class="col-8 input-group input-group-sm">
                            <label for="plan_status_id" class="input-group-text">ปริมาณที่ต้องแล้วเสร็จเมื่อเปรียบเทียบกับแผนงาน</label>
                            <select class="form-select form-control" name="plan_status_id" id="plan_status_id">
                              <option value="-1" selected>...</option>
                              <?php
                              foreach ($rsPlanStatus as $row) :
                                $selected_attr = ($rsInspection['period']['plan_status_id'] == $row['plan_status_id']) ? " selected" : "";
                                echo "<option value='{$row['plan_status_id']}' {$selected_attr}>{$row['plan_status_name']}</option>";
                              endforeach ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-floating">
                          <textarea name="remark" class="form-control" id="remark" placeholder="Leave a comment here" rows="4" style="min-height: 4em;height: auto;"><?php echo isset($rsInspection['period']['remark']) ? trim(htmlspecialchars($rsInspection['period']['remark'])) : ''; ?></textarea>
                          <label for="remark">หมายเหตุ:</label>
                        </div>

                        <?php
                        // savedPaymentMethod = data.payment_method || 'Cash'; // กำหนดค่าดีฟอลต์เป็น Cash
                        // $disbursement = $rsInspection['period']['disbursement'] ?? 0; // ถ้าเป็น null กำหนดค่าดีฟอลต์เป็น 0
                        $disbursement = $rsInspection['period']['disbursement']; // ถ้าเป็น null ไม่ต้องกำหนดค่าดีฟอลต์

                        ?>

                        <div class="row m-1 mb-3">
                          <div class="col-2 input-group input-group-sm">
                            <label for="disbursement" class="input-group-text">การเบิกจ่าย</label>
                          </div>
                          <div class="col-2 form-check form-check-inline">
                            <input class=" form-check-input" type="radio" name="disbursement" id="disbursement1" value="1" <?php echo ($disbursement == 1 ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="disbursement1">
                              อนุมัติ
                            </label>
                          </div>
                          <div class="col-2 form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="disbursement" id="disbursement2" value="0" <?php echo ($disbursement == 0 ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="disbursement2">
                              ไม่อนุมัติ
                            </label>
                          </div>
                        </div>

                        <!-- ผู้รับเหมาได้ดำเนินการตามรายละเอียดดังกล่าวข้างต้น จึงเห็นสมควร -->
                      </div>
                      <!-- /.card-body -->

                    </div>

                  </div>
                  <!-- /.card-body -->

                  <div class="container-fluid  p-0 d-flex justify-content-between">
                    <button type="button" name="btnCancel" class="btn btn-primary btn-sm m-1 btnCancel"> <i class="fi fi-rr-left"></i> </button>
                    <div>
                      <?php
                      if (
                        isset($rsInspection['period']['current_approval_level']) && $rsInspection['period']['current_approval_level'] == 1
                        && array_key_exists('approval_date', $rsInspection['periodApprovals']) && is_null($rsInspection['periodApprovals']['approval_date'])
                        && isset($rsInspection['periodApprovals']['approver_id']) && $rsInspection['periodApprovals']['approver_id'] == $_SESSION['user_id']
                      ):
                      ?>
                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" value="บันทึก" data-current_approval_level="<?= $rsInspection['period']['current_approval_level'] ?>">
                      <?php
                      endif;
                      ?>
                      <button type="button" name="btnCancel" class="btn btn-warning btn-sm m-1 btnCancel">ยกเลิก</button>
                    </div>
                  </div>

                </div>
                <!-- /.card -->

              </form>
              <!-- /.myForm -->

            </div>
          </div>
        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- Main Content -->
    </div>
    <!-- /.Content Wrapper -->


    <?php include 'logout_modal.php'; ?>

    <?php include 'footer_bar.php'; ?>


    <!-- ./wrapper -->

    <!-- Scroll to Top Button-->
    <!-- <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a> -->

    <!-- REQUIRED SCRIPTS -->
    <!-- Bootstrap 5.3.3 -->
    <script src="plugins/bootstrap-5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- jQuery -->
    <script src="plugins/jQuery-3.7.1/jquery-3.7.1.min.js"></script>
    <!-- AdminLTE App -->
    <script src="plugins/dist/js/adminlte.js"></script>
    <!-- Sweet Alert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- My JavaScript  -->
    <script src="javascript/inspection_period_form.js"></script>