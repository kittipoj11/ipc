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
      <section class="container-fluid content-header">
        <div class="col-sm-6 d-flex">
          <h6 class="m-1 fw-bold">All Purchase Order</h6>
          <a href="po_create.php" class="btn btn-success btn-sm btnNew" title="New" style="margin: 0px 5px 5px 5px;">
            <i class="fa-solid fa-plus"></i>
          </a>
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
                <!-- <div class="card-header">
                               <h3 class="card-title">supplier</h3>
                           </div> -->
                <!-- /.card-header -->
                <div class="card-body p-0" id="card-body">
                  <table id="example1" class="table table-bordered table-striped table-sm">
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
                      <?php foreach ($rs as $row) {
                        $html = <<<EOD
                                        <tr>
                                            <td class="p-0 d-none">{$row['po_id']}</td>
                                            <td class="p-0"><a class='link-opacity-100 pe-auto' data-bs-toggle='modal'  data-bs-placement='right' title='Edit' data-bs-target='#editModal' iid='{$row['po_id']}' style='margin: 0px 5px 5px 5px'>{$row['po_no']}</a></td>
                                            <td class="p-0">{$row['project_name']}</td>
                                            <td class="p-0">{$row['supplier_name']}</td>
                                            <td class="p-0">{$row['location_name']}</td>
                                            <td class="p-0">{$row['working_name_th']}</td>
                                            <td class="p-0">{$row['contract_value_before']}</td>
                                            <td class="p-0">{$row['contract_value']}</td>
                                            <td class="p-0">{$row['number_of_period']}</td>
                                            <td class="p-0" align='center'>
                                                <div class='btn-group-sm'>
                                                    <a class='btn btn-warning btn-sm btnEdit' data-bs-toggle='modal'  data-bs-placement='right' title='Edit' data-bs-target='#openModal' iid='{$row['po_id']}' style='margin: 0px 5px 5px 5px'>
                                                        <i class='fa-regular fa-pen-to-square'></i>
                                                    </a>
                                                    <a class='btn btn-danger btn-sm btnDelete' data-bs-toggle='modal'  data-bs-placement='right' title='Delete' data-bs-target='#deleteModal' iid='{$row['po_id']}' style='margin: 0px 5px 5px 5px'>
                                                        <i class='fa-regular fa-trash-can'></i>
                                                    </a>
                                                </div>
                                            </td>
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
    </div>
    <!-- /.content-wrapper -->

    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< ส่วน Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->

    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Logout Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <!-- logout.php -->

    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Open(Insert/Update) data Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <!-- <div class="container-fluid table-responsive-sm p-0"> -->
    <div class="modal fade" id="openModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modal">จัดการข้อมูล</h1>
            <a class="" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </a>
          </div>

          <form name="frmOpen" id="frmOpen" action="" method="">
            <!-- <input type="text" name="action" id="action"> -->
            <div class="modal-body">
              <!-- <div class="row m-3 d-none"> -->
              <div class="row m-3">
                <label for="po_id" class="col-4 col-form-label">#</label>
                <div class="col-8">
                  <input type="text" class="form-control form-control-sm fst-italic" name="po_id" value="[Autonumber]" disabled>
                </div>
              </div>

              <div class="row m-3">
                <label for="po_no" class="col-4 col-form-label">เลขที่ PO</label>
                <div class="col-8">
                  <input type="text" class="form-control form-control-sm" name="po_no" id="po_no">
                </div>
              </div>

              <div class="row m-3">
                <label for="project_name" class="col-4 col-form-label">ชื่อโครงการ</label>
                <div class="col-8">
                  <input type="text" class="form-control form-control-sm" name="project_name" id="project_name">
                </div>
              </div>

              <div class="row m-3">
                <label for="supplier_id" class="col-4 col-form-label">ผู้รับเหมา</label>
                <div class="col-8">
                  <select class="form-select form-control form-control-sm supplier_id" name="supplier_id">
                    <?php
                    foreach ($supplier_rs as $row) :
                      echo "<option value='{$row['supplier_id']}'>{$row['supplier_name']}</option>";
                    endforeach ?>
                  </select>
                </div>
              </div>

              <div class="row m-3">
                <label for="location_id" class="col-4 col-form-label">สถานที่</label>
                <div class="col-8">
                  <select class="form-select form-control form-control-sm location_id" name="location_id">
                    <?php
                    foreach ($location_rs as $row) :
                      echo "<option value='{$row['location_id']}'>{$row['location_name']}</option>";
                    endforeach ?>
                  </select>
                </div>
              </div>

              <div class="row m-3">
                <label for="working_name_th" class="col-4 col-form-label">ชื่องาน(ภาษาไทย)</label>
                <div class="col-8">
                  <input type="text" class="form-control form-control-sm" name="working_name_th" id="working_name_th">
                </div>
              </div>

              <div class="row m-3">
                <label for="working_name_en" class="col-4 col-form-label">ชื่องาน(ภาษาอังกฤษ)</label>
                <div class="col-8">
                  <input type="text" class="form-control form-control-sm" name="working_name_en" id="working_name_en">
                </div>
              </div>

              <div class="row m-3">
                <label for="contract_value" class="col-4 col-form-label">มูลค่างาน</label>
                <div class="col-8">
                  <input type="number" class="form-control form-control-sm" name="contract_value" id="contract_value">
                </div>
              </div>

              <div class="row m-3">
                <label for="vat" class="col-4 col-form-label">VAT</label>
                <div class="col-8">
                  <input type="number" class="form-control form-control-sm" name="vat" id="vat">
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" name="btnSaveData" id="btnSaveData" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>

        </div>
      </div>
    </div>

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