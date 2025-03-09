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
    $rsInspectionPeriod = $po->getInspectionPeriodOneLine($po_id, $period_id);
    $rsInspectionPeriodDetail = $po->getInspectionPeriodDetail($po_id, $period_id);

    $supplier = new Supplier;
    $supplier_rs = $supplier->getRecordAll();

    $location = new Location;
    $location_rs = $location->getRecordAll();

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-6 d-flex">
          <h6 class="m-1 fw-bold text-uppercase">Inspection(ตรวจรับงาน)</h6>
        </div>
      </section>
      <!-- /.container-fluid content-header-->

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
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
                  <form id="uploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="recordName">Record Name:</label>
                      <input type="text" class="form-control" id="recordName" name="record_name" required>
                    </div>
                    <div class="form-group">
                      <label for="files">Upload Files (PDF or Images):</label>
                      <input type="file" class="form-control-file" id="files" name="files[]" multiple accept="image/*,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                      <small class="form-text text-muted">อนุญาตเฉพาะไฟล์ PDF, JPG, PNG, Excel (.xls, .xlsx) และขนาดไม่เกิน 2MB ต่อไฟล์</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Record and Files</button>
                  </form>
                  <!-- /.uploadForm -->
                  <div id="uploadStatus" class="mt-3"></div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->

              <div class="card border border-1 border-dark m-1">
                <h6 class="m-1 fw-bold">รายการไฟล์</h6>
                <!-- <div class="card-header" style="display: flex;"> -->
                <div class="card-body" id="recordsDisplay">
                </div>
              </div>
              <!-- /.card -->

              <div id="fileDisplayArea" class="card mt-4 file-display-area">
                <div class="card-header">
                  File Preview
                </div>
                <div class="card-body">
                  <p>No file selected.</p>
                </div>
              </div>

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
    <script src="javascript/inspection_edit.js"></script>