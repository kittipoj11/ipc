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
    table tr .tdPeriod {
      cursor: pointer;
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
    require_once  'class/inspection_class.php';
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';

    // $_SESSION['Request'] = $_REQUEST;
    $po_id = $_REQUEST['po_id'];
    // $period_id = $_REQUEST['period_id'];
    // $inspection_id = $_REQUEST['inspection_id'];

    $po = new Po;
    $rsPoMainByPoId = $po->getPoMainByPoId($po_id);

    $inspection = new Inspection;
    $rsInspectionPeriod =$inspection->getInspectionPeriodAllByPoId($po_id );

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
          <h6 class="m-1 fw-bold text-uppercase">Inspection(ตรวจรับงาน)</h6>
        </div>
        <!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <form name="myForm" id="myForm" action="" method="post">
            <div class="card">
              <div class="card-header">
                <h6 class="m-1 fw-bold"><?= $rsPoMainByPoId['po_number'] . " : " . $rsPoMainByPoId['supplier_id'] . " - " . $rsPoMainByPoId['supplier_name'] ?></h6>
              </div>

              <div class="card-body m-0 p-0">

                <input type="text" class="form-control d-none" name="po_id" id="po_id" value="<?= $rsPoMainByPoId['po_id'] ?>">

                <div class="row m-1">
                  <div class="col-4 input-group input-group-sm">
                    <label for="supplier_name" class="input-group-text">ผู้รับเหมา</label>
                    <input type="text" class="form-control" name="supplier_name" id="supplier_name" value="<?= $rsPoMainByPoId['supplier_name'] ?>" disabled>
                  </div>
                </div>

                <div class="row m-1">
                  <div class="col-6 input-group input-group-sm">
                    <label for="project_name" class="input-group-text">โครงการ</label>
                    <input type="text" class="form-control" name="project_name" id="project_name" disabled value="<?= $rsPoMainByPoId['project_name'] ?>">
                  </div>

                  <div class="col-6 input-group input-group-sm">
                    <label for="location_name" class="input-group-text">สถานที่</label>
                    <input type="text" class="form-control" name="location_name" id="location_name" disabled value="<?= $rsPoMainByPoId['location_name'] ?>">
                  </div>
                </div>

                <div class="row m-1">
                  <div class="col-6 input-group input-group-sm">
                    <label for="working_name_th" class="input-group-text">งาน</label>
                    <input type="text" class="form-control" name="working_name_th" id="working_name_th" disabled value="<?= $rsPoMainByPoId['working_name_th'] ?> (<?= $rsPoMainByPoId['working_name_en'] ?>)">
                  </div>

                </div>

                <div class="row m-1">
                  <div class="col-4">
                    <div class="row-1 input-group input-group-sm">
                      <label for="working_date_from" class="input-group-text">ระยะเวลาดำเนินการ</label>
                      <input type="date" class="form-control" name="working_date_from" id="working_date_from" disabled value="<?php echo isset($rsPoMainByPoId['working_date_from']) ? htmlspecialchars($rsPoMainByPoId['working_date_from']) : ''; ?>">

                    </div>
                  </div>
                  <div class="col-4">
                    <div class="row-1 input-group input-group-sm">
                      <label for="working_date_to" class="input-group-text "> ถึง </label>
                      <input type="date" class="form-control" name="working_date_to" id="working_date_to" disabled value="<?php echo isset($rsPoMainByPoId['working_date_to']) ? htmlspecialchars($rsPoMainByPoId['working_date_to']) : ''; ?>">
                    </div>
                  </div>

                  <div class="col-2 input-group input-group-sm">
                    <label for="working_day" class="input-group-text">รวม</label>
                    <input type="number" class="form-control" name="working_day" id="working_day" disabled value="<?php echo isset($rsPoMainByPoId['working_day']) ? htmlspecialchars($rsPoMainByPoId['working_day']) : ''; ?>">
                  </div>
                </div>

                <!-- <hr class="hr border border-dark"> -->

                <div class="row m-1">
                  <div class="col-4 input-group input-group-sm">
                    <label for="po_number" class="input-group-text">เลขที่ PO</label>
                    <input type="text" class="form-control" name="po_number" id="po_number" value=<?= $rsPoMainByPoId['po_number'] ?> readonly>
                  </div>

                  <div class="col-4 input-group input-group-sm">
                    <label for="contract_value" class="input-group-text">มูลค่างานตาม PO</label>
                    <input type="number" class="form-control" name="contract_value" id="contract_value" disabled value=<?= $rsPoMainByPoId['contract_value'] ?>>
                  </div>

                  <div class="col-2 input-group input-group-sm">
                    <?php
                    $display_include_vat = $rsPoMainByPoId['is_include_vat'] ? "(Including VAT 7% )" : "";
                    ?>
                    <label for="vat" class="input-group-text d-none">(Includeing VAT</label>
                    <input type="text" class="form-control border border-0" name="vat" id="vat" disabled value="<?= $display_include_vat ?>">
                  </div>
                </div>

                <!-- <hr class="hr border border-dark"> -->

                <div class="row m-1 mb-3 d-none">
                  <div class="col-4">
                    <div class="row-1 input-group input-group-sm">
                      <label for="workload_planned_percent" class="input-group-text ">ปริมาณที่ต้องแล้วเสร็จตามแผนงาน</label>
                      <input type="number" class="form-control " name="workload_planned_percent" id="workload_planned_percent" disabled value="<?php echo isset($rsPoMainByPoId['workload_planned_percent']) ? htmlspecialchars($rsPoMainByPoId['workload_planned_percent']) : ''; ?>">
                      <label for="workload_planned_percent" class="input-group-text ">%</label>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="row-1 input-group input-group-sm">
                      <label for="workload_actual_completed_percent" class="input-group-text ">ปริมาณที่แล้วเสร็จจริง</label>
                      <input type="number" class="form-control " name="workload_actual_completed_percent" id="workload_actual_completed_percent" value="<?php echo isset($rsPoMainByPoId['workload_actual_completed_percent']) ? htmlspecialchars($rsPoMainByPoId['workload_actual_completed_percent']) : ''; ?>">
                      <label for="workload_actual_completed_percent" class="input-group-text ">%</label>
                    </div>
                  </div>

                  <div class="col-4">
                    <div class="row-1 input-group input-group-sm">
                      <label for="workload_remaining_percent" class="input-group-text">ปริมาณงานคงเหลือ</label>
                      <input type="number" class="form-control" name="workload_remaining_percent" id="workload_remaining_percent" readonly value="<?php echo isset($rsPoMainByPoId['workload_remaining_percent']) ? htmlspecialchars($rsPoMainByPoId['workload_remaining_percent']) : ''; ?>">
                      <label for="workload_remaining_percent" class="input-group-text ">%</label>
                    </div>
                  </div>
                </div>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </form>
        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- /.content -->

      <section class="content-period d-nonex">
        <div class="container-fluid">
          <div class="card border border-1 border-dark m-1">
            <h6 class="m-1 fw-bold">รายการงวดงาน</h6>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <table class="table table-bordered justify-content-center text-center">
                <thead>
                  <tr>
                    <!-- <th class="text-center align-content-center p-1 d-none" rowspan="2" width="5%">po_id</th>
                    <th class="text-center align-content-center p-1 d-none" rowspan="2" width="5%">period_id</th>
                    <th class="text-center align-content-center p-1 d-none" rowspan="2" width="5%">inspection_id</th> -->
                    <th class="text-center align-content-center p-1" rowspan="2" width="5%">งวดงาน</th>
                    <th class="text-center p-1" colspan="3">ปริมาณงาน</th>
                    <th class="text-center p-1" colspan="3">ยอดเบิกเงินงวด</th>
                    <th class="text-center align-content-center p-1" rowspan="2">หมายเหตุ</th>
                  </tr>
                  <tr>
                    <th class="text-center p-1" width="10%">ตามแผนงาน(%)</th>
                    <th class="text-center p-1" width="10%">ที่แล้วเสร็จจริง(%)</th>
                    <th class="text-center p-1" width="10%">คงเหลือ(%)</th>
                    <th class="text-center p-1" width="10%">ยอดปัจจุบัน</th>
                    <th class="text-center p-1" width="10%">ยอดสะสมถึงปัจจุบัน</th>
                    <th class="text-center p-1" width="10%">ยอดคงเหลือ</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($rsInspectionPeriod as $row) { ?>
                    <tr data-po_id=<?= $row['po_id'] ?> data-period_id=<?= $row['period_id'] ?> data-inspection_id=<?= $row['inspection_id'] ?>>
                      <!-- <td class="tdPeriod text-right text-primary input-group-sm p-0 po_id d-none" data-id=<?= $row['po_id'] ?>><?= $row['po_id'] ?></td>
                      <td class="tdPeriod text-right text-primary input-group-sm p-0 period_id d-none" data-id=<?= $row['period_id'] ?>><?= $row['period_id'] ?></td>
                      <td class="tdPeriod text-right text-primary input-group-sm p-0 inspection_id d-none" data-id=<?= $row['inspection_id'] ?>><?= $row['inspection_id'] ?></td> -->

                      <td class="tdPeriod text-right text-primary py-0 px-1"><?= $row['period_number'] ?></td>
                      <td class="tdPeriod text-right text-primary py-0 px-1"><?= $row['workload_planned_percent'] ?></td>
                      <td class="tdPeriod text-right text-primary py-0 px-1"><?= $row['workload_actual_completed_percent'] ?></td>
                      <td class="tdPeriod text-right text-primary py-0 px-1"><?= $row['workload_remaining_percent'] ?></td>
                      <td class="tdPeriod text-right text-primary py-0 px-1"><?= $row['interim_payment'] ?></td>
                      <td class="tdPeriod text-right text-primary py-0 px-1"><?= $row['interim_payment_less_previous'] ?></td>
                      <td class="tdPeriod text-right text-primary py-0 px-1"><?= $row['interim_payment_remain'] ?></td>
                      <td class="tdPeriod text-left text-primary py-0 px-1"><?= $row['remark'] ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <div class="container-fluid d-flex justify-content-start">
          <!-- <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" value="บันทึก"> -->
          <!-- <button type="submit" name="btnSave" id="btnSave" class="btn btn-primary btn-sm m-1">บันทึก</button> -->
          <button type="button" name="btnCancel" id="btnCancel" class="btn btn-primary btn-sm m-1"> <i class="fi fi-rr-left"></i> </button>
        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Content End -->

    <?php include 'logout_modal.php'; ?>

    <?php include 'footer_bar.php'; ?></div>


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
    <script src="javascript/inspection_view.js"></script>