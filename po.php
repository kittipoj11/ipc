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
    #tbody tr {
      cursor: pointer;
    }

    #tableMain thead,
    #tablePeriod {
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
    require_once  'class/connection_class.php';
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';

    $connection=new Connection;
    $pdo=$connection->getDbConnection();
    $po = new Po($pdo);
    $rsPoMainAll = $po->fetchAll();
    // $rsPoPeriod = $po->fetchAllPeriodByPoId(0);
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-6 d-flex">
          <h6 class="m-1 fw-bold text-uppercase">Purchase Order</h6>
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
                  <h6 class="m-1 fw-bold">All Purchase Order</h6>
                  <a href="po_dml.php?action=create" class="btn btn-success btn-sm btnNew" title="New" style="margin: 0px 5px 5px 5px;">
                    <i class="fa-solid fa-plus"></i>
                  </a>
                </div>

                <div class="card-body p-0" id="card-body">
                  <table id="tableMain" class="table table-bordered table-striped table-sm">
                    <thead>
                      <tr>
                        <th class="text-center p-1 d-none">#</th>
                        <th class="text-center p-1" style="width: 150px;">เลขที่ PO</th>
                        <th class="text-center p-1">โครงการ</th>
                        <th class="text-center p-1">ผู้รับเหมา</th>
                        <th class="text-center p-1">สถานที่</th>
                        <th class="text-center p-1">งาน</th>
                        <th class="text-center p-1">มูลค่า PO ไม่รวม VAT</th>
                        <th class="text-center p-1">มูลค่า PO</th>
                        <th class="text-center p-1">จำนวนงวดงาน</th>
                        <th class="text-center p-1 d-none" style="width: 120px;">Action</th>
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
                        <th class="text-center p-1" width="5%">งวดงาน</th>
                        <th class="text-center p-1" width="15%">งานที่แล้วเสร็จตามแผน(%)</th>
                        <th class="text-center p-1" width="15%">จำนวนเงิน</th>
                        <th class="text-center p-1" width="10%">คิดเป็น(%)</th>
                        <th class="text-center p-1">เงื่อนไขการจ่ายเงิน</th>
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
    <script src="javascript/po.js"></script>