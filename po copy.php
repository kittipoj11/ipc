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
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';
    $po = new Po;
    $rsPoMainAll = $po->getPoMainAll();
    $rsPoPeriod = $po->getPoPeriodByPoId(0);
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

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

              <div class="card">
                <div class="card-header d-flex">
                  <h6 class="m-1 fw-bold">All Purchase Order</h6>
                  <a href="po_dml.php?action=insert" class="btn btn-success btn-sm btnNew" title="New" style="margin: 0px 5px 5px 5px;">
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

    </div>
    <!-- /.content-wrapper -->

    <!-- Main Content End -->

    <?php include 'logout_modal.php'; ?>

    <?php include 'footer_bar.php'; ?>


    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- Bootstrap 5.3.3 -->
    <script src="plugins/bootstrap-5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="plugins/jQuery-3.7.1/jquery-3.7.1.min.js"></script>
    <!-- AdminLTE App -->
    <script src="plugins/dist/js/adminlte.js"></script>
    <!-- Sweet Alert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- My JavaScript  -->
    <script src="javascript/po.js"></script>