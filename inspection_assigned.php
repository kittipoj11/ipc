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
  <!-- <link rel="stylesheet" href="plugins/uicons-regular-rounded/css/uicons-regular-rounded.css"> -->
  <!-- DataTables -->
  <!-- <link rel="stylesheet" href="plugins/DataTables/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/DataTables/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/DataTables/datatables-buttons/css/buttons.bootstrap4.min.css"> -->
  <!-- Theme style -->
  <link rel="stylesheet" href="plugins/dist/css/adminlte.min.css">

  <style>
    #tbody,
    #tbody-period tr td a {
      cursor: pointer;
    }

    #tableMain thead,
    #tablePeriod thead,
    #tablePeriod tr {
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
    require_once  'class/inspection_class.php';
    require_once  'class/po_class.php';

    $username = $_SESSION['username'];
    $inspection = new Inspection;
    $rsInspection = $inspection->getInspectionPeriodAssignToMe($username);
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
      <!-- <div class="result"></div> -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

              <div class="card">
                <div class="card-header d-flex">
                  <h6 class="m-1 fw-bold">All Assign to me</h6>
                </div>

                <div class="card-body p-0" id="card-body">
                  <table id="tableMain" class="table table-bordered table-striped table-sm">
                    <thead>
                      <tr>
                        <th class="text-center align-content-center p-2 d-none">po_id</th>
                        <th class="text-center align-content-center p-2 d-none">period_id</th>
                        <th class="text-center align-content-center p-2 d-none">inspection_id</th>
                        <th class="text-center p-2" style="width: 100px;">เลขที่ PO</th>
                        <th class="text-center align-content-center p-2">งวดงาน</th>
                        <th class="text-center p-2">โครงการ</th>
                        <th class="text-center p-2">ผู้รับเหมา</th>
                        <th class="text-center p-2">สถานที่</th>
                        <th class="text-center p-2">งาน</th>
                        <th class="text-center p-2">มูลค่า PO</th>
                      </tr>
                    </thead>
                    <!-- <td class="tdMain p-0"><a class='link-opacity-100 pe-auto po_number' title='Edit' style='margin: 0px 5px 5px 5px' data-po_number='{$row['po_number']}'>{$row['po_number']}</a></td> -->
                    <tbody id="tbody">
                      <?php foreach ($rsInspection as $row) {
                        $html = <<<EOD
                                        <tr data-po_id='{$row['po_id']}' data-period_id='{$row['period_id']}' data-inspection_id='{$row['inspection_id']}'>
                                            <td class="tdMain p-1 d-none">{$row['po_id']}</td>
                                            <td class="tdMain p-1 d-none">{$row['period_id']}</td>
                                            <td class="tdMain p-1 d-none">{$row['inspection_id']}</td>
                                            <td class="tdMain p-1 ">{$row['po_number']}</td>
                                            <td class="tdMain p-1 ">{$row['period_number']}</td>
                                            <td class="tdMain p-1">{$row['project_name']}</td>
                                            <td class="tdMain p-1">{$row['supplier_name']}</td>
                                            <td class="tdMain p-1">{$row['location_name']}</td>
                                            <td class="tdMain p-1">{$row['working_name_th']}</td>
                                            <td class="tdMain p-1 text-right">{$row['contract_value']}</td>
                                        </tr>
                                        EOD;
                        echo $html;
                      } ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- /.content -->

      <section class="content-period d-none">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title"></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0" id="card-body">
                  <table class="table table-bordered justify-content-center text-center" id="tablePeriod">
                    <thead>
                      <tr>
                        <th class="text-center align-content-center p-1 d-none" rowspan="2" width="5%">po_id</th>
                        <th class="text-center align-content-center p-1 d-none" rowspan="2" width="5%">period_id</th>
                        <th class="text-center align-content-center p-1 d-none" rowspan="2" width="5%">inspection_id</th>
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
                    <tbody id="tbody-period">
                      <!-- <tr>
                        <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right po_id" value="{$row['po_id']}" readonly></td>
                        <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right po_periods_id" value="{$row['po_periods_id']}" readonly></td>
                        <td class="tdPeriod text-right py-0 px-1"><a class='link-opacity-100 pe-auto' style='margin: 0px 5px 5px 5px'>{$row['period']}</a></td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['workload_planned_percent']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['workload_actual_completed_percent']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['workload_remaining_percent']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment_less_previous']}</td>
                        <td class="tdPeriod text-right py-0 px-1">{$row['interim_payment_remain']}</td>
                        <td class="tdPeriod text-left py-0 px-1">{$row['remark']}</td>
                      </tr> -->
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->

    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< ส่วน Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->

    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Logout Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <!-- logout.php -->

    <!-- Main Content End -->

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
    <script src="javascript/inspection_assigned.js"></script>