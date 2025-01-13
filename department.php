<?php
@session_start();
require_once 'config.php';
require_once 'auth.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>IPC | Test</title>
  <link rel="shortcut icon" href="images/inspection.png" type="image/png">

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
    require_once  'class/department.class.php';
    $department = new department;
    $rs = $department->getAllRecord();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6 d-flex">
              <h4>Department</h4>
              <a class="btn btn-success btn-sm" data-bs-toggle="modal" data-placement="right" title="เพิ่มข้อมูล" data-bs-target="#insertModal" style="margin: 0px 5px 5px 5px;">
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
                               <h3 class="card-title">department</h3>
                           </div> -->
                <!-- /.card-header -->
                <div class="card-body" id="card-body">
                  <table id="example1" class="table table-bordered table-striped table-sm">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 100px;">#</th>
                        <th class="text-center">department name</th>
                        <th class="text-center" style="width: 120px;">Action</th>
                      </tr>
                    </thead>
                    <tbody id="tbody">
                      <?php foreach ($rs as $row) {
                        $html = <<<EOD
                                        <tr>
                                            <td>{$row['department_id']}</td>
                                            <td>{$row['department_name']}</td>
                                            <td align='center'>
                                                <div class='btn-group-sm'>
                                                    <a class='btn btn-warning btn-sm btnEdit' data-toggle='modal'  data-placement='right' title='Edit' data-target='#editModal' iid='{$row['department_id']}' style='margin: 0px 5px 5px 5px'>
                                                        <i class='fa-regular fa-pen-to-square'></i>
                                                    </a>
                                                    <a class='btn btn-danger btn-sm btnDelete' data-toggle='modal'  data-placement='right' title='Delete' data-target='#deleteModal' iid='{$row['department_id']}' style='margin: 0px 5px 5px 5px'>
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

    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Insert data Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5">เพิ่มข้อมูลใหม่</h1>
            <a class="btn-close" data-dismiss="modal" aria-label="Close">
              <!-- <i class="fa-regular fa-circle-xmark"></i> -->
              <i class="fa-solid fa-xmark"></i>
            </a>
          </div>

          <form name="frmInsert" id="frmInsert" action="" method="">
            <div class="modal-body">
              <div class="row m-3">
                <label for="department_id" class="col-sm-6 col-form-label">#</label>
                <div class="col-sm-6">
                  <input type="input" class="form-control form-control-sm fst-italic" name="department_id" value="[Autonumber]" disabled>
                </div>
              </div>

              <div class="row m-3">
                <label for="department_name" class="col-sm-6 col-form-label">department name</label>
                <div class="col-sm-6">
                  <input type="input" class="form-control form-control-sm" name="department_name" id="department_name">
                </div>
              </div>

              <!-- <div class="row">
                                    <div class="col-sm-12 mb-2">
                                        <div class="input-group input-group-sm mb-1">
                                            <span class="input-group-text">Active</span>
                                            <label class="switch ms-2"><input type="checkbox" name='is_active_i' checked>
                                            <span class="slider round"></span></label>
                                        </div>
                                    </div>
                                </div> -->
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" name="btnInsertData" id="btnInsertData" class="btn btn-primary" data-dismiss="modal">Save</button>
            </div>
          </form>

        </div>
      </div>
    </div>

    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit data Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <!-- <div class="container-fluid table-responsive-sm p-0"> -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modal">แก้ไขข้อมูล</h1>
            <a class="btn-close" data-dismiss="modal" aria-label="Close">
              <!-- <i class="fa-regular fa-circle-xmark"></i> -->
              <!-- <i class="bi bi-x"></i> -->
              <i class="fa-solid fa-xmark"></i>
            </a>
          </div>

          <form name="frmEdit" id="frmEdit" action="" method="">
            <!-- <input type="text" name="action" id="action"> -->
            <div class="modal-body">
              <div class="row m-3">
                <label for="department_id" class="col-sm-6 col-form-label">#</label>
                <div class="col-sm-6">
                  <!-- <input type="hidden" class="department_id" name="department_id"> -->
                  <input type="input" class="form-control form-control-sm fst-italic department_id" id="department_id" readonly name="department_id">
                </div>
              </div>

              <div class="row m-3">
                <label for="department_name" class="col-sm-6 col-form-label">department name</label>
                <div class="col-sm-6">
                  <input type="input" class="form-control form-control-sm" name="department_name" id="department_name">
                </div>
              </div>

              <!-- <div class="row">
                                <div class="col-sm-12 mb-2">
                                    <div class="input-group input-group-sm mb-1">
                                        <span class="input-group-text">Active</span>
                                        <label class="switch ms-2"><input type="checkbox" name='is_active_i' checked>
                                        <span class="slider round"></span></label>
                                    </div>
                                </div>
                            </div> -->
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" name="btnUpdateData" id="btnUpdateData" class="btn btn-primary" data-dismiss="modal">Save</button>
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
    <script src="javascript/department.js"></script>