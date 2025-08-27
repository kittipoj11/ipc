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
      text-align: center;
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

    /* .info-label {
      font-weight: bold;
      width: 200px;
    } */

    /* .info-value {
      flex-grow: 1;
    } */

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

    /* .payment-details .item .label {
      flex-grow: 1;
    } */

    /* .payment-details .item .value {
      width: 150px;
      text-align: right;
    } */

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

    .signature- {
      font-size: 14px;
    }

    .company-footer {
      margin-top: 20px;
      font-size: 12px;
      text-align: right;
      border-top: 1px solid #ccc;
      padding-top: 10px;

      .status-box {
        text-align: center;
        padding: 15px;
        margin: 15px 0;
        border-radius: 5px;
        font-weight: bold;
      }
    }

    .status {
      font-weight: bold;
      padding: 5px 10px;
      border-radius: 15px;
      color: white;
      font-size: 0.9em;
    }

    .status-draft {
      background-color: #6c757d;
    }

    .status-pending-submit {
      background-color: #ffc107;
      color: #000;
    }

    .status-rejected {
      background-color: #dc3545;
    }

    .status-completed {
      background-color: #28a745;
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
    $content_header = 'Interim Payment Claim';
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="container-fluid content-header">
        <div class="col-sm-12  d-flex justify-content-between">
          <h6 class="m-1 fw-bold text-uppercase"><?= $content_header ?></h6>
        </div>
      </section>
      <!-- /.container-fluid content-header -->

      <!-- Main content -->
      <section>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <form name="myForm" id="myForm" action="" method="post"
                data-user-id="<?= $_SESSION['user_id'] ?>"
                data-ipc-id="<?= $rsIpc['ipc']['ipc_id'] ?>"
                data-inspection-id="<?= $rsIpc['ipc']['inspection_id'] ?>"
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
                      <h6 class="m-1 fw-bold">
                        <div class="status status-pending-submit"><?php echo htmlspecialchars($rsIpc['ipc']['ipc_status']); ?></div>
                      </h6>
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
                    <div class="container-fluid">
                      <!-- Content Area -->
                      <div id="content" class="border p-3 rounded mb-4">
                        <h3 class="">INTERIM CERTIFICATE</h3>
                        <div class="header-info">
                          <!-- <div class="info-row">
                          <div class="fw-bold" style="width: 200px;">DATE</div>
                          <div class="flex-grow-1">18<sup>th</sup> May 2023</div>
                        </div> -->
                          <div class="info-row">
                            <div class="fw-bold" style="width: 200px;">PROJECT</div>
                            <div class="flex-grow-1"><?= $rsIpc['pomain']['project_name'] ?></div>
                          </div>
                          <div class="info-row">
                            <div class="fw-bold" style="width: 200px;">OWNER</div>
                            <div class="flex-grow-1">IMPACT Exhibition Management Co., Ltd.<br>47/569-576, 10th floor, Bangkok Land Building,<br>Popular 3 Road, Banmai Sub-district,<br>Pakkred District, Nonthaburi 11120</div>
                          </div>
                        </div>

                        <hr>

                        <div class="header-info">
                          <div class="info-row">
                            <div class="fw-bold" style="width: 200px;">AGREEMENT DATE</div>
                            <!-- <div class="flex-grow-1">25<sup>th</sup> April 2023 (IMPO23020769-1)</div> -->
                            <div class="flex-grow-1"><?= $rsIpc['ipc']['agreement_date'] ?></div>
                          </div>
                          <div class="info-row">
                            <div class="fw-bold" style="width: 200px;">CONTRACTOR</div>
                            <div class="flex-grow-1"><?= $rsIpc['ipc']['contractor'] ?></div>
                          </div>
                          <div class="info-row">
                            <div class="fw-bold" style="width: 200px;">CONTRACT VALUE</div>
                            <div class="flex-grow-1">(Including Vat 7%)</div>
                            <div class="flex-grow-1" style="text-align: right; font-weight: bold;"><?= number_format($rsIpc['ipc']['contract_value'], 2) ?></div>
                          </div>
                        </div>

                        <div class="payment-boxx">
                          <h3>INTERIM PAYMENT CLAIM No.1</h3>
                        </div>

                        <div class="payment-details">
                          <div class="item">
                            <div class="flex-grow-1">Total Value Of Interim Payment</div>
                            <div class="text-end" style="width: 150px;"><?= number_format($rsIpc['ipc']['total_value_of_interim_payment'], 2) ?></div>
                          </div>
                          <div class="item">
                            <div class="flex-grow-1">Less Previous Interim Payment</div>
                            <div class="text-end" style="width: 150px;"><?= number_format($rsIpc['ipc']['less_previous_interim_payment'], 2) ?></div>
                          </div>
                          <div class="item">
                            <div class="flex-grow-1">Net Value of Current Claim</div>
                            <div class="text-end" style="width: 150px;"><?= number_format($rsIpc['ipc']['net_value_of_current_claim'], 2) ?></div>
                          </div>
                          <div class="item">
                            <div class="flex-grow-1">Less Retention 5% (Exclu. VAT)</div>
                            <div class="text-end" style="width: 150px;"><?= number_format($rsIpc['ipc']['less_retension_exclude_vat'], 2) ?></div>
                          </div>
                        </div>

                        <div class="d-flex justify-content-between fw-bold" style="font-size: 18px;">
                          <div class="">NET AMOUNT DUE FOR PAYMENT No.1</div>
                          <div class="text-end"><?= number_format($rsIpc['ipc']['net_amount_due_for_payment'], 2) ?></div>
                        </div>

                        <div class="payment-details">
                          <div class="item">
                            <div class="flex-grow-1">Total Value of Retention (Inclu. this certificate)</div>
                            <div class="text-end" style="width: 150px;"><?= number_format($rsIpc['ipc']['total_value_of_retention'], 2) ?></div>
                          </div>
                          <div class="item">
                            <div class="flex-grow-1">Total Value of Certification made (Inclu. this certificate)</div>
                            <div class="text-end" style="width: 150px;"><?= number_format($rsIpc['ipc']['total_value_of_certification_made'], 2) ?></div>
                          </div>
                          <div class="item">
                            <div class="flex-grow-1">Resulting Balance of Contract Sum Outstanding</div>
                            <div class="text-end" style="width: 150px;"><?= number_format($rsIpc['ipc']['resulting_balance_of_contract_sum_outstanding'], 2) ?></div>
                          </div>
                        </div>

                      </div>


                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                      <ul class="pagination" id="pagination">
                        <li class="page-item disabled"><a class="page-link" href="#" id="prevBtn">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#" data-page="1">1</a></li>
                        <li class="page-item"><a class="page-link" href="#" data-page="2">2</a></li>
                        <li class="page-item"><a class="page-link" href="#" data-page="3">3</a></li>
                        <li class="page-item"><a class="page-link" href="#" id="nextBtn">Next</a></li>
                      </ul>
                    </nav>
                  </div>
                  <!-- /.card-body -->
                  <hr>
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
    <!-- <script type="module" src="javascript/ipc_form.js"></script> -->

    <!-- <script>
      const page1 = `<h4>หน้า 1</h4><p>เนื้อหาของหน้าที่ 1 แสดงอยู่ตรงนี้ในครั้งต่อมา 🎉</p>
    <h4>หน้า 1</h4><p>เนื้อหาของหน้าที่ 1 แสดงอยู่ตรงนี้ในครั้งต่อมา 🎉</p>
    <h4>หน้า 1</h4><p>เนื้อหาของหน้าที่ 1 แสดงอยู่ตรงนี้ในครั้งต่อมา 🎉</p>
    <h4>หน้า 1</h4><p>เนื้อหาของหน้าที่ 1 แสดงอยู่ตรงนี้ในครั้งต่อมา 🎉</p>`;
      const contentData = {
        1: page1,
        2: "<h4>หน้า 2</h4><p>นี่คือเนื้อหาของหน้าที่ 2 😎</p>",
        3: "<h4>หน้า 3</h4><p>และนี่คือหน้าที่ 3 🚀</p>"
      };

      let currentPage = 1;
      const totalPages = Object.keys(contentData).length;

      const contentDiv = document.getElementById("content");
      const paginationItems = document.querySelectorAll(".page-item a[data-page]");
      const prevBtn = document.getElementById("prevBtn").parentElement;
      const nextBtn = document.getElementById("nextBtn").parentElement;

      function renderPage(page) {
        currentPage = page;
        contentDiv.innerHTML = contentData[page];

        // update active class
        paginationItems.forEach(item => {
          item.parentElement.classList.remove("active");
          if (parseInt(item.dataset.page) === page) {
            item.parentElement.classList.add("active");
          }
        });

        // disable prev/next if needed
        prevBtn.classList.toggle("disabled", page === 1);
        nextBtn.classList.toggle("disabled", page === totalPages);
      }

      // add click events
      paginationItems.forEach(item => {
        item.addEventListener("click", (e) => {
          e.preventDefault();
          renderPage(parseInt(item.dataset.page));
        });
      });

      // document.getElementById("prevBtnx").addEventListener("click", (e) => {
      //   e.preventDefault();
      //   if (currentPage > 1) renderPage(currentPage - 1);
      // });

      document.getElementById("nextBtn").addEventListener("click", (e) => {
        e.preventDefault();
        if (currentPage < totalPages) renderPage(currentPage + 1);
      });
    </script> -->