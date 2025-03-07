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
        require_once  'class/inspection_class.php';
        require_once  'class/po_class.php';

        $inspection = new Inspection;
        // $rs = $inspection->getRecordAll();

        $rs = [0 => 'งวดที่ 1', 1 => 'งวดที่ 2', 2 => 'งวดที่ 3'];

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <!-- <h4>Inspections: < ?=$rsHeader[0]['inspect_id'] ?> -->
                            <h4>Inspections: xxxxx
                            </h4>
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
                               <h3 class="card-title">po</h3>
                           </div> -->
                                <!-- /.card-header -->
                                <div class="card-body" id="card-body">
                                    <form name="myForm" id="myForm">

                                        <div class="row d-none">
                                            <label for="po_id" class="col-3">#</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control form-control-sm fst-italic" name="po_id" value="[Autonumber]" disabled>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 input-group">
                                                <label for="po_no" class="input-group-text">เลขที่ PO</label>
                                                <!-- <input type="text" class="form-control-plaintext" readonly name="po_no" id="po_no"> -->
                                                <input type="text" class="form-control" readonly name="po_no" id="po_no">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-8 input-group">
                                                <label for="project_name" class="input-group-text">ชื่อโครงการ</label>
                                                <input type="text" class="form-control" readonly name="project_name" id="project_name">
                                            </div>

                                            <div class="col-4 input-group">
                                                <label for="location_id" class="input-group-text">สถานที่</label>
                                                <select class="form-select form-control" disabled name="location_id" id="location_id">
                                                    <option value="">...</option>
                                                    <?php
                                                    foreach ($location_rs as $row) :
                                                        echo "<option value='{$row['location_id']}'>{$row['location_name']}</option>";
                                                    endforeach ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6 input-group">
                                                <label for="working_name_th" class="input-group-text">ชื่องาน(ภาษาไทย)</label>
                                                <input type="text" class="form-control" readonly name="working_name_th" id="working_name_th">
                                            </div>

                                            <div class="col-6 input-group">
                                                <label for="working_name_en" class="input-group-text">ชื่องาน(ภาษาอังกฤษ)</label>
                                                <input type="text" class="form-control" readonly name="working_name_en" id="working_name_en">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6 input-group">
                                                <label for="supplier_id" class="input-group-text">ผู้รับเหมา</label>
                                                <select class="form-select form-control" disabled name="supplier_id" id="supplier_id">
                                                    <option value="">...</option>
                                                    <?php
                                                    foreach ($supplier_rs as $row) :
                                                        echo "<option value='{$row['supplier_id']}'>{$row['supplier_name']}</option>";
                                                    endforeach ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-5 input-group">
                                                <label for="contract_value" class="input-group-text">มูลค่างาน</label>
                                                <input type="number" class="form-control" readonly name="contract_value" id="contract_value">
                                            </div>

                                            <div class="col-3 form-check">
                                                <input class="form-check-input" type="checkbox" disabled value="" id="include_vat">
                                                <label class="form-check-label" for="include_vat">Include VAT</label>
                                            </div>

                                            <div class="col-4 input-group">
                                                <label for="vat" class="input-group-text">VAT</label>
                                                <input type="text" class="form-control" readonly name="vat" id="vat">
                                            </div>
                                        </div>

                                        <div class="row inline d-none">
                                            <!-- ค่า Default ที่ดึงจาก tbl_hall-->
                                            <div class="col-sm-3 px-0">
                                                <div class="input-group input-group-sm mb-1">
                                                    <span class="input-group-text">Time Start</span>
                                                    <div class="col">
                                                        <input type="time" name="time_start_header" id="time_start_header" class="form-control" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 px-0">
                                                <div class="input-group input-group-sm mb-1">
                                                    <span class="input-group-text">Time End</span>
                                                    <div class="col">
                                                        <input type="time" class="form-control" readonly name="time_end_header" id="time_end_header">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card border border-1 border-dark mt-3" id="div_open_area_schedule">
                                            <!-- <div class="card-header" style="display: flex;"> -->
                                            <div class="card-header p-2">
                                                <h5>งวดงาน</h5>

                                            </div>

                                            <div class="card-body p-0">
                                                <!-- สร้าง Table ตามปกติ -->
                                                <table class="table table-bordered justify-content-center text-center">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">งวดงาน</th>
                                                            <th width="20%">จำนวนเงิน</th>
                                                            <th>เงื่อนไขการจ่ายเงิน</th>
                                                            <th style="display:none">id</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">
                                                        <?php foreach ($rs as $key => $row) {
                                                            $html = <<<EOD
                                                                        <tr id="{$key}">
                                                                            <td><a href="inspection_period_one.php">{$row}</a></td>
                                                                            <td>จำนวนเงิน</td>
                                                                            <td>เงื่อนไขการจ่ายเงิน</td>
                                                                        </tr>
                                                                        EOD;
                                                            echo $html;
                                                        } ?>

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                        <div class="modal-footer d-none">
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

        <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< ส่วน Modal >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->

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