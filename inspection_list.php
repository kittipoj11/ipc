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
                  <h6 class="m-1 fw-bold">All Inspection</h6>
                </div>

                <div class="card-body p-0" id="card-body">
                  <table id="tableMain" class="table table-bordered table-striped table-sm">
                    <thead>
                      <tr>
                        <th class="text-center p-1" style="width: 150px;">เลขที่ PO</th>
                        <th class="text-center p-1">โครงการ</th>
                        <th class="text-center p-1">ผู้รับเหมา</th>
                        <th class="text-center p-1">สถานที่</th>
                        <th class="text-center p-1">งาน</th>
                        <th class="text-center p-1">มูลค่า PO</th>
                        <th class="text-center p-1">จำนวนงวดงาน</th>
                      </tr>
                    </thead>
                    <tbody id="tbody">
                      
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
    <script src="javascript/inspection_list.js"></script>