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
    require_once  'class/po_class.php';
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';


    $connection = new Connection;
    $pdo = $connection->getDbConnection();
    // $action = $_REQUEST['action'];

    if ($_REQUEST['action'] == 'create') {
      $card_header_display = 'd-none';
      $disabled = '';
      $content_header = 'Create Purchase Order';
    } elseif ($_REQUEST['action'] == 'update') {
      $card_header_display = 'd-inline';
      $disabled = 'disabled';
      // $po_id = $_REQUEST['po_id'];
      $content_header = 'Edit Purchase Order';

      $po = new Po($pdo);
      $rsPoMain = $po->fetchByPoId($_REQUEST['po_id']);
      $rsPoPeriod = $po->fetchAllPeriodByPoId($_REQUEST['po_id']);
    }

    $supplier = new Supplier($pdo);
    $supplier_rs = $supplier->fetchAll();

    $location = new Location($pdo);
    $location_rs = $location->fetchAll();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-6 d-flex">
          <h6 class="m-1 fw-bold text-uppercase"><?= $content_header ?></h6>
        </div>
        <!-- /.container-fluid -->
        <div id="response-message">x</div>
      </section>

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

              <div class="card">
                <div class="card-header <?= $card_header_display ?>">
                  <h6 class="m-1 fw-bold"><?= (isset($rsPoMain['po_number']) ? $rsPoMain['po_number'] : '') . " : " . (isset($rsPoMain['supplier_id']) ? $rsPoMain['supplier_id'] : '') . " - " . (isset($rsPoMain['supplier_name']) ? $rsPoMain['supplier_name'] : '') ?></h6>
                </div>

                <div class="card-body m-0 p-0">
                  <form name="myForm" id="myForm" action="" method="post">
                    <input type="text" class="d-nonex" name="po_id" id="po_id" value=<?= (isset($_REQUEST['po_id']) ? $_REQUEST['po_id'] : '') ?>>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="po_number" class="input-group-text">เลขที่ PO</label>
                        <input type="text" class="form-control" name="po_number" id="po_number" value="<?= (isset($rsPoMain['po_number']) ? $rsPoMain['po_number'] : '') ?>" <?= $disabled ?>>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="project_name" class="input-group-text">ชื่อโครงการ</label>
                        <input type="text" class="form-control" name="project_name" id="project_name" value="<?= (isset($rsPoMain['project_name']) ? $rsPoMain['project_name'] : '') ?>">
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="supplier_id" class="input-group-text">ผู้รับเหมา</label>
                        <select class="form-select form-control" name="supplier_id" id="supplier_id">
                          <option value="">...</option>
                          <?php
                          foreach ($supplier_rs as $row) :
                            $selected_attr = ((isset($rsPoMain['supplier_id']) ? $rsPoMain['supplier_id'] : '') == $row['supplier_id']) ? " selected" : "";
                            echo "<option value='{$row['supplier_id']}' {$selected_attr}>{$row['supplier_name']}</option>";
                          endforeach ?>
                        </select>
                      </div>

                      <div class="col-3 input-group input-group-sm">
                        <label for="location_id" class="input-group-text">สถานที่</label>
                        <select class="form-select form-control" name="location_id" id="location_id">
                          <option value="">...</option>
                          <?php
                          foreach ($location_rs as $row) :
                            $selected_attr = ((isset($rsPoMain['location_id']) ? $rsPoMain['location_id'] : '') == $row['location_id']) ? " selected" : "";
                            // $selected_attr = ($rsPoMain['location_id'] == $row['location_id']) ? " selected" : "";
                            echo "<option value='{$row['location_id']}' {$selected_attr}>{$row['location_name']}</option>";
                          endforeach ?>
                        </select>
                      </div>
                    </div>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="working_name_th" class="input-group-text">ชื่องาน(ภาษาไทย)</label>
                        <input type="text" class="form-control" name="working_name_th" id="working_name_th" value="<?= (isset($rsPoMain['working_name_th']) ? $rsPoMain['working_name_th'] : '') ?>">
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="working_name_en" class="input-group-text">ชื่องาน(ภาษาอังกฤษ)</label>
                        <input type="text" class="form-control" name="working_name_en" id="working_name_en" value="<?= (isset($rsPoMain['working_name_en']) ? $rsPoMain['working_name_en'] : '') ?>">
                      </div>
                    </div>
                    <hr>

                    <div class="row m-1">
                      <div class="col-4 input-group input-group-sm">
                        <label for="contract_value_before" class="input-group-text">PO ไม่รวม VAT</label>
                        <input type="number" step="0.01" class="form-control" name="contract_value_before" id="contract_value_before" value="<?= (isset($rsPoMain['contract_value_before']) ? $rsPoMain['contract_value_before'] : '') ?>">
                      </div>

                      <div class="col-4 input-group input-group-sm">
                        <label for="contract_value" class="input-group-text">PO รวม VAT</label>
                        <input type="number" step="0.01" class="form-control" name="contract_value" id="contract_value" value="<?= (isset($rsPoMain['contract_value']) ? $rsPoMain['contract_value'] : '') ?>">
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <label for="vat" class="input-group-text">VAT</label>
                        <input type="number" step="0.01" class="form-control" name="vat" id="vat" value="<?= (isset($rsPoMain['vat']) ? $rsPoMain['vat'] : '') ?>" data-vat_rate=<?= VAT_RATE ?> readonly>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <div class="form-check">
                          <?php
                          $checked_attr = isset($rsPoMain['deposit_percent']) ? ($rsPoMain['is_deposit'] ? "checked" : "") : "";
                          ?>
                          <input class="form-check-input" type="checkbox" name="is_deposit" id="is_deposit" <?= $checked_attr ?>>
                        </div>
                        <label class="form-check-label" for="deposit_percent">เงินมัดจำ</label>
                        <input type="number" step="0.01" class="form-control" name="deposit_percent" id="deposit_percent" value="<?= (isset($rsPoMain['deposit_percent']) ? $rsPoMain['deposit_percent'] : '') ?>">%
                      </div>
                    </div>
                    <hr>

                    <div class="row m-1">
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_from" class="input-group-text ">ระยะเวลาดำเนินการ</label>
                          <input type="date" class="form-control " name="working_date_from" id="working_date_from" value="<?= (isset($rsPoMain['working_date_from']) ? htmlspecialchars($rsPoMain['working_date_from']) : '') ?>">
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="row-1 input-group input-group-sm">
                          <label for="working_date_to" class="input-group-text "> ถึง </label>
                          <input type="date" class="form-control " name="working_date_to" id="working_date_to" value="<?= (isset($rsPoMain['working_date_to']) ? htmlspecialchars($rsPoMain['working_date_to']) : '') ?>">
                        </div>
                      </div>

                      <div class="col-2 input-group input-group-sm">
                        <label for="working_day" class="input-group-text">รวม</label>
                        <input type="number" class="form-control" name="working_day" id="working_day" value="<?= (isset($rsPoMain['working_day']) ? $rsPoMain['working_day'] : '') ?>" readonly>
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
                        <a id="btnClear" class="btn btn-danger btn-sm <?= ($_REQUEST['action'] == 'create' ? 'd-inline' : 'd-none') ?>" data-toggle="tooltip" data-placement="right" title="ลบงวดงานทั้งหมด">
                          Clear all period
                        </a>
                      </div>

                      <div class="card-body p-0">
                        <!-- สร้าง Table ตามปกติ -->
                        <table class="table table-bordered justify-content-center text-center">
                          <thead>
                            <tr>
                              <th class="p-1" width="5%">งวดงาน</th>
                              <th class="p-1" width="15%">งานที่แล้วเสร็จตามแผน(%)</th>
                              <th class="p-1" width="15%">จำนวนเงิน</th>
                              <th class="p-1" width="10%">คิดเป็น(%)</th>
                              <th class="p-1">เงื่อนไขการจ่ายเงิน</th>
                              <th class="p-1 d-none" width="5%">Action</th>
                              <th class="p-1" width="5%">Crud</th>
                            </tr>
                          </thead>
                          <tbody id="tbody-period">
                            <?php if ($_REQUEST['action'] == 'create') {
                            } elseif ($_REQUEST['action'] == 'update') {
                              foreach ($rsPoPeriod as $row) { ?>
                                <tr data-crud='select' data-period-id=<?php echo isset($row['period_id']) ? htmlspecialchars($row['period_id']) : ''; ?>>
                                  <!-- กำหนดลำดับ Auto 1, 2, 3, ... -->
                                  <td class="input-group-sm p-0"><input type="number" name="period_number" class="form-control period_number" value="<?php echo isset($row['period_number']) ? htmlspecialchars($row['period_number']) : ''; ?>" readonly></td>
                                  <td class="input-group-sm p-0"><input type="number" name="workload_planned_percent" step="0.01" class="form-control workload_planned_percent" value="<?php echo isset($row['workload_planned_percent']) ? htmlspecialchars($row['workload_planned_percent']) : '0'; ?>"></td>
                                  <td class="input-group-sm p-0"><input type="number" name="interim_payment" class="form-control interim_payment" value="<?php echo isset($row['interim_payment']) ? htmlspecialchars($row['interim_payment']) : '0'; ?>"></td>
                                  <td class="input-group-sm p-0"><input type="number" name="interim_payment_percent" step="0.01" class="form-control interim_payment_percent" value="<?php echo isset($row['interim_payment_percent']) ? htmlspecialchars($row['interim_payment_percent']) : '0'; ?>"></td>
                                  <td class="input-group-sm p-0"><input type="text" name="remark" class="form-control remark" value="<?php echo isset($row['remark']) ? htmlspecialchars($row['remark']) : ''; ?>"></td>
                                  <td class="input-group-sm p-0"><input type="text" name="crud" class="form-control crud" value="select"></td>
                                </tr>
                            <?php }
                            } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- <div class="card-footer p-0 d-flex justify-content-end">
                      <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" value="บันทึก">
                      <button type="button" name="btnCancel" id="btnCancel" class="btn btn-secondary btn-sm m-1">ยกเลิก</button>
                    </div> -->

                    <div class="container-fluid  p-0 d-flex justify-content-between">
                      <button type="button" name="btnBack" class="btn btn-primary btn-sm m-1 btnBack"> <i class="fi fi-rr-left"></i> </button>
                      <div>
                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" data-action="<?= $_REQUEST['action'] ?>" value="บันทึก" data-current_approval_level="<?= $rsInspectionPeriod['current_approval_level'] ?>">
                        <button type="button" name="btnCancel" class="btn btn-warning btn-sm m-1 btnCancel">ยกเลิก</button>
                      </div>
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
    <script src="javascript/po_dml.js"></script>