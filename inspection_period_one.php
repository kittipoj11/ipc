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
        require_once  'class/inspection_class.php';
        require_once  'class/po_class.php';
        require_once  'class/plan_status_class.php';

        $inspection = new Inspection;
        // $rs = $inspection->getAllRecord();

        $plan_status = new Plan_status;
        $plan_status_rs = $plan_status->getAllRecord();

        $rs = ['1' => 'งานเดินท่อ', '2' => 'งานติดตั้ง1', '3' => 'งานติดตั้ง2'];

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <!-- <h4>Inspections: < ?=$rsHeader[0]['inspect_id'] ?> -->
                            <h4>Inspections: xxxxx(งวดงานที่ 1)</h4>
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
                                        <!-- <div class="card border border-1 border-dark mt-3"> -->
                                        <div class="card border border-1 border-light mt-3">
                                            <div class="row d-none border border-1 border-light">
                                                <label for="po_id" class="col-3">#</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control form-control-sm fst-italic" name="po_id" value="[Autonumber]" disabled>
                                                </div>
                                            </div>

                                            <div class="row border border-1 border-light">
                                                <div class="col-6 input-group">
                                                    <label for="supplier_id" class="input-group-text">ผู้รับเหมา</label>
                                                    <input type="text" class="form-control-plaintext" readonly name="supplier_id" id="supplier_id">
                                                </div>
                                            </div>

                                            <div class="row border border-1 border-light">
                                                <div class="col-8 input-group">
                                                    <label for="project_name" class="input-group-text">โครงการ</label>
                                                    <input type="text" class="form-control-plaintext" readonly name="project_name" id="project_name">
                                                </div>

                                                <div class="col-4 input-group">
                                                    <label for="location_id" class="input-group-text">สถานที่</label>
                                                    <input type="text" class="form-control-plaintext" readonly name="location_id" id="location_id">
                                                </div>
                                            </div>
                                            <div class="row border border-1 border-light">
                                                <div class="col-12 input-group">
                                                    <label for="working_name_th" class="input-group-text">งาน</label>
                                                    <!-- <input type="text" class="form-control-plaintext-plaintext" readonly name="working_name_th" id="working_name_th"> -->
                                                    <input type="text" class="form-control-plaintext" readonly name="working_name_th" id="working_name_th">
                                                </div>

                                                <!-- <div class="row">
                                            <div class="col-6 input-group">
                                                <label for="working_name_th" class="input-group-text">ชื่องาน(ภาษาไทย)</label>
                                                <input type="text" class="form-control" readonly name="working_name_th" id="working_name_th">
                                            </div>

                                            <div class="col-6 input-group">
                                                <label for="working_name_en" class="input-group-text">ชื่องาน(ภาษาอังกฤษ)</label>
                                                <input type="text" class="form-control" readonly name="working_name_en" id="working_name_en">
                                            </div>-->
                                            </div>

                                            <div class="row">
                                                <div class="col-12 input-group border border-1 border-light">
                                                    <label for="working_date_from" class="input-group-text">ระยะเวลาดำเนินการ</label>
                                                    <!-- <input type="text" class="form-control-plaintext" readonly name="working_date_from" id="working_date_from"> -->
                                                    <input type="date" class="form-control-plaintext" readonly name="working_date_from" id="working_date_from">
                                                    <label for="working_date_to" class="input-group-text">ถึง</label>
                                                    <input type="date" class="form-control-plaintext" readonly name="working_date_to" id="working_date_to">
                                                    <label for="working_day" class="input-group-text">รวม</label>
                                                    <input type="number" class="form-control-plaintext" readonly name="working_day" id="working_day" value="100">
                                                    <label for="working_day" class="input-group-text">วัน</label>
                                                </div>

                                                <!-- <div class="row">
                                            <div class="col-6 input-group">
                                                <label for="working_name_th" class="input-group-text">ชื่องาน(ภาษาไทย)</label>
                                                <input type="text" class="form-control" readonly name="working_name_th" id="working_name_th">
                                            </div>

                                            <div class="col-6 input-group">
                                                <label for="working_name_en" class="input-group-text">ชื่องาน(ภาษาอังกฤษ)</label>
                                                <input type="text" class="form-control" readonly name="working_name_en" id="working_name_en">
                                            </div>-->
                                            </div>
                                        </div>

                                        <div class="card border border-1 border-light mt-3">
                                            <div class="row">
                                                <div class="col-12 input-group">
                                                    <label for="po_no" class="input-group-text">เลขที่ PO</label>
                                                    <!-- <input type="text" class="form-control-plaintext" readonly name="po_no" id="po_no"> -->
                                                    <input type="text" class="form-control-plaintext" readonly name="po_no" id="po_no">
                                                    <label for="contract_value" class="input-group-text">มูลค่างาน</label>
                                                    <input type="number" class="form-control-plaintext" readonly name="contract_value" id="contract_value">
                                                    <input type="text" class="form-control-plaintext d-none" readonly value="(Including VAT 7%)">
                                                    <!-- <label for="vat" class="input-group-text">(Including VAT 7%)</label> -->
                                                </div>
                                            </div>

                                        </div>

                                        <div class="card border border-1 border-dark mt-3">
                                            <div class="row d-flex justify-content-around">
                                                <div class="col-4 form-check ps-5">
                                                    <input class="form-check-input" type="checkbox" value="">
                                                    <label class="form-check-label">Deposit</label>
                                                </div>
                                                <div class="col-8 border border-1 border-light">
                                                    <table class="table table-sm tableborder-0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="25%">ยอดเบิกเงินงวดปัจจุบัน</td>
                                                                <td width="25%" class="text-end">12345678 บาท</td>
                                                                <td width="25%" class="text-center">(Including VAT 7%)</td>
                                                                <td width="10%">คิดเป็น</td>
                                                                <td width="10%" class="text-end">15</td>
                                                                <td width="5%" class="text-center">%</td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</td>
                                                                <td width="25%" class="text-end">12345678 บาท</td>
                                                                <td width="25%" class="text-center">(Including VAT 7%)</td>
                                                                <td width="10%">คิดเป็น</td>
                                                                <td width="10%" class="text-end">15</td>
                                                                <td width="5%" class="text-center">%</td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">ยอดเงินงวดคงเหลือ</td>
                                                                <td width="25%" class="text-end">12345678 บาท</td>
                                                                <td width="25%" class="text-center">(Including VAT 7%)</td>
                                                                <td width="10%">คิดเป็น</td>
                                                                <td width="10%" class="text-end">15</td>
                                                                <td width="5%" class="text-center">%</td>
                                                            </tr>

                                                        </tbody>
                                                    </table>

                                                    <!-- <label for="vat" class="input-group-text">(Including VAT 7%)</label> -->
                                                </div>
                                            </div>

                                        </div>

                                        <div class="card border border-1 border-dark mt-3">
                                            <div class="col-12 border border-1 border-light">
                                                <table class="container-fluid tableborder-0">
                                                    <tbody>
                                                        <tr>
                                                            <td width="33%" class="text-start">ปริมาณที่ต้องแล้วเสร็จตามแผนงาน 30 %</td>
                                                            <td width="33%" class="text-start">ปริมาณที่แล้วเสร็จจริง 30 %</td>
                                                            <td width="33%" class="text-start">ปริมาณงานคงเหลือ 30 %</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card border border-1 border-dark mt-3" id="div_open_area_schedule">
                                            <!-- <div class="card-header" style="display: flex;"> -->
                                            <div class="card-header p-2">
                                                <h5>รายละเอียด</h5>

                                            </div>

                                            <div class="card-body p-0">
                                                <!-- สร้าง Table ตามปกติ -->
                                                <table class="table table-sm table-bordered justify-content-center text-center">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">ลำดับที่</th>
                                                            <th width="70%">รายละเอียดการตรวจสอบ</th>
                                                            <th>หมายเหตุ</th>
                                                            <th style="display:none">id</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">
                                                        <?php foreach ($rs as $key => $row) {
                                                            $html = <<<EOD
                                                                        <tr id="{$key}">
                                                                            <td>{$key}</td>
                                                                            <td class="text-start">{$row}</td>
                                                                            <td class="text-start">???</td>
                                                                        </tr>
                                                                        EOD;
                                                            echo $html;
                                                        } ?>

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                        <div class="row input-group">
                                            <label for="plan_status_id" class="col-4 col-form-label">ปริมาณงานแล้วเสร็จเมื่อเปรียบเทียบกับแผนงาน</label>
                                            <div class="col-2">
                                                <select class="form-select form-control" name="plan_status_id">
                                                    <option value="">...</option>
                                                    <?php
                                                    foreach ($plan_status_rs as $row) :
                                                        echo "<option value='{$row['plan_status_id']}'>{$row['plan_status_name']}</option>";
                                                    endforeach ?>
                                                </select>
                                            </div>
                                            <!-- <div class="form-check d-inline-block">
                                                <input class="form-check-input" type="radio" value="" name="flexCheckChecked">
                                                <label class="form-check-label" for="flexCheckChecked">
                                                    ตามแผนงาน
                                                </label>
                                            </div>
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" type="radio" value="" name="flexCheckChecked">
                                                <label class="form-check-label" for="flexCheckChecked">
                                                    ล่าช้ากว่าแผนงาน
                                                </label>
                                            </div>
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" type="radio" value="" name="flexCheckChecked">
                                                <label class="form-check-label" for="flexCheckChecked">
                                                    เร็วกว่าแผนงาน
                                                </label>
                                            </div> -->
                                        </div>
                                        <div class="mt-3">
                                            <div class="card-header p-2 d-none">
                                                <h6>หมายเหตุ</h6>
                                            </div>
                                            <div class="form-floating">
                                                <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                                                <label for="floatingTextarea2">หมายเหตุ</label>
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