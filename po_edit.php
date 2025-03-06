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
    #tablePeriod thead {
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

    $po_id = $_REQUEST['po_id'];

    $po = new Po;
    $rs = $po->getRecordByPoId($po_id);
    $rsPeriod = $po->getPeriodByPoId($po_id);

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
          <h6 class="m-1 fw-bold text-uppercase">Purchase Order</h6>
        </div>
        <!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

              <div class="card">
                <div class="card-header">
                  <h6 class="m-1 fw-bold"><?= $rs['po_number'] . " : " . $rs['supplier_id'] . " - " . $rs['supplier_name'] ?></h6>
                </div>

                <div class="card-body m-0 p-0">
                  <form name="myForm" id="myForm" action="" method="post">
                    <input type="text" class="d-none" name="po_id" id="po_id" value=<?= $po_id ?>>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="po_number" class="input-group-text">เลขที่ PO</label>
                        <input type="text" class="form-control" name="po_number" id="po_number" value=<?= $rs['po_number'] ?> disabled>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="project_name" class="input-group-text">ชื่อโครงการ</label>
                        <input type="text" class="form-control" name="project_name" id="project_name" value="<?= $rs['project_name'] ?>">
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="supplier_id" class="input-group-text">ผู้รับเหมา</label>
                        <select class="form-select form-control" name="supplier_id" id="supplier_id" value=<?= $rs['supplier_id'] ?>>
                          <option value="">...</option>
                          <?php
                          foreach ($supplier_rs as $row) :
                            $selected_attr = ($rs['supplier_id'] == $row['supplier_id']) ? " selected" : "";
                            echo "<option value='{$row['supplier_id']}' {$selected_attr}>{$row['supplier_name']}</option>";
                          // echo "<option value='" . $row['supplier_id'] . "'" . ($rs['supplier_id'] == $row['supplier_id'] ? " selected" : "") . ">" . htmlspecialchars($row['supplier_name']) . "</option>";
                          endforeach ?>
                        </select>
                      </div>

                      <div class="col-3 input-group input-group-sm">
                        <label for="location_id" class="input-group-text">สถานที่</label>
                        <select class="form-select form-control" name="location_id" id="location_id">
                          <option value="">...</option>
                          <?php
                          foreach ($location_rs as $row) :
                            $selected_attr = ($rs['location_id'] == $row['location_id']) ? " selected" : "";
                            echo "<option value='{$row['location_id']}' {$selected_attr}>{$row['location_name']}</option>";
                          endforeach ?>
                        </select>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="working_name_th" class="input-group-text">ชื่องาน(ภาษาไทย)</label>
                        <input type="text" class="form-control" name="working_name_th" id="working_name_th" value="<?= $rs['working_name_th'] ?>">
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="working_name_en" class="input-group-text">ชื่องาน(ภาษาอังกฤษ)</label>
                        <input type="text" class="form-control" name="working_name_en" id="working_name_en" value="<?= $rs['working_name_en'] ?>"">
                      </div>
                    </div>
                    <hr>

                    <div class=" row m-1">
                        <div class="col-4 input-group input-group-sm">
                          <label for="contract_value_before" class="input-group-text">PO ไม่รวม VAT</label>
                          <input type="text" class="form-control" name="contract_value_before" id="contract_value_before" value=<?= $rs['contract_value_before'] ?>>
                        </div>

                        <div class="col-4 input-group input-group-sm">
                          <label for="contract_value" class="input-group-text">PO รวม VAT</label>
                          <input type="number" class="form-control" name="contract_value" id="contract_value" value=<?= $rs['contract_value'] ?>>
                        </div>

                        <div class="col-2 input-group input-group-sm">
                          <label for="vat" class="input-group-text">VAT</label>
                          <input type="text" class="form-control" name="vat" id="vat" value=<?= $rs['vat'] ?>>
                        </div>

                        <div class="col-2 input-group input-group-sm">
                          <div class="form-check">
                            <?php
                            $checked_attr = $rs['is_deposit'] ? "checked" : "";
                            ?>
                            <input class="form-check-input" type="checkbox" name="is_deposit" id="is_deposit" <?= $checked_attr ?>>
                          </div>
                          <label class="form-check-label" for="deposit_percent">เงินมัดจำ</label>
                          <input type="number" class="form-control" name="deposit_percent" id="deposit_percent" value=<?= $rs['deposit_percent'] ?>>%
                        </div>
                      </div>
                      <hr>

                      <div class="row m-1">
                        <div class="col-4">
                          <div class="row-1 input-group input-group-sm">
                            <label for="working_date_from" class="input-group-text ">ระยะเวลาดำเนินการ</label>
                            <input type="date" class="form-control " name="working_date_from" id="working_date_from" value="<?php echo isset($rs['working_date_from']) ? htmlspecialchars($rs['working_date_from']) : ''; ?>">

                          </div>
                        </div>
                        <div class="col-4">
                          <div class="row-1 input-group input-group-sm">
                            <label for="working_date_to" class="input-group-text "> ถึง </label>
                            <input type="date" class="form-control " name="working_date_to" id="working_date_to" value="<?php echo isset($rs['working_date_to']) ? htmlspecialchars($rs['working_date_to']) : ''; ?>">
                          </div>
                        </div>

                        <div class="col-2 input-group input-group-sm">
                          <label for="working_day" class="input-group-text">รวม</label>
                          <input type="number" class="form-control" name="working_day" id="working_day" value="<?php echo isset($rs['working_day']) ? htmlspecialchars($rs['working_day']) : ''; ?>" readonly>
                        </div>
                      </div>

                      <hr>

                      <div class="card border border-1 border-dark m-1">
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
                          <table class="table table-bordered justify-content-center text-center" id="tablePeriod">
                            <thead>
                              <tr>
                                <th class="p-1" width="5%">งวดงาน</th>
                                <th class="p-1" width="20%">จำนวนเงิน</th>
                                <th class="p-1" width="10%">คิดเป็น(%)</th>
                                <th class="p-1">เงื่อนไขการจ่ายเงิน</th>
                                <th class="p-1" width="5%">Crud</th>
                                <th class="p-1 d-nonex" width="5%">period_id</th>
                              </tr>
                            </thead>

                            <tbody id="tbody-period">
                              <?php foreach ($rsPeriod as $row) { ?>
                                <tr class="firstTr">
                                  <!-- กำหนดลำดับ Auto 1, 2, 3, ... -->
                                  <td class="input-group-sm p-0"><input type="number" name="period_numbers[]" class="form-control period_number" value="<?php echo isset($row['period_number']) ? htmlspecialchars($row['period_number']) : ''; ?>" readonly>
                                  </td>
                                  <td class="input-group-sm p-0"><input type="number" name="interim_payments[]" class="form-control interim_payment" value="<?php echo isset($row['interim_payment']) ? htmlspecialchars($row['interim_payment']) : ''; ?>">
                                  </td>
                                  <td class="input-group-sm p-0"><input type="number" name="interim_payment_percents[]" class="form-control interim_payment_percent" value="<?php echo isset($row['interim_payment_percent']) ? htmlspecialchars($row['interim_payment_percent']) : ''; ?>">
                                  </td>
                                  <td class="input-group-sm p-0">
                                    <input type="text" name="remarks[]" class="form-control remark" value="<?php echo isset($row['remark']) ? htmlspecialchars($row['remark']) : ''; ?>">
                                  </td>
                                  <td class="input-group-sm p-0">
                                    <input type="text" name="cruds[]" class="form-control crud" value="s">
                                  </td>
                                  <td class="input-group-sm p-0 d-nonex"><input type="text" name="period_ids[]" class="form-control period_id" value="<?php echo isset($row['period_id']) ? htmlspecialchars($row['period_id']) : ''; ?>" readonly></td>
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
    <script src="javascript/po_edit.js"></script>