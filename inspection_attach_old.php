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

    body {
      padding-top: 20px;
    }

    .file-link {
      display: block;
      margin-bottom: 5px;
    }

    .file-list-item {
      cursor: pointer;
      /* เปลี่ยน cursor เป็น pointer เมื่อ hover */
      color: blue;
      /* กำหนดสีของลิงก์ชื่อไฟล์ */
      text-decoration: underline;
      /* ขีดเส้นใต้ลิงก์ชื่อไฟล์ */
    }

    .file-display-area {
      margin-top: 20px;
      border: 1px solid #ddd;
      padding: 10px;
      border-radius: 5px;
    }

    .file-display-area img {
      max-width: 100%;
      max-height: 400px;
      /* ปรับขนาดรูปภาพแสดงผล */
      display: block;
      margin: 0 auto;
      /* จัดรูปภาพไว้ตรงกลาง */
    }

    .file-display-area embed {
      width: 100%;
      height: 600px;
      /* ปรับขนาด PDF viewer */
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
    $rsInspectionPeriod = $po->getInspectionOnePeriod($po_id, $period_id);
    $rsInspectionFiles = $po->getInspectionFiles($po_id, $period_id, $inspection_id);

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
          <form id="uploadForm" enctype="multipart/form-data">
            <div class="border border-1 border-dark m-1">
              <div class="card">
                <div class="card-header d-flex align-items-center">
                  <input type="text" class="form-control d-none" name="inspection_id" id="inspection_id" value="<?= $rsInspectionPeriod['inspection_id'] ?>">
                  <input type="text" class="form-control d-none" name="period_id" id="period_id" value="<?= $rsInspectionPeriod['period_id'] ?>">
                  <input type="text" class="form-control d-none" name="po_id" id="po_id" value="<?= $rsInspectionPeriod['po_id'] ?>">

                  <h6 class="m-1 fw-bold"><?= $rsInspectionPeriod['po_number'] . " : " . $rsInspectionPeriod['supplier_id'] . " - " . $rsInspectionPeriod['supplier_name'] ?></h6>
                  <h6 class="m-1 fw-bold"><?= "[งวดงานที่ " . $rsInspectionPeriod['period_number'] . "]" ?></h6>
                </div>

                <div class="card-header d-flex">
                  <h6 class="m-1 fw-bold">รายการไฟล์</h6>
                  <!-- เรียก Modal -->
                  <a href="" class="btn btn-success btn-sm btnAdd" title="Add" style="margin: 0px 5px 5px 5px;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    <i class="fa-solid fa-plus"></i>
                  </a>
                </div>

                <!-- ส่วนของตารางแสดงรายชื่อไฟล์ -->
                <div class="card-body m-0 p-0" id="recordsDisplay">
                  <table id="tableMain" class="table table-bordered table-striped table-sm">
                    <thead>
                      <tr>
                        <th class="text-center p-1">#</th>
                        <th class="text-center p-1">ชื่อไฟล์</th>
                        <th class="text-center p-1" style="width: 120px;"></th>
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

            <div class="card mt-4 file-display-area" id="fileDisplayArea">
              <div class="card-header">
                File Preview
              </div>
              <div class="card-body">
                <p>No file selected.</p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- Modal : Upload -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Attach Files</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">

                    <div class="card border border-1 border-dark m-1">
                      <div class="card-body m-0 p-0">
                        <div class="form-group">
                          <label for="recordName">Record Name:</label>
                          <input type="text" class="form-control" id="recordName" name="record_name" value="test">
                        </div>
                        <div class="form-group">
                          <label for="files">Upload Files (PDF or Images):</label>
                          <input type="file" class="form-control-file" id="files" name="files[]" multiple accept="image/*,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" required>
                          <small class="form-text text-muted">อนุญาตเฉพาะไฟล์ PDF, JPG, PNG, Word (.doc, .docx), Excel (.xls, .xlsx), PowerPoint (.ppt, .pptx) และขนาดไม่เกิน 2MB ต่อไฟล์</small>
                        </div>
                        <div id="uploadStatus" class="mt-3">

                        </div>
                      </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                  </div>
                  <!-- /.modal-body -->
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <!-- วิธีที่ 1. เป็นวิธีที่ง่ายและสะดวกที่สุด หากเพียงต้องการให้ Modal ปิดเมื่อปุ่มถูกคลิก -->
                    <!-- <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Upload Files</button> -->

                    <!-- วิธีที่ 2. ไม่ได้กำหนด data-bs-dismiss="modal" เพราะต้องการให้สั่งการทำงานจาก javascript -->
                    <button type="submit" class="btn btn-primary">Upload Files</button>
                  </div>
                  <!-- /.uploadForm -->
                </div>
                <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

          </form>
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
    <script src="javascript/inspection_attach.js"></script>