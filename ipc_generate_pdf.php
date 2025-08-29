<?php
require_once __DIR__ . '/vendor/autoload.php'; // ติดตั้ง mPDF ด้วย composer

    require_once  'class/connection_class.php';
    require_once  'class/ipc_class.php';

    $connection = new Connection;
    $pdo = $connection->getDbConnection();

    $ipcId = $_REQUEST['ipc_id'];

    $ipc = new Ipc($pdo);
    $rsIpc = $ipc->getIpcByIpcId($ipcId);

$html = '<style>
            table, th, td {
              text-align: center;
              border: 1px solid black;
              border-radius: 5px;
              padding: 2px 0px;
              background-color: #ffffff;
            }
            .fw-bold{
              font-weight: bold;
            }
        </style>';
$html .= '
          <div class="container-fluid">
            <h3 class="">INTERIM CERTIFICATE</h3>

            <div class="header-info">
              <!-- <div class="info-row">
                <div class="fw-bold" style="width: 200px;">DATE</div>
                <div class="flex-grow-1">18<sup>th</sup> May 2023</div>
              </div> -->
              <div class="info-row">
                <div class="fw-bold" style="width: 200px;">PROJECT</div>
                <div class="flex-grow-1">'. $rsIpc['pomain']['project_name'] .'</div>
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
                <div class="flex-grow-1">'. $rsIpc['ipc']['agreement_date'] .'</div>
              </div>
              <div class="info-row">
                <div class="fw-bold" style="width: 200px;">CONTRACTOR</div>
                <div class="flex-grow-1">'. $rsIpc['ipc']['contractor'] .'</div>
              </div>
              <div class="info-row">
                <div class="fw-bold" style="width: 200px;">CONTRACT VALUE</div>
                <div class="flex-grow-1">(Including Vat 7%)</div>
                <div class="flex-grow-1" style="text-align: right; font-weight: bold;">'. number_format($rsIpc['ipc']['contract_value'], 2) .'</div>
              </div>
            </div>

            <div class="payment-boxx">
              <h3>INTERIM PAYMENT CLAIM No.1</h3>
            </div>

            <div class="payment-details">
              <div class="item">
                <div class="flex-grow-1">Total Value Of Interim Payment</div>
                <div class="text-end" style="width: 150px;">'. number_format($rsIpc['ipc']['total_value_of_interim_payment'], 2) .'</div>
              </div>
              <div class="item">
                <div class="flex-grow-1">Less Previous Interim Payment</div>
                <div class="text-end" style="width: 150px;">'. number_format($rsIpc['ipc']['less_previous_interim_payment'], 2) .'</div>
              </div>
              <div class="item">
                <div class="flex-grow-1">Net Value of Current Claim</div>
                <div class="text-end" style="width: 150px;">'. number_format($rsIpc['ipc']['net_value_of_current_claim'], 2) .'</div>
              </div>
              <div class="item">
                <div class="flex-grow-1">Less Retention 5% (Exclu. VAT)</div>
                <div class="text-end" style="width: 150px;">'. number_format($rsIpc['ipc']['less_retension_exclude_vat'], 2) .'</div>
              </div>
            </div>
            <div class="d-flex justify-content-between fw-bold" style="font-size: 18px;">
              <div class="">NET AMOUNT DUE FOR PAYMENT No.1</div>
              <div class="text-end">'. number_format($rsIpc['ipc']['net_amount_due_for_payment'], 2) .'</div>
            </div>

            <div class="payment-details" style="margin-bottom:10px;">
              <div class="item">
                <div class="flex-grow-1">Total Value of Retention (Inclu. this certificate)</div>
                <div class="text-end" style="width: 150px;">'. number_format($rsIpc['ipc']['total_value_of_retention'], 2) .'</div>
              </div>
              <div class="item">
                <div class="flex-grow-1">Total Value of Certification made (Inclu. this certificate)</div>
                <div class="text-end" style="width: 150px;">'. number_format($rsIpc['ipc']['total_value_of_certification_made'], 2) .'</div>
              </div>
              <div class="item">
                <div class="flex-grow-1">Resulting Balance of Contract Sum Outstanding</div>
                <div class="text-end" style="width: 150px;">'. number_format($rsIpc['ipc']['resulting_balance_of_contract_sum_outstanding'], 2) .'</div>
              </div>
            </div>

            <div class="signatures">
              <div class="signature-block" style="margin-bottom:10px;">
                <div>By : <span class="signature-line"></span></div>
                <div class="signature-name">( Watchara Chanthrasopa )</div>
                <div class="signature-">Head of Project Management Department</div>
              </div>

              <div class="signature-block" style="margin-bottom:10px;">
                <div>By : <span class="signature-line"></span></div>
                <div class="signature-name">( Tanawat Worasakdinan )</div>
                <div class="signature-">Cost control Manager</div>
                <div style="margin-top: 30px;">By : <span class="signature-line"></span></div>
                <div class="signature-name">( Apichaya Sindhuprama )</div>
                <div class="signature-">Project Manager</div>
              </div>
            </div>

            <!-- <div class="company-footer">
              IMPACT EXHIBITION MANAGEMENT CO., LTD.<br>
              10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,<br>
              Banmai Sub-district, Pakkred District, Nonthaburi 11120<br>
              GREATER BANGKOK, THAILAND
            </div> -->

          </div>';

// $mpdf = new \Mpdf\Mpdf(['default_font' => 'thsarabun']); // รองรับภาษาไทย
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'thsarabun', // หรือชื่อฟอนต์ที่คุณต้องการ
    'autoScriptToLang' => true,
    'autoLangToFont' => true
]);
$mpdf->WriteHTML($html);
$mpdf->Output("receipt_".$receipt['invoice_no'].".pdf", "I"); // แสดง PDF ใน browser
