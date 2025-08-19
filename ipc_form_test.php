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
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f0f2f5;
    }

    .tab-buttons {
      text-align: center;
      margin-bottom: 20px;
    }

    .tab-buttons button {
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border: 1px solid #ccc;
      background-color: #e9e9e9;
      transition: background-color 0.3s;
    }

    .tab-buttons button.active {
      background-color: #fff;
      border-bottom: 1px solid #fff;
    }

    .content-container {
      max-width: 800px;
      margin: 0 auto;
      background-color: #fff;
      padding: 30px;
      border: 1px solid #ddd;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    /* Style for Page 1 */
    .page1-container {
      font-family: Arial, sans-serif;
    }

    .page1-title {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .page1-header-info,
    .page1-payment-details,
    .page1-footer-info {
      margin-bottom: 20px;
    }

    .page1-info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .page1-info-label {
      font-weight: bold;
      width: 200px;
    }

    .page1-info-value {
      flex-grow: 1;
    }

    .page1-line {
      border-bottom: 1px solid #000;
      margin-top: 5px;
    }

    .page1-payment-box {
      border: 1px solid #000;
      padding: 10px;
      margin-bottom: 20px;
    }

    .page1-payment-box h2 {
      font-size: 18px;
      margin: 0 0 10px 0;
    }

    .page1-payment-details .item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .page1-payment-details .item .label {
      flex-grow: 1;
    }

    .page1-payment-details .item .value {
      width: 150px;
      text-align: right;
    }

    .page1-net-amount {
      font-weight: bold;
      font-size: 18px;
      margin-top: 10px;
    }

    .page1-signatures {
      display: flex;
      justify-content: space-between;
      margin-top: 50px;
      padding-bottom: 30px;
      border-bottom: 1px solid #ccc;
    }

    .page1-signature-block {
      text-align: center;
    }

    .page1-signature-line {
      border-bottom: 1px solid #000;
      width: 250px;
      margin: 0 auto 5px auto;
    }

    .page1-signature-name {
      font-weight: bold;
    }

    .page1-signature-title {
      font-size: 14px;
    }

    .page1-company-footer {
      margin-top: 20px;
      font-size: 12px;
      text-align: right;
      border-top: 1px solid #ccc;
      padding-top: 10px;
    }

    /* Style for Page 2 */
    .page2-container {
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      line-height: 1.6;
    }

    .page2-header {
      display: flex;
      align-items: flex-start;
      margin-bottom: 20px;
    }

    .page2-header .logo {
      width: 150px;
      margin-right: 20px;
    }

    .page2-header .logo img {
      max-width: 100%;
      height: auto;
    }

    .page2-header .title-block {
      flex-grow: 1;
    }

    .page2-header .title-block h1 {
      font-size: 24px;
      margin: 0;
    }

    .page2-header .title-block h2 {
      font-size: 18px;
      font-weight: normal;
      margin: 5px 0 0 0;
    }

    .page2-header .refs {
      text-align: right;
      font-size: 14px;
    }

    .page2-header .refs p {
      margin: 0;
    }

    .page2-info-row {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .page2-info-label {
      font-weight: bold;
    }

    .page2-content {
      margin-top: 30px;
    }

    .page2-content .salutation {
      margin-bottom: 20px;
      font-size: 14px;
    }

    .page2-content .body-text {
      font-size: 14px;
    }

    .page2-body-text p {
      margin: 0 0 10px 0;
    }

    .page2-body-text ol {
      padding-left: 20px;
      list-style: none;
      counter-reset: my-awesome-counter;
    }

    .page2-body-text ol li {
      counter-increment: my-awesome-counter;
      position: relative;
      margin-bottom: 15px;
      line-height: 1.5;
    }

    .page2-body-text ol li::before {
      content: counter(my-awesome-counter) ".";
      position: absolute;
      left: -20px;
      font-weight: bold;
    }

    .page2-signature-block {
      margin-top: 50px;
      display: flex;
      align-items: flex-end;
    }

    .page2-signature-block .sig-line {
      width: 200px;
      border-bottom: 1px solid #000;
      margin-left: 50px;
      margin-right: 10px;
    }

    .page2-signature-details {
      text-align: left;
      margin-top: 10px;
    }

    .page2-signature-details p {
      margin: 0;
      font-size: 14px;
    }

    .page2-footer {
      border-top: 1px solid #ccc;
      padding-top: 10px;
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
      font-size: 12px;
    }

    .page2-footer .left-footer {
      text-align: left;
    }

    .page2-footer .right-footer {
      text-align: right;
    }

    .page2-footer p {
      margin: 0;
    }

    .page2-small-text {
      font-size: 10px;
    }

    .page2-top-right-note {
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
                    <div class="content-container">
    <div class="tab-buttons">
        <button id="btnPage1" class="active">หน้า 1</button>
        <button id="btnPage2">หน้า 2</button>
    </div>
                      <div id="page1" class="tab-content active">
                        <div class="page1-container">
                          <h1 class="page1-title">INTERIM CERTIFICATE</h1>

                          <div class="page1-header-info">
                            <div class="page1-info-row">
                              <div class="page1-info-label">DATE</div>
                              <div class="page1-info-value">18<sup>th</sup> May 2023</div>
                            </div>
                            <div class="page1-info-row">
                              <div class="page1-info-label">PROJECT</div>
                              <div class="page1-info-value">Material cost and installation of LED decoration lamps (first-fixed) at Statue<br>of Lord Indra Riding on Erawan Elephant project</div>
                            </div>
                            <div class="page1-info-row">
                              <div class="page1-info-label">OWNER</div>
                              <div class="page1-info-value">IMPACT Exhibition Management Co., Ltd.<br>47/569-576, 10th floor, Bangkok Land Building,<br>Popular 3 Road, Banmai Sub-district,<br>Pakkred District, Nonthaburi 11120</div>
                            </div>
                          </div>

                          <hr>

                          <div class="page1-header-info">
                            <div class="page1-info-row">
                              <div class="page1-info-label">AGREEMENT DATE</div>
                              <div class="page1-info-value">25<sup>th</sup> April 2023 (IMPO23020769-1)</div>
                            </div>
                            <div class="page1-info-row">
                              <div class="page1-info-label">CONTRACTOR</div>
                              <div class="page1-info-value">Winstar corp Co.,Ltd.</div>
                            </div>
                            <div class="page1-info-row">
                              <div class="page1-info-label">CONTRACT VALUE</div>
                              <div class="page1-info-value">(Including Vat 7%)</div>
                              <div class="page1-info-value" style="text-align: right; font-weight: bold;">869,161.00</div>
                            </div>
                          </div>

                          <div class="page1-payment-box">
                            <h2>INTERIM PAYMENT CLAIM No.1</h2>
                          </div>

                          <div class="page1-payment-details">
                            <div class="item">
                              <div class="label">Total Value Of Interim Payment</div>
                              <div class="value">132,412.50</div>
                            </div>
                            <div class="item">
                              <div class="label">Less Previous Interim Payment</div>
                              <div class="value">0.00</div>
                            </div>
                            <div class="item">
                              <div class="label">Net Value of Current Claim</div>
                              <div class="value">132,412.50</div>
                            </div>
                            <div class="item">
                              <div class="label">Less Retention 5% (Exclu. VAT)</div>
                              <div class="value">6,187.50</div>
                            </div>
                          </div>

                          <div class="page1-net-amount">
                            <div class="page1-info-row">
                              <div class="page1-info-label">NET AMOUNT DUE FOR PAYMENT No.1</div>
                              <div class="page1-info-value" style="text-align: right; font-weight: bold;">126,225.00</div>
                            </div>
                          </div>

                          <div class="page1-payment-details">
                            <div class="item">
                              <div class="label">Total Value of Retention (Inclu. this certificate)</div>
                              <div class="value">6,187.50</div>
                            </div>
                            <div class="item">
                              <div class="label">Total Value of Certification made (Inclu. this certificate)</div>
                              <div class="value">126,225.00</div>
                            </div>
                            <div class="item">
                              <div class="label">Resulting Balance of Contract Sum Outstanding</div>
                              <div class="value">742,936.00</div>
                            </div>
                          </div>

                          <div class="page1-signatures">
                            <div class="page1-signature-block">
                              <div>By : <span class="page1-signature-line"></span></div>
                              <div class="page1-signature-name">( Watchara Chanthrasopa )</div>
                              <div class="page1-signature-title">Head of Project Management Department</div>
                            </div>
                            <div class="page1-signature-block">
                              <div>By : <span class="page1-signature-line"></span></div>
                              <div class="page1-signature-name">( Tanawat Worasakdinan )</div>
                              <div class="page1-signature-title">Cost control Manager</div>
                              <div style="margin-top: 30px;">By : <span class="page1-signature-line"></span></div>
                              <div class="page1-signature-name">( Apichaya Sindhuprama )</div>
                              <div class="page1-signature-title">Project Manager</div>
                            </div>
                          </div>

                          <div class="page1-company-footer">
                            IMPACT EXHIBITION MANAGEMENT CO., LTD.<br>
                            10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,<br>
                            Banmai Sub-district, Pakkred District, Nonthaburi 11120<br>
                            GREATER BANGKOK, THAILAND
                          </div>
                        </div>
                      </div>

                      <div id="page2" class="tab-content">
                        <div class="page2-container">
                          <div class="page2-top-right-note">
                            2.1/2. (Last Payment) และมีค้างยอด<br>
                            Deduct
                          </div>

                          <div class="page2-header">
                            <div class="logo">
                              <img src="https://i.imgur.com/your-impact-logo.png" alt="IMPACT MUANG THONG THANI Logo">
                            </div>
                          </div>
                          <div class="page2-header">
                            <div class="page2-title-block">
                              <h1>PROJECT MANAGEMENT DEPARTMENT</h1>
                            </div>
                            <div class="page2-refs">
                              <p>Ref. : Winstar corp_Lord Indra Riding on Erawan Elephant/04/66</p>
                              <p>Ref. : Winstar corp_Lord Indra Riding on Erawan Elephant/05/66</p>
                            </div>
                          </div>

                          <div class="page2-info-row">
                            <div class="page2-info-label">DATE :</div>
                            <div class="page2-info-value">07<sup>th</sup> November 2023</div>
                          </div>

                          <div class="page2-info-row">
                            <div class="page2-info-label">Financial and Accounting Dept.</div>
                            <div class="page2-info-value"></div>
                          </div>
                          <div class="page2-info-row">
                            <div class="page2-info-label">IMPACT Exhibition Management Co., Ltd.</div>
                            <div class="page2-info-value"></div>
                          </div>
                          <div class="page2-info-row">
                            <div class="page2-info-label">47/569-576, 10th floor, Bangkok Land Building,</div>
                            <div class="page2-info-value"></div>
                          </div>
                          <div class="page2-info-row">
                            <div class="page2-info-label">Popular 3 Road, Banmai Sub-district,</div>
                            <div class="page2-info-value"></div>
                          </div>
                          <div class="page2-info-row">
                            <div class="page2-info-label">Pakkred District, Nonthaburi 11120</div>
                            <div class="page2-info-value"></div>
                          </div>

                          <br>
                          <p style="text-align: center; font-weight: bold; font-size: 16px;">STRICTLY CONFIDENTIAL</p>
                          <br>

                          <div class="page2-content">
                            <div class="page2-salutation">
                              Dear Sir,
                              <br><br>
                              Impact Exhibition Management Co., Ltd.
                            </div>

                            <div class="page2-body-text">
                              <ol>
                                <li>The Project management Department of IMPACT Exhibition Management Company Limited is in charge of the management of the construction and development works in respect of all project at IMPACT Exhibition & Convention Center Muang Thong Thani, Chaeng wattana, Nonthaburi Province.</li>
                                <li>In accordance with the terms of the Agreement between IMPACT Exhibition Management Co.,Ltd. and Winstar corp Co.,Ltd., IMPACT is obliged to make interim payment for the executed work.</li>
                                <li>Interim payment for **Material cost and installation of LED decoration lamps (first-fixed) at Statue of Lord Indra Riding on Erawan Elephant project No. 2/1, 2/2 (IMPO23020769-1) (Last Payment, Deduct)** is now due and we attach to this letter certificate certifying that Such payment is due.</li>
                              </ol>
                            </div>
                          </div>

                          <div class="page2-signature-block">
                            <p>By : </p>
                            <div class="page2-sig-line"></div>
                            <div class="page2-signature-details">
                              <p>( **Kunwadee Jintavorn** )</p>
                              <p>Executive Director</p>
                            </div>
                          </div>

                          <div class="page2-footer">
                            <div class="page2-left-footer">
                              <p>IMPACT ARENA</p>
                              <p>IMPACT FORUM</p>
                              <p>IMPACT CHALLENGER</p>
                              <p>IMPACT EXHIBITION CENTER</p>
                              <p>NOVOTEL BANGKOK IMPACT</p>
                            </div>
                            <div class="page2-right-footer">
                              <p>IMPACT EXHIBITION MANAGEMENT CO., LTD.</p>
                              <p>10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,</p>
                              <p>Banmai Sub-district, Pakkred District, Nonthaburi 11120</p>
                              <p>GREATER BANGKOK, THAILAND</p>
                              <p>Tel : <span class="page2-small-text">+66(0) 2833-4455</span> Fax : <span class="page2-small-text">+66(0) 2833-4456</span></p>
                              <p>E-mail : <span class="page2-small-text">info@impact.co.th</span> Website : <span class="page2-small-text">www.impact.co.th</span></p>
                            </div>
                          </div>
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
    <!-- <script src="javascript/ipc_form.js"></script> -->
    <script>
      document.getElementById('btnPage1').addEventListener('click', function() {
        document.getElementById('page1').classList.add('active');
        document.getElementById('page2').classList.remove('active');
        document.getElementById('btnPage1').classList.add('active');
        document.getElementById('btnPage2').classList.remove('active');
      });

      document.getElementById('btnPage2').addEventListener('click', function() {
        document.getElementById('page2').classList.add('active');
        document.getElementById('page1').classList.remove('active');
        document.getElementById('btnPage2').classList.add('active');
        document.getElementById('btnPage1').classList.remove('active');
      });
    </script>