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
    table tr th {
      cursor: default;
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
    require_once  'class/po_class.php';
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';

    // $_SESSION['Request'] = $_REQUEST;
    $po_id = $_REQUEST['po_id'];
    $period_id = $_REQUEST['period_id'];
    $inspection_id = $_REQUEST['inspection_id'];

    $po = new Po;
    $rsInspectionPeriod = $po->getInspectionPeriodByPeriodId($po_id, $period_id);
    $rsInspectionPeriodDetail = $po->getInspectionPeriodDetailByPeriodId($po_id, $period_id);

    $supplier = new Supplier;
    $supplier_rs = $supplier->getAllRecords();

    $location = new Location;
    $location_rs = $location->getAllRecords();

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-6 d-flex">
          <h6 class="m-1 fw-bold text-uppercase">IPC</h6>
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
                  <div class="card-header d-flex align-items-center">
                    <input type="text" class="form-control d-none" name="inspection_id" id="inspection_id" value="<?= $rsInspectionPeriod['inspection_id'] ?>">
                    <input type="text" class="form-control d-none" name="period_id" id="period_id" value="<?= $rsInspectionPeriod['period_id'] ?>">
                    <input type="text" class="form-control d-none" name="po_id" id="po_id" value="<?= $rsInspectionPeriod['po_id'] ?>">

                    <h6 class="m-1 fw-bold"><?= $rsInspectionPeriod['po_number'] . " : " . $rsInspectionPeriod['supplier_id'] . " - " . $rsInspectionPeriod['supplier_name'] ?></h6>
                    <h6 class="m-1 fw-bold"><?= "[งวดงานที่ " . $rsInspectionPeriod['period_number'] . "]" ?></h6>
                    <button type="button" name="btnAttach" id="btnAttach" class="btn btn-primary btn-sm m-1"> <i class="fi fi-rr-clip"></i> </button>
                  </div>

                  <div class="card-body m-0 p-0">

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="supplier_name" class="input-group-text">ผู้รับเหมา</label>
                        <input type="text" class="form-control" name="supplier_name" id="supplier_name" value="<?= $rsInspectionPeriod['supplier_name'] ?>" disabled>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-6 input-group input-group-sm">
                        <label for="project_name" class="input-group-text">โครงการ</label>
                        <input type="text" class="form-control" name="project_name" id="project_name" disabled value="<?= $rsInspectionPeriod['project_name'] ?>">
                      </div>

                      <div class="col-6 input-group input-group-sm">
                        <label for="location_name" class="input-group-text">สถานที่</label>
                        <input type="text" class="form-control" name="location_name" id="location_name" disabled value="<?= $rsInspectionPeriod['location_name'] ?>">
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-6 input-group input-group-sm">
                        <label for="working_name_th" class="input-group-text">งาน</label>
                        <input type="text" class="form-control" name="working_name_th" id="working_name_th" disabled value="<?= $rsInspectionPeriod['working_name_th'] ?> (<?= $rsInspectionPeriod['working_name_en'] ?>)">
                      </div>

                    </div>

                    <div class="row m-1">
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_from" class="input-group-text">ระยะเวลาดำเนินการ</label>
                          <input type="date" class="form-control" name="working_date_from" id="working_date_from" disabled value="<?php echo isset($rsInspectionPeriod['working_date_from']) ? htmlspecialchars($rsInspectionPeriod['working_date_from']) : ''; ?>">

                        </div>
                      </div>
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_to" class="input-group-text "> ถึง </label>
                          <input type="date" class="form-control" name="working_date_to" id="working_date_to" disabled value="<?php echo isset($rsInspectionPeriod['working_date_to']) ? htmlspecialchars($rsInspectionPeriod['working_date_to']) : ''; ?>">
                        </div>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <label for="working_day" class="input-group-text">รวม</label>
                        <input type="number" class="form-control" name="working_day" id="working_day" disabled value="<?php echo isset($rsInspectionPeriod['working_day']) ? htmlspecialchars($rsInspectionPeriod['working_day']) : ''; ?>">
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="po_number" class="input-group-text">เลขที่ PO</label>
                        <input type="text" class="form-control" name="po_number" id="po_number" value=<?= $rsInspectionPeriod['po_number'] ?> readonly>
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="contract_value" class="input-group-text">มูลค่างานตาม PO</label>
                        <input type="number" class="form-control" name="contract_value" id="contract_value" disabled value=<?= $rsInspectionPeriod['contract_value'] ?>>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <?php
                        $display_include_vat = $rsInspectionPeriod['is_include_vat'] ? "(Including VAT 7% )" : "";
                        ?>
                        <label for="vat" class="input-group-text d-none">(Includeing VAT</label>
                        <input type="text" class="form-control border border-0" name="vat" id="vat" disabled value="<?= $display_include_vat ?>">
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1">
                      <div class="col-3 border-end border-dark-subtle m-0 p-0">
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="period_number" class="input-group-text">เบิกงวดงานที่ </label>
                            <input type="text" class="form-control" name="period_number" disabled value="<?= $rsInspectionPeriod['period_number'] ?>">
                          </div>
                        </div>
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <div class="form-check">
                              <?php
                              $checked_attr = $rsInspectionPeriod['is_deposit'] ? "checked" : "";
                              ?>
                              <input class="form-check-input" type="checkbox" name="is_deposit" disabled <?= $checked_attr ?>>
                            </div>
                            <label class="form-check-label" for="deposit_percent">มีเงินมัดจำ </label>
                            <input type="number" class="form-control" name="deposit_percent" id="deposit_percent" disabled value=<?= $rsInspectionPeriod['deposit_percent'] ?>>%
                          </div>
                        </div>
                      </div>

                      <div class="col-9">
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment" class="col-3 input-group-text">ยอดเบิกเงินงวดปัจจุบัน</label>
                            <input type="text" class="col-3 form-control" name="interim_payment" value="<?= $rsInspectionPeriod['interim_payment'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="text" class="col-2 form-control" name="interim_payment_percent" disabled value=<?= $rsInspectionPeriod['interim_payment_percent'] ?>>
                            <label class="input-group-text">%</label>
                          </div>
                        </div>

                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment_less_previous" class="col-3 input-group-text">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</label>
                            <input type="text" class="col-3 form-control" name="interim_payment_less_previous" disabled value="<?= $rsInspectionPeriod['interim_payment_less_previous'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="text" class="col-2 form-control" name="interim_payment_less_previous_percent" disabled value="<?= $rsInspectionPeriod['interim_payment_less_previous_percent'] ?>">
                            <label class="input-group-text">%</label>
                          </div>
                        </div>

                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment_remain" class="col-3 input-group-text">ยอดเงินงวดคงเหลือ</label>
                            <input type="text" class="col-3 form-control" name="interim_payment_remain" disabled value="<?= $rsInspectionPeriod['interim_payment_remain'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="text" class="col-2 form-control" name="interim_payment_remain_percent" disabled value="<?= $rsInspectionPeriod['interim_payment_remain_percent'] ?>">
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
                          <input type="number" class="form-control " name="workload_planned_percent" id="workload_planned_percent" disabled value="<?php echo isset($rsInspectionPeriod['workload_planned_percent']) ? htmlspecialchars($rsInspectionPeriod['workload_planned_percent']) : ''; ?>">
                          <label for="workload_planned_percent" class="input-group-text ">%</label>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="workload_actual_completed_percent" class="input-group-text ">ปริมาณที่แล้วเสร็จจริง</label>
                          <input type="number" class="form-control " name="workload_actual_completed_percent" id="workload_actual_completed_percent" value="<?php echo isset($rsInspectionPeriod['workload_actual_completed_percent']) ? htmlspecialchars($rsInspectionPeriod['workload_actual_completed_percent']) : ''; ?>">
                          <label for="workload_actual_completed_percent" class="input-group-text ">%</label>
                        </div>
                      </div>

                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="workload_remaining_percent" class="input-group-text">ปริมาณงานคงเหลือ</label>
                          <input type="number" class="form-control" name="workload_remaining_percent" id="workload_remaining_percent" readonly value="<?php echo isset($rsInspectionPeriod['workload_remaining_percent']) ? htmlspecialchars($rsInspectionPeriod['workload_remaining_percent']) : ''; ?>">
                          <label for="workload_remaining_percent" class="input-group-text ">%</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-body -->

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
                          <?php foreach ($rsInspectionPeriodDetail as $row) { ?>
                            <tr class="firstTr">
                              <!-- กำหนดลำดับ Auto 1, 2, 3, ... -->
                              <td class="input-group-sm p-0"><input type="number" name="order_nos[]" class="form-control order_no" value="<?php echo isset($row['order_no']) ? htmlspecialchars($row['order_no']) : ''; ?>" readonly>
                              </td>
                              <td class="input-group-sm p-0"><input type="text" name="details[]" class="form-control detail" value="<?php echo isset($row['details']) ? htmlspecialchars($row['details']) : ''; ?>">
                              </td>
                              <td class="input-group-sm p-0"><input type="text" name="remarks[]" class="form-control remark" value="<?php echo isset($row['remark']) ? htmlspecialchars($row['remark']) : ''; ?>">
                              </td>
                              <td class="input-group-sm p-0">
                                <input type="text" name="cruds[]" class="form-control crud" value="s">
                              </td>
                              <td class="input-group-sm p-0 d-nonex"><input type="text" name="rec_ids[]" class="form-control rec_id" value="<?php echo isset($row['rec_id']) ? htmlspecialchars($row['rec_id']) : ''; ?>" readonly></td>
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
                      <div class="row">
                        <div class="col-4">
                          <div class="row-1 input-group input-group-sm">
                            <label for="workload_planned_percent" class="input-group-text ">ปริมาณที่ต้องแล้วเสร็จตามแผนงาน</label>
                            <input type="number" class="form-control " name="workload_planned_percent" id="workload_planned_percent" disabled value="<?php echo isset($rsInspectionPeriod['workload_planned_percent']) ? htmlspecialchars($rsInspectionPeriod['workload_planned_percent']) : ''; ?>">
                            <label for="workload_planned_percent" class="input-group-text ">%</label>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="row-1 input-group input-group-sm">
                            <label for="workload_actual_completed_percent" class="input-group-text ">ปริมาณที่แล้วเสร็จจริง</label>
                            <input type="number" class="form-control " name="workload_actual_completed_percent" id="workload_actual_completed_percent" value="<?php echo isset($rsInspectionPeriod['workload_actual_completed_percent']) ? htmlspecialchars($rsInspectionPeriod['workload_actual_completed_percent']) : ''; ?>">
                            <label for="workload_actual_completed_percent" class="input-group-text ">%</label>
                          </div>
                        </div>

                        <div class="col-4">
                          <div class="row-1 input-group input-group-sm">
                            <label for="workload_remaining_percent" class="input-group-text">ปริมาณงานคงเหลือ</label>
                            <input type="number" class="form-control" name="workload_remaining_percent" id="workload_remaining_percent" readonly value="<?php echo isset($rsInspectionPeriod['workload_remaining_percent']) ? htmlspecialchars($rsInspectionPeriod['workload_remaining_percent']) : ''; ?>">
                            <label for="workload_remaining_percent" class="input-group-text ">%</label>
                          </div>
                        </div>
                      </div>

                      <div class="form-floating">
                        <textarea class="form-control" placeholder="Leave a comment here"></textarea>
                        <label for="floatingTextarea">หมายเหตุ:</label>
                      </div>

                      ผู้รับเหมาได้ดำเนินการตามรายละเอียดดังกล่าวข้างต้น จึงเห็นสมควร
                    </div>
                    <!-- /.card-body -->

                  </div>


                  <div class="card-footer p-0 d-flex justify-content-end">
                    <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" value="บันทึก">
                    <!-- <button type="submit" name="btnSave" id="btnSave" class="btn btn-primary btn-sm m-1">บันทึก</button> -->
                    <button type="button" name="btnCancel" id="btnCancel" class="btn btn-secondary btn-sm m-1">ยกเลิก</button>
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
    <script src="javascript/ipc_edit.js"></script>