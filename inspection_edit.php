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
    // require_once  'class/po_class.php';
    require_once  'class/inspection_class.php';
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';

    $po_id = $_REQUEST['po_id'];
    $po_period_id = $_REQUEST['po_period_id'];
    $inspection = new Inspection;
    $rsInspectionPeriod = $inspection->getPeriodOneLine($po_id, $po_period_id);

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
          <h6 class="m-1 fw-bold"><?= $rsInspectionPeriod['po_no'] . " : " . $rsInspectionPeriod['supplier_id'] . " - " . $rsInspectionPeriod['supplier_name'] ?></h6>
        </div>
        <!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <form name="myForm" id="myForm" action="" method="post">
                <div class="card">
                  <div class="card-body m-0 p-0">

                    <input type="text" class="form-control d-none" name="inspect_id" id="inspect_id" value=<?= $rsInspectionPeriod['inspect_id'] ?>>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="supplier_name" class="input-group-text">ผู้รับเหมา</label>
                        <input type="text" class="form-control" name="supplier_name" id="supplier_name" value=<?= $rsInspectionPeriod['supplier_name'] ?>>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-6 input-group input-group-sm">
                        <label for="project_name" class="input-group-text">โครงการ</label>
                        <input type="text" class="form-control" name="project_name" id="project_name" value="<?= $rsInspectionPeriod['project_name'] ?>">
                      </div>

                      <div class="col-6 input-group input-group-sm">
                        <label for="location_name" class="input-group-text">สถานที่</label>
                        <input type="text" class="form-control" name="location_name" id="location_name" value="<?= $rsInspectionPeriod['location_name'] ?>">
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-6 input-group input-group-sm">
                        <label for="working_name_th" class="input-group-text">งาน</label>
                        <input type="text" class="form-control" name="working_name_th" id="working_name_th" value="<?= $rsInspectionPeriod['working_name_th'] ?> (<?= $rsInspectionPeriod['working_name_en'] ?>)">
                      </div>

                      <div class="col-6 input-group input-group-sm d-none">
                        <label for="working_name_en" class="input-group-text">งาน</label>
                        <input type="text" class="form-control" name="working_name_en" id="working_name_en" value="<?= $rsInspectionPeriod['working_name_en'] ?>">
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_from" class="input-group-text ">ระยะเวลาดำเนินการ</label>
                          <input type="date" class="form-control " name="working_date_from" id="working_date_from" value="<?php echo isset($rsInspectionPeriod['working_date_from']) ? htmlspecialchars($rsInspectionPeriod['working_date_from']) : ''; ?>">

                        </div>
                      </div>
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_to" class="input-group-text "> ถึง </label>
                          <input type="date" class="form-control " name="working_date_to" id="working_date_to" value="<?php echo isset($rsInspectionPeriod['working_date_to']) ? htmlspecialchars($rsInspectionPeriod['working_date_to']) : ''; ?>">
                        </div>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <label for="working_day" class="input-group-text">รวม</label>
                        <input type="number" class="form-control" name="working_day" id="working_day" value="<?php echo isset($rsInspectionPeriod['working_day']) ? htmlspecialchars($rsInspectionPeriod['working_day']) : ''; ?>" readonly>
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="po_no" class="input-group-text">เลขที่ PO</label>
                        <input type="text" class="form-control" name="po_no" id="po_no" value=<?= $rsInspectionPeriod['po_no'] ?>>
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="contract_value" class="input-group-text">มูลค่างานตาม PO</label>
                        <input type="number" class="form-control" name="contract_value" id="contract_value" value=<?= $rsInspectionPeriod['contract_value'] ?>>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <?php
                        $display_include_vat = $rsInspectionPeriod['is_include_vat'] ? "(Including VAT 7% )" : "";
                        ?>
                        <label for="vat" class="input-group-text d-none">(Includeing VAT</label>
                        <input type="text" class="form-control border border-0" name="vat" id="vat" value="<?= $display_include_vat ?>">
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1">
                      <div class="col-3 border-end border-dark-subtle m-0 p-0">
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="period" class="input-group-text">เบิกงวดงานที่ </label>
                            <input type="text" class="form-control" name="period" value=<?= $rsInspectionPeriod['period'] ?>>
                          </div>
                        </div>
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <div class="form-check">
                              <?php
                              $checked_attr = $rsInspectionPeriod['is_deposit'] ? "checked" : "";
                              ?>
                              <input class="form-check-input" type="checkbox" name="is_deposit" id="is_deposit" <?= $checked_attr ?>>
                            </div>
                            <label class="form-check-label" for="deposit_percent">มีเงินมัดจำ </label>
                            <input type="number" class="form-control" name="deposit_percent" id="deposit_percent" value=<?= $rsInspectionPeriod['deposit_percent'] ?>>%
                          </div>
                        </div>
                      </div>

                      <div class="col-9">
                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment" class="col-3 input-group-text">ยอดเบิกเงินงวดปัจจุบัน</label>
                            <input type="text" class="col-3 form-control" name="interim_payment" value="<?= $rsInspectionPeriod['interim_payment'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="text" class="col-2 form-control" name="interim_payment_percent" value=<?= $rsInspectionPeriod['interim_payment_percent'] ?>>
                            <label class="input-group-text">%</label>
                          </div>
                        </div>

                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment_less_previous" class="col-3 input-group-text">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</label>
                            <input type="text" class="col-3 form-control" name="interim_payment_less_previous" value="<?= $rsInspectionPeriod['interim_payment_less_previous'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="text" class="col-2 form-control" name="interim_payment_less_previous_percent" value=<?= $rsInspectionPeriod['interim_payment_less_previous_percent'] ?>>
                            <label class="input-group-text">%</label>
                          </div>
                        </div>

                        <div class="row m-1">
                          <div class="col-12 input-group input-group-sm">
                            <label for="interim_payment_remain" class="col-3 input-group-text">ยอดเงินงวดคงเหลือ</label>
                            <input type="text" class="col-3 form-control" name="interim_payment_remain" value="<?= $rsInspectionPeriod['interim_payment_remain'] ?>">
                            <label class="input-group-text">บาท</label>
                            <label class="col-2 input-group-text">(Including VAT7%)</label>
                            <!-- </div>
                          <div class="col-2 input-group input-group-sm"> -->
                            <label class="input-group-text">คิดเป็น</label>
                            <input type="text" class="col-2 form-control" name="interim_payment_remain_percent" value=<?= $rsInspectionPeriod['interim_payment_remain_percent'] ?>>
                            <label class="input-group-text">%</label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <hr class="hr border border-dark">

                    <div class="row m-1 mb-3">
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_from" class="input-group-text ">ปริมาณที่ต้องแล้วเสร็จตามแผนงาน</label>
                          <input type="number" class="form-control " name="working_date_from" id="working_date_from" value="<?php echo isset($rsInspectionPeriod['working_date_from']) ? htmlspecialchars($rsInspectionPeriod['working_date_from']) : ''; ?>">
                          <label for="working_date_from" class="input-group-text ">%</label>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_to" class="input-group-text ">ปริมาณที่แล้วเสร็จจริง</label>
                          <input type="number" class="form-control " name="working_date_to" id="working_date_to" value="<?php echo isset($rsInspectionPeriod['working_date_to']) ? htmlspecialchars($rsInspectionPeriod['working_date_to']) : ''; ?>">
                          <label for="working_date_from" class="input-group-text ">%</label>
                        </div>
                      </div>

                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_day" class="input-group-text">ปริมาณงานคงเหลือ</label>
                          <input type="number" class="form-control" name="working_day" id="working_day" value="<?php echo isset($rsInspectionPeriod['working_day']) ? htmlspecialchars($rsInspectionPeriod['working_day']) : ''; ?>" readonly>
                          <label for="working_date_from" class="input-group-text ">%</label>
                        </div>
                      </div>
                    </div>


                  </div>

                </div>

                <div class="card border border-1 border-dark m-1" id="div_open_area_schedule">
                  <h6 class="m-1 fw-bold">รายการงวดงาน</h6>
                  <!-- <div class="card-header" style="display: flex;"> -->
                  <div class="m-1">
                    <a id="btnAdd" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="right" title="เพิ่มงวดงาน">
                      Add Period
                    </a>

                    <a id="btnDeleteLast" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="ลบงวดงานล่าสุด">
                      Delete last period
                    </a>
                    <a id="btnClear" class="btn btn-danger btn-sm d-none" data-toggle="tooltip" data-placement="right" title="ลบงวดงานทั้งหมด">
                      Clear all period
                    </a>
                  </div>

                  <div class="card-body p-0">
                    <!-- สร้าง Table ตามปกติ -->
                    <table class="table table-bordered justify-content-center text-center">
                      <thead>
                        <tr>
                          <th class="p-1" width="5%">งวดงาน</th>
                          <th class="p-1" width="20%">จำนวนเงิน</th>
                          <th class="p-1" width="10%">คิดเป็น(%)</th>
                          <th class="p-1">เงื่อนไขการจ่ายเงิน</th>
                          <th class="p-1" width="5%">Crud</th>
                          <th class="p-1 d-nonex" width="5%">po_period_id</th>
                        </tr>
                      </thead>

                      <tbody id="tableBody">
                        <?php foreach ($rsInspectionPeriod as $row) { ?>


                          <tr class="firstTr" crud="s">
                            <!-- กำหนดลำดับ Auto 1, 2, 3, ... -->
                            <td class="input-group-sm p-0"><input type="number" name="period[]" class="form-control period" value="<?php echo isset($row['period']) ? htmlspecialchars($row['period']) : ''; ?>" readonly>
                            </td>
                            <td class="input-group-sm p-0"><input type="number" name="interim_payment[]" class="form-control interim_payment" value="<?php echo isset($row['interim_payment']) ? htmlspecialchars($row['interim_payment']) : ''; ?>">
                            </td>
                            <td class="input-group-sm p-0"><input type="number" name="interim_payment_percent[]" class="form-control interim_payment_percent" value="<?php echo isset($row['interim_payment_percent']) ? htmlspecialchars($row['interim_payment_percent']) : ''; ?>">
                            </td>
                            <td class="input-group-sm p-0">
                              <input type="text" name="remark[]" class="form-control remark" value="<?php echo isset($row['remark']) ? htmlspecialchars($row['remark']) : ''; ?>">
                            </td>
                            <td class="input-group-sm p-0">
                              <input type="text" name="crud[]" class="form-control crud" value="s">
                            </td>
                            <td class="input-group-sm p-0 d-nonex"><input type="text" name="po_period_id[]" class="form-control po_period_id" value="<?php echo isset($row['po_period_id']) ? htmlspecialchars($row['po_period_id']) : ''; ?>" readonly></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="card-footer p-0 d-flex justify-content-end">
                  <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" value="บันทึก">
                  <!-- <button type="submit" name="btnSave" id="btnSave" class="btn btn-primary btn-sm m-1">บันทึก</button> -->
                  <button type="button" name="btnCancel" id="btnCancel" class="btn btn-secondary btn-sm m-1">ยกเลิก</button>
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
  <script src="javascript/inspection_edit.js"></script>
  <script src="javascript/inspection_edit.js"></script>