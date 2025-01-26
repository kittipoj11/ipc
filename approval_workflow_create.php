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
    table tr {
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
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';
    $po = new Po;
    $rs = $po->getAllRecord();

    $supplier = new Supplier;
    $supplier_rs = $supplier->getAllRecord();

    $location = new Location;
    $location_rs = $location->getAllRecord();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6 d-flex">
              <h4>Create Approval Level(กำหนดระดับการอนุมัติ)</h4>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>
      <!-- <div class="result"></div> -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

              <div class="card">
                <!-- <div class="card-header">
                               <h3 class="card-title">ชื่อพื้นที่</h3>
                           </div> -->
                <!-- /.card-header -->
                <div class="card-body">
                  <form name="myForm" id="myForm">

                    <div class="row d-none">
                      <label for="po_id" class="col-3">#</label>
                      <div class="col-9">
                        <input type="text" class="form-control form-control-sm fst-italic" name="po_id" value="[Autonumber]" disabled>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12 input-group">
                        <label for="po_no" class="input-group-text">Workflow name</label>
                        <input type="text" class="form-control" name="po_no" id="po_no">
                      </div>
                    </div>

                    <div class="card border border-1 border-dark mt-3" id="div_open_area_schedule">
                      <!-- <div class="card-header" style="display: flex;"> -->
                      <div class="card-header p-2">
                        <a id="btnAdd" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="right" style="margin: 0px 5px 5px 5px;" title="เพิ่มรายการ">
                          + Level
                        </a>
                        <!-- <a id="btnDeleteListx" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="right" style="margin: 0px 5px 5px 5px;display:none" title="ลบรายการล่าสุด">
                                               Delete
                                           </a> -->

                        <a id="btnClear" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" style="margin: 0px 5px 5px 5px;" title="ลบรายการทั้งหมด">
                          Clear
                        </a>
                      </div>

                      <div class="card-body p-0">
                        <!-- สร้าง Table ตามปกติ -->
                        <table class="table table-bordered justify-content-center text-center">
                          <thead>
                            <tr>
                              <th width="10%">Sequence</th>
                              <th width="20%">Approval level</th>
                            </tr>
                          </thead>
                          <tbody id="tableBody">
                            <tr class="firstTr">
                              <!-- กำหนดลำดับ Auto 1, 2, 3, ... -->
                              <td class="p-1"><input type="number" name="level_order[]" class="form-control level_order">
                              </td>
                              <td class="p-1"><input type="number" name="approval_level[]" class="form-control approval_level" require>
                              </td>

                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <a type="submit" name="btnSave" id="btnSave" class="btn btn-primary">บันทึก</a>
                      <a type="button" name="btnCancel" id="btnCancel" class="btn btn-secondary">ยกเลิก</a>
                    </div>

                  </form>
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
    <script src="javascript/po_create.js"></script>