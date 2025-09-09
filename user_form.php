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

  <!-- flaticon -->
  <link rel="stylesheet" href="plugins/uicons-regular-rounded/css/uicons-regular-rounded.css">

  <!-- DataTables -->
  <!-- <link rel="stylesheet" href="plugins/DataTables/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/DataTables/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/DataTables/datatables-buttons/css/buttons.bootstrap4.min.css"> -->

  <!-- Theme style -->
  <link rel="stylesheet" href="plugins/dist/css/adminlte.min.css">

  <style>
    table tbody tr {
      cursor: pointer;
    }

    table thead tr {
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
    require_once  'class/connection_class.php';
    require_once  'class/user_class.php';
    require_once  'class/role_class.php';
    require_once  'class/department_class.php';

    $connection = new Connection;
    $pdo = $connection->getDbConnection();

    if ($_REQUEST['action'] == 'create') {
      $card_header_display = 'd-none';
      $disabled = '';
      $content_header = 'Create User';
    } elseif ($_REQUEST['action'] == 'update') {
      $card_header_display = 'd-inline';
      $disabled = 'disabled';
      // $user_id = $_REQUEST['user_id'];
      $content_header = 'Edit User';

      $user = new User($pdo);
      $rsUser = $user->getByUserId($_REQUEST['user_id']);
    }

    $role = new role($pdo);
    $rsRole = $role->getAll();

    $department = new department($pdo);
    $rsDepartment = $department->getAll();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-6 d-flex">
          <h6 class="m-1 fw-bold text-uppercase"><?= $content_header ?></h6>
        </div>
        <!-- /.container-fluid -->
        <div id="response-message"></div>
      </section>

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

              <div class="card">
                <div class="card-header <?= $card_header_display ?>">
                  <h6 class="m-1 fw-bold"><?= (isset($rsUser['user_code']) ? $rsUser['user_code'] : '') . " : " . (isset($rsUser['full_name']) ? $rsUser['full_name'] : '') . " - " . (isset($rsUser['role_name']) ? $rsUser['role_name'] : '') ?></h6>
                </div>

                <div class="card-body m-0 p-0">
                  <form name="myForm" id="myForm" action="" method="post"
                    data-user-id=<?= (isset($rsUser['user_id']) ? $rsUser['user_id'] : '') ?>>
                    <input type="text" class="d-none" name="user_id" id="user_id" value=<?= (isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '') ?>>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="user_code" class="input-group-text">รหัสพนักงาน</label>
                        <input type="text" class="form-control" name="user_code" id="user_code" value="<?= (isset($rsUser['user_code']) ? $rsUser['user_code'] : '') ?>" <?= $disabled ?>>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="username" class="input-group-text">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?= (isset($rsUser['username']) ? $rsUser['username'] : '') ?>" <?= $disabled ?>>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="password" class="input-group-text">รหัสผ่าน</label>
                        <input type="text" class="form-control" name="password" id="password" value="<?= (isset($rsUser['password']) ? $rsUser['password'] : '') ?>" <?= $disabled ?>>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="full_name" class="input-group-text">ชื่อพนักงาน</label>
                        <input type="text" class="form-control" name="full_name" id="full_name" value="<?= (isset($rsUser['full_name']) ? $rsUser['full_name'] : '') ?>">
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="role_id" class="input-group-text">Role</label>
                        <select class="form-select form-control" name="role_id" id="role_id">
                          <option value="">...</option>
                          <?php
                          foreach ($rsRole as $row) :
                            $selected_attr = ((isset($rsUser['role_id']) ? $rsUser['role_id'] : '') == $row['role_id']) ? " selected" : "";
                            echo "<option value='{$row['role_id']}' {$selected_attr}>{$row['role_name']}</option>";
                          endforeach ?>
                        </select>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="department_id" class="input-group-text">แผนก</label>
                        <select class="form-select form-control" name="department_id" id="department_id">
                          <option value="">...</option>
                          <?php
                          foreach ($rsDepartment as $row) :
                            $selected_attr = ((isset($rsUser['department_id']) ? $rsUser['department_id'] : '') == $row['department_id']) ? " selected" : "";
                            // $selected_attr = ($rsUser['department_id'] == $row['department_id']) ? " selected" : "";
                            echo "<option value='{$row['department_id']}' {$selected_attr}>{$row['department_name']}</option>";
                          endforeach ?>
                        </select>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <a href="" class="btn btn-primary btn-sm btnAdd <?= $mode ?>" title="Add" style="margin: 0px 5px 5px 5px;" data-bs-toggle="modal" data-bs-target="#imageModal">
                          <!-- <i class="fa-solid fa-plus"></i> -->
                          เลือกลายเซ็นต์
                        </a>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <input type="text" class="form-control" name="signature_path" id="signature_path" placeholder="ชื่อไฟล์" value="<?= (isset($rsUser['signature_path']) ? $rsUser['signature_path'] : '') ?>">
                      </div>
                    </div>

                    <!--  width="500" height="600" -->
                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm" width="600px" height="300px">
                        <img id="mainPreview" src="<?= (isset($rsUser['signature_path']) ? $rsUser['signature_path'] : '') ?>" alt="" style="max-width:600px;" class="border rounded" alt="...">
                      </div>
                    </div>
                    <!-- </div> -->

                    <hr>

                    <div class="container-fluid  p-0 d-flex justify-content-between">
                      <button type="button" name="btnBack" class="btn btn-primary btn-sm m-1 btnBack"> <i class="fi fi-rr-left"></i> </button>
                      <div>
                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" data-action="<?= $_REQUEST['action'] ?>" value="บันทึก">
                        <button type="button" name="btnCancel" class="btn btn-warning btn-sm m-1 btnCancel">ยกเลิก</button>
                      </div>
                    </div>

                    <!-- Modal : ลายเซ็นต์ -->
                    <div class="modal fade" id="imageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog">

                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">เลือกไฟล์ลายเซ็นต์</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <!-- /.modal-header -->
                          <div class="modal-body">
                            <input type="file" class="form-control" id="fileInput" accept="image/jpeg" required>
                            <small class="form-text">อนุญาตเฉพาะไฟล์ JPG</small>
                            <img id="modalPreview" src="" alt="" style="max-width:100%; margin-top:10px; display:none;" class="border rounded">
                          </div>
                          <!-- /.modal-body -->
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><!-- data-bs-dismiss="modal" ต้องการให้ Modal ปิดเมื่อปุ่มถูกคลิก -->
                            <button type="button" class="btn btn-primary" id="btnOk">OK</button>
                          </div>
                          <!-- /.modal-footer -->
                        </div>
                        <!-- /.modal-content -->
                      </div>
                      <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->

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
    <script src="javascript/user_form.js"></script>