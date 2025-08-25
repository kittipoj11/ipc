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
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 40px;
            background-color: #fff;
            color: #000;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .header .logo {
            width: 150px;
            margin-right: 20px;
        }
        .header .logo img {
            max-width: 100%;
            height: auto;
        }
        .header .title-block {
            flex-grow: 1;
        }
        .header .title-block h1 {
            font-size: 24px;
            margin: 0;
        }
        .header .title-block h2 {
            font-size: 18px;
            font-weight: normal;
            margin: 5px 0 0 0;
        }
        .header .refs {
            text-align: right;
            font-size: 14px;
        }
        .header .refs p {
            margin: 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
        }
        .content {
            margin-top: 30px;
        }
        .content .salutation {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .content .body-text {
            font-size: 14px;
        }
        .body-text p {
            margin: 0 0 10px 0;
        }
        .body-text ol {
            padding-left: 20px;
            list-style: none;
            counter-reset: my-awesome-counter;
        }
        .body-text ol li {
            counter-increment: my-awesome-counter;
            position: relative;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .body-text ol li::before {
            content: counter(my-awesome-counter) ".";
            position: absolute;
            left: -20px;
            font-weight: bold;
        }
        .signature-block {
            margin-top: 50px;
            display: flex;
            align-items: flex-start;
            flex-direction: column;
        }
        .signature-block .sig-line {
            width: 200px;
            border-bottom: 1px solid #000;
            margin-left: 50px;
            margin-right: 10px;
        }
        .signature-details {
            text-align: left;
            margin-top: 10px;
        }
        .signature-details p {
            margin: 0;
            font-size: 14px;
        }
        .footer {
            border-top: 1px solid #ccc;
            padding-top: 10px;
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .footer .left-footer {
            text-align: left;
        }
        .footer .right-footer {
            text-align: right;
        }
        .footer p {
            margin: 0;
        }
        .small-text {
            font-size: 10px;
        }
        .top-right-note {
            position: absolute;
            top: 40px;
            right: 40px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: red;
            font-weight: bold;
            transform: rotate(10deg);
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
        <div class="header">
            <div class="logo">
                <img src="https://i.imgur.com/your-impact-logo.png" alt="IMPACT MUANG THONG THANI Logo">
            </div>
        </div>
        <div class="header">
            <div class="title-block">
                <h1>PROJECT MANAGEMENT DEPARTMENT</h1>
            </div>
            <div class="refs">
                <p>Ref. : Winstar corp_Lord Indra Riding on Erawan Elephant/04/66</p>
                <p>Ref. : Winstar corp_Lord Indra Riding on Erawan Elephant/05/66</p>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">DATE :</div>
            <div class="info-value">07<sup>th</sup> November 2023</div>
        </div>

        <div class="info-row">
            <div class="info-label">Financial and Accounting Dept.</div>
            <div class="info-value"></div>
        </div>
        <div class="info-row">
            <div class="info-label">IMPACT Exhibition Management Co., Ltd.</div>
            <div class="info-value"></div>
        </div>
        <div class="info-row">
            <div class="info-label">47/569-576, 10th floor, Bangkok Land Building,</div>
            <div class="info-value"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Popular 3 Road, Banmai Sub-district,</div>
            <div class="info-value"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Pakkred District, Nonthaburi 11120</div>
            <div class="info-value"></div>
        </div>
        
        <br>
        <p style="text-align: center; font-weight: bold; font-size: 16px;">STRICTLY CONFIDENTIAL</p>
        <br>

        <div class="content">
            <div class="salutation">
                Dear Sir,
                <br><br>
                Impact Exhibition Management Co., Ltd.
            </div>

            <div class="body-text">
                <ol>
                    <li>The Project management Department of IMPACT Exhibition Management Company Limited is in charge of the management of the construction and development works in respect of all project at IMPACT Exhibition & Convention Center Muang Thong Thani, Chaeng wattana, Nonthaburi Province.</li>
                    <li>In accordance with the terms of the Agreement between IMPACT Exhibition Management Co.,Ltd. and Winstar corp Co.,Ltd., IMPACT is obliged to make interim payment for the executed work.</li>
                    <li>Interim payment for <?= $rsIpc['pomain']['project_name'] ?> is now due and we attach to this letter certificate certifying that Such payment is due.</li>
                </ol>
            </div>
        </div>

        <div class="signature-block">
            <p>By : </p>
            <div class="sig-line"></div>
            <div class="signature-details">
                <p>( Kunwadee Jintavorn )</p>
                <p>Executive Director</p>
            </div>
        </div>

        <div class="footer">
            <div class="left-footer">
                <p>IMPACT ARENA</p>
                <p>IMPACT FORUM</p>
                <p>IMPACT CHALLENGER</p>
                <p>IMPACT EXHIBITION CENTER</p>
                <p>NOVOTEL BANGKOK IMPACT</p>
            </div>
            <div class="right-footer">
                <p>IMPACT EXHIBITION MANAGEMENT CO., LTD.</p>
                <p>10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,</p>
                <p>Banmai Sub-district, Pakkred District, Nonthaburi 11120</p>
                <p>GREATER BANGKOK, THAILAND</p>
                <p>Tel : <span class="small-text">+66(0) 2833-4455</span> Fax : <span class="small-text">+66(0) 2833-4456</span></p>
                <p>E-mail : <span class="small-text">info@impact.co.th</span> Website : <span class="small-text">www.impact.co.th</span></p>
            </div>
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

