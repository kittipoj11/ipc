<?php
@session_start();

require_once 'config.php';
require_once 'auth.php';
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';

// เพิ่ม Font ให้กับ mPDF
$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'thsarabun', // หรือชื่อฟอนต์ที่คุณต้องการ
    'autoScriptToLang' => true,
    'autoLangToFont' => true
]);

ob_start(); // Start get HTML code
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
    :root {
      --my-gold-color: gold;
      --my-primary-color: var(--bs-primary);
      /* สีเทา */
      --my-greenyellow-color: greenyellow;
      /* สีเหลือง */
      --my-text-color: #333;
      /* สีดำ */
      --my-background-color: #f8f9fa;
      /* สีเทาอ่อน */
    }

    table tr th {
      cursor: default;
    }

    /* .dropdown-menu {
      width: fit-content;
    } */
    .dropdown-item:hover {
      /* background-color: var(--bs-primary); */
      background-color: var(--my-primary-color);
      color: white;
      /* เพิ่มสีข้อความให้เป็นสีขาวเพื่อให้ตัดกับพื้นหลัง */
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f9f9f9;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      background-color: #fff;
      padding: 30px;
      border: 1px solid #ddd;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1.title {
      text-align: start;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .header-info,
    .payment-details,
    .footer-info {
      margin-bottom: 20px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .info-label {
      font-weight: bold;
      width: 200px;
    }

    .info-value {
      flex-grow: 1;
    }

    .line {
      border-bottom: 1px solid #000;
      margin-top: 5px;
    }

    .payment-box {
      border: 1px solid #000;
      padding: 10px;
      margin-bottom: 20px;
    }

    .payment-box h2 {
      font-size: 18px;
      margin: 0 0 10px 0;
    }

    .payment-details .item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .payment-details .item .label {
      flex-grow: 1;
    }

    .payment-details .item .value {
      width: 150px;
      text-align: right;
    }

    .net-amount {
      font-weight: bold;
      font-size: 18px;
      margin-top: 10px;
    }

    .signatures {
      display: flex;
      justify-content: space-between;
      margin-top: 50px;
      padding-bottom: 30px;
      border-bottom: 1px solid #ccc;
    }

    .signature-block {
      text-align: center;
    }

    .signature-line {
      border-bottom: 1px solid #000;
      width: 250px;
      margin: 0 auto 5px auto;
    }

    .signature-name {
      font-weight: bold;
    }

    .signature-title {
      font-size: 14px;
    }

    .company-footer {
      margin-top: 20px;
      font-size: 12px;
      text-align: right;
      border-top: 1px solid #ccc;
      padding-top: 10px;
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
    require_once  'class/ipc_class.php';
    require_once  'class/plan_status_class.php';

    // $_SESSION['Request'] = $_REQUEST;
    $connection = new Connection;
    $pdo = $connection->getDbConnection();

    // $poId = $_REQUEST['po_id'];
    // $periodId = $_REQUEST['period_id'];
    // $inspectionId = $_REQUEST['inspection_id'];
    $ipcId = $_REQUEST['ipc_id'];

    $ipc = new Ipc($pdo);
    $rsIpc = $ipc->getIpcByIpcId($ipcId);

    $rsCurrentApprovalType  = $ipc->getCurrentApprovalType($ipcId);

    $plan_status = new Plan_status($pdo);
    $rsPlanStatus = $plan_status->getAll();

    // 
    // ถ้า approval_level ตรงกับ current_approval_level และ approver_id ตรงกับ user_id ที่ login
    // 
    $content_header = 'IPC';
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-12  d-flex justify-content-between">
          <h6 class="m-1 fw-bold text-uppercase"><?= $content_header ?></h6>
        </div>
      </section>
      <!-- /.container-fluid content-header-->

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <form name="myForm" id="myForm" action="" method="post"
                data-user-id="<?= $_SESSION['user_id'] ?>"
                data-ipc-id="<?= $rsIpc['ipc']['ipc_id'] ?>"
                data-ipc-status="<?= $rsIpc['ipc']['ipc_status'] ?>"
                data-workflow-id="<?= $rsIpc['ipc']['workflow_id'] ?>"
                data-created-by="<?= $rsIpc['ipc']['created_by'] ?>"
                data-current-approver-id="<?= $rsIpc['ipc']['current_approver_id'] ?>"
                data-current-approval-level="<?= $rsIpc['ipc']['current_approval_level'] ?>">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <div class="col d-flex align-items-center">
                      <h6 class="m-1 fw-bold"><?= $rsIpc['pomain']['po_number'] . " : " . $rsIpc['pomain']['supplier_id'] . " - " . $rsIpc['pomain']['supplier_name'] ?></h6>
                      <h6 class="m-1 fw-bold"><?= "[งวดงานที่ " . $rsIpc['ipc']['period_number'] . "]" ?></h6>
                    </div>

                    <!-- <div class="dropdown"> -->
                    <div class="btn-group" role="group">
                      <button id="btnAction" class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Action
                      </button>

                      <ul class="dropdown-menu py-0" id="action_type">
                        <!-- <li><a class="dropdown-item p-1" href="#">Confirm</a></li> -->

                        <li><button class="dropdown-item approve" data-approve-text="<?php echo $rsCurrentApprovalType['approval_type_text'] ?>"><?php echo $rsCurrentApprovalType['approval_type_text'] ?></button></li>
                        <?php if ($rsCurrentApprovalType['current_approval_level'] > 1): ?>
                          <li><button class="dropdown-item reject">reject</button></li>
                        <?php endif; ?>

                      </ul>
                    </div>
                  </div>

                  <div class="card-body m-0 p-0">
                    <div class="container">
                      <h1 class="title">INTERIM CERTIFICATE</h1>

                      <div class="header-info">
                        <div class="info-row">
                          <div class="info-label">DATE</div>
                          <div class="info-value">18<sup>th</sup> May 2023</div>
                        </div>
                        <div class="info-row">
                          <div class="info-label">PROJECT</div>
                          <div class="info-value"><?= $rsIpc['pomain']['project_name'] ?></div>
                        </div>
                        <div class="info-row">
                          <div class="info-label">OWNER</div>
                          <div class="info-value">IMPACT Exhibition Management Co., Ltd.<br>47/569-576, 10th floor, Bangkok Land Building,<br>Popular 3 Road, Banmai Sub-district,<br>Pakkred District, Nonthaburi 11120</div>
                        </div>
                      </div>

                      <hr>

                      <div class="header-info">
                        <div class="info-row">
                          <div class="info-label">AGREEMENT DATE</div>
                          <!-- <div class="info-value">25<sup>th</sup> April 2023 (IMPO23020769-1)</div> -->
                          <div class="info-value"><?= $rsIpc['ipc']['agreement_date'] ?></div>
                        </div>
                        <div class="info-row">
                          <div class="info-label">CONTRACTOR</div>
                          <div class="info-value"><?= $rsIpc['ipc']['contractor'] ?></div>
                        </div>
                        <div class="info-row">
                          <div class="info-label">CONTRACT VALUE</div>
                          <div class="info-value">(Including Vat 7%)</div>
                          <div class="info-value" style="text-align: right; font-weight: bold;"><?= number_format($rsIpc['ipc']['contract_value'], 2) ?></div>
                        </div>
                      </div>

                      <div class="payment-box">
                        <h2>INTERIM PAYMENT CLAIM No.1</h2>
                      </div>

                      <div class="payment-details">
                        <div class="item">
                          <div class="label">Total Value Of Interim Payment</div>
                          <div class="value"><?= number_format($rsIpc['ipc']['total_value_of_interim_payment'], 2) ?></div>
                        </div>
                        <div class="item">
                          <div class="label">Less Previous Interim Payment</div>
                          <div class="value"><?= number_format($rsIpc['ipc']['less_previous_interim_payment'], 2) ?></div>
                        </div>
                        <div class="item">
                          <div class="label">Net Value of Current Claim</div>
                          <div class="value"><?= number_format($rsIpc['ipc']['net_value_of_current_claim'], 2) ?></div>
                        </div>
                        <div class="item">
                          <div class="label">Less Retention 5% (Exclu. VAT)</div>
                          <div class="value"><?= number_format($rsIpc['ipc']['less_retension_exclude_vat'], 2) ?></div>
                        </div>
                      </div>

                      <div class="net-amount">
                        <div class="info-row">
                          <div class="info-label">NET AMOUNT DUE FOR PAYMENT No.1</div>
                          <div class="info-value" style="text-align: right; font-weight: bold;"><?= number_format($rsIpc['ipc']['net_amount_due_for_payment'], 2) ?></div>
                        </div>
                      </div>

                      <div class="payment-details">
                        <div class="item">
                          <div class="label">Total Value of Retention (Inclu. this certificate)</div>
                          <div class="value"><?= number_format($rsIpc['ipc']['total_value_of_retention'], 2) ?></div>
                        </div>
                        <div class="item">
                          <div class="label">Total Value of Certification made (Inclu. this certificate)</div>
                          <div class="value"><?= number_format($rsIpc['ipc']['total_value_of_certification_made'], 2) ?></div>
                        </div>
                        <div class="item">
                          <div class="label">Resulting Balance of Contract Sum Outstanding</div>
                          <div class="value"><?= number_format($rsIpc['ipc']['resulting_balance_of_contract_sum_outstanding'], 2) ?></div>
                        </div>
                      </div>

                      <div class="signatures">
                        <div class="signature-block">
                          <div>By : <span class="signature-line"></span></div>
                          <div class="signature-name">( Watchara Chanthrasopa )</div>
                          <div class="signature-title">Head of Project Management Department</div>
                        </div>
                        <div class="signature-block">
                          <div>By : <span class="signature-line"></span></div>
                          <div class="signature-name">( Tanawat Worasakdinan )</div>
                          <div class="signature-title">Cost control Manager</div>
                          <div style="margin-top: 30px;">By : <span class="signature-line"></span></div>
                          <div class="signature-name">( Apichaya Sindhuprama )</div>
                          <div class="signature-title">Project Manager</div>
                        </div>
                      </div>

                      <div class="company-footer">
                        IMPACT EXHIBITION MANAGEMENT CO., LTD.<br>
                        10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,<br>
                        Banmai Sub-district, Pakkred District, Nonthaburi 11120<br>
                        GREATER BANGKOK, THAILAND
                      </div>

                    </div>

                  </div>
                  <!-- /.card-body -->

                  <div class="container-fluid  p-0 d-flex justify-content-between">
                    <button type="button" name="btnCancel" class="btn btn-primary btn-sm m-1 btnCancel"> <i class="fi fi-rr-left"></i> </button>
                    <div>
                      <button type="button" name="btnCancel" class="btn btn-warning btn-sm m-1 btnCancel">Close</button>
                    </div>
                  </div>

                </div>
                <!-- /.card -->

              </form>
              <!-- /.myForm -->

            </div>
          </div>
        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- Main Content -->
    </div>
    <!-- /.Content Wrapper -->


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
    <script src="javascript/ipc_form.js"></script>

