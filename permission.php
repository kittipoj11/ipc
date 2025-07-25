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
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">


  <!-- Page Wrapper -->
  <div class="wrapper">

    <?php include 'sidebar.php'; ?>
    <?php include 'navbar.php'; ?>

    <!-- Main Content Start -->
    <?php
    require_once  'class/permission_class.php';
    $permission = new Permission;
    $rs = $permission->getAll();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6 d-flex">
              <h4>Permission</h4>
              <a class="btn btn-success btn-sm btnNew" data-bs-toggle="modal" data-bs-placement="right" title="New" data-bs-target="#openModal" style="margin: 0px 5px 5px 5px;">
                <i class="fa-solid fa-plus"></i>
              </a>
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
                               <h3 class="card-title">permission</h3>
                           </div> -->
                <!-- /.card-header -->
                <div class="card-body" id="card-body">
                  <table id="example1" class="table table-bordered table-striped table-sm">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 100px;">#</th>
                        <th class="text-center">Permission name</th>
                        <th class="text-center" style="width: 120px;">Action</th>
                      </tr>
                    </thead>
                    <tbody id="tbody">
                      <!-- < ?php foreach ($rs as $row) {
                        $html = <<<EOD
                                        <tr id="{$row['permission_id']}">
                                            <td>{$row['permission_id']}</td>
                                            <td>{$row['permission_name']}</td>
                                            <td align='center'>
                                                <div class='btn-group-sm'>
                                                    <a class='btn btn-warning btn-sm btnEdit' data-bs-toggle='modal'  data-bs-placement='right' title='Edit' data-bs-target='#openModal' iid='{$row['permission_id']}' style='margin: 0px 5px 5px 5px'>
                                                        <i class='fa-regular fa-pen-to-square'></i>
                                                    </a>
                                                    <a class='btn btn-danger btn-sm btnDelete' data-bs-toggle='modal'  data-bs-placement='right' title='Delete' data-bs-target='#deleteModal' iid='{$row['permission_id']}' style='margin: 0px 5px 5px 5px'>
                                                        <i class='fa-regular fa-trash-can'></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        EOD;
                        echo $html;
                      } ?> -->
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
    <div class="modal fade" id="openModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
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
              <div class="row m-3">
                <label for="permission_id" class="col-sm-6 col-form-label">#</label>
                <div class="col-sm-6">
                  <!-- <input type="hidden" class="permission_id" name="permission_id"> -->
                  <input type="input" class="form-control form-control-sm fst-italic permission_id" id="permission_id" readonly name="permission_id">
                </div>
              </div>

              <div class="row m-3">
                <label for="permission_name" class="col-sm-6 col-form-label">permission name</label>
                <div class="col-sm-6">
                  <input type="input" class="form-control form-control-sm" name="permission_name" id="permission_name">
                </div>
              </div>

              <div class="row m-3">
                <label for="menu_name" class="col-sm-6 col-form-label">menu name</label>
                <div class="col-sm-6">
                  <input type="input" class="form-control form-control-sm" name="menu_name" id="menu_name">
                </div>
              </div>

              <div class="row m-3">
                <label for="content_filename" class="col-sm-6 col-form-label">content filename</label>
                <div class="col-sm-6">
                  <input type="input" class="form-control form-control-sm" name="content_filename" id="content_filename">
                </div>
              </div>

              <div class="row m-3">
                <label for="function_name" class="col-sm-6 col-form-label">function name</label>
                <div class="col-sm-6">
                  <input type="input" class="form-control form-control-sm" name="function_name" id="function_name">
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
    <script src="javascript/permission.js"></script>