<?php
// require_once __DIR__ . '/vendor/autoload.php'; // ติดตั้ง mPDF ด้วย composer
require_once $_SERVER["DOCUMENT_ROOT"] . '/ipc/vendor/autoload.php';
require_once  'class/connection_class.php';
require_once  'class/ipc_class.php';

$connection = new Connection;
$pdo = $connection->getDbConnection();

$ipcId = $_REQUEST['ipc_id'];

$ipc = new Ipc($pdo);
$rsIpc = $ipc->getIpcByIpcId($ipcId);

$html = '
<style>
    body {
        font-family: "dejavusans", sans-serif;
        font-size: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid black;
      border: none;
      /* cellpadding="4";
      cellspacing="0";*/
    }
    td {
      border: 1px solid #333;
      padding: 8px;
      vertical-align: top;
      border: none;
    }
    .col-fixed {
      width: 150px; /* คอลัมน์แรก fix 150px */
    }

    .title {
        text-align: start;
        font-weight: bold;
        font-size: 14pt;
        margin-bottom: 15px;
    }
    .section {
        margin-top: 8px;
        margin-bottom: 8px;
    }
    .label {
        font-weight: bold;
    }
    .box {
        border: 1px solid #000;
        padding: 5px;
        font-weight: bold;
    }
    .black-box {
        border: 1px solid #000;
        padding: 5px;
        font-weight: bold;
        background-color: #000;
        color:#FFF;
    }
    .right {
        text-align: right;
    }
    .center {
        text-align: center;
    }
    .signature {
        margin-top: 50px;
        text-align: center;
        font-weight: bold;
    }
    .company-footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: right;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
</style>

<div class="title black-box">INTERIM CERTIFICATE</div>

<table>
  <tr>
    <td class="col-fixed"><span class="label">DATE</span></td>
    <td>18<sup>th</sup> May 2023</td>
  </tr>
  <tr>
    <td class="col-fixed"><span class="label">PROJECT</span></td>
    <td>' . $rsIpc['pomain']['project_name'] . '</td>
  </tr>
  <tr>
    <td class="col-fixed"><span class="label">OWNER</span></td>
    <td>IMPACT Exhibition Management Co., Ltd.<br>47/569-576, 10th floor, Bangkok Land Building,<br>Popular 3 Road, Banmai Sub-district,<br>Pakkred District, Nonthaburi 11120</td>
  </tr>
  <tr>
    <td class="col-fixed"><span class="label">AGREEMENT DATE</span></td>
    <td>' . $rsIpc['ipc']['agreement_date'] . '</td>
  </tr>
  <tr>
    <td class="col-fixed"><span class="label">CONTRACTOR</span></td>
    <td>' . $rsIpc['ipc']['contractor'] . '</td>
  </tr>
  <tr>
    <td class="col-fixed"><span class="label">CONTRACT VALUE</span></td>
    <td>
      <table style="width:100%; border:0;padding:0;">
        <tr>
          <td style="text-align:left; border:0;padding:0;">(Including Vat 7%)</td>
          <td style="text-align:right; border:0;padding:0;">' . number_format($rsIpc['ipc']['contract_value'], 2) . '</td>
        </tr>
      </table>
  </td>
  </tr>
</table>

<div class="box">INTERIM PAYMENT CLAIM No.1</div>

<table>
    <tr>
        <td>Total Value Of Interim Payment</td>
        <td class="right">' . number_format($rsIpc['ipc']['total_value_of_interim_payment'], 2) . '</td>
    </tr>
    <tr>
        <td>Less Previous Interim Payment</td>
        <td class="right">' . number_format($rsIpc['ipc']['less_previous_interim_payment'], 2) . '</td>
    </tr>
    <tr>
        <td>Net Value of Current Claim</td>
        <td class="right">' . number_format($rsIpc['ipc']['net_value_of_current_claim'], 2) . '</td>
    </tr>
    <tr>
        <td>Less Retention 5% (Exclu. VAT)</td>
        <td class="right">' . number_format($rsIpc['ipc']['less_retension_exclude_vat'], 2) . '</td>
    </tr>
    <tr>
      <td colspan="2" style="height:25px;"></td>
    </tr>    
    <tr>
        <td><span class="label">NET AMOUNT DUE FOR PAYMENT No.1</span></td>
        <td class="right label">' . number_format($rsIpc['ipc']['net_amount_due_for_payment'], 2) . '</td>
    </tr>
    <tr>
      <td colspan="2" style="height:25px;"></td>
    </tr>  
    <tr>
        <td>Total Value of Retention (Inclu. this certificate)</td>
        <td class="right"><span style="float:right;">' . number_format($rsIpc['ipc']['total_value_of_retention'], 2) . '</span></td>
    </tr>
    <tr>
        <td>Total Value of Certification made (Inclu. this certificate)</td>
        <td class="right"><span style="float:right;">' . number_format($rsIpc['ipc']['total_value_of_certification_made'], 2) . '</span></td>
    </tr>
    <tr>
        <td>Resulting Balance of Contract Sum Outstanding</td>
        <td class="right"><span style="float:right;">' . number_format($rsIpc['ipc']['resulting_balance_of_contract_sum_outstanding'], 2) . '</span></td>
    </tr>    
</table>

<table>
  <tr>
    <td colspan="2" style="height:25px;"></td>
  </tr>  

  <tr>
    <td class="col-fixed center">
        <div class="signature">
            By : <img src="images/signature.jpg" width="120"><br>
                    ( Watchara Chanthrasopa ) <br>
                Head of Project Management Department
        </div>
    </td>

    <td class="col-fixed center">
        <div class="signature">
            By : <img src="images/signature.jpg" width="120"><br>
                    ( Tanawat Worasakdinan ) <br>
                      Cost Control Manager
        </div>
    </td>
  </tr>

  <tr>
    <td colspan="2" style="height:25px;"></td>
  </tr>  

  <tr>
    <td class="col-fixed center">

    </td>

    <td class="col-fixed center">
        <div class="signature">
            By : <img src="images/signature.jpg" width="120"><br>
                    ( Apichaya Sindhuprama ) <br>
                        Project Manager
        </div>
    </td>
  </tr>
</table>

        <div class="company-footer">
            IMPACT EXHIBITION MANAGEMENT CO., LTD.<br>
            10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,<br>
            Banmai Sub-district, Pakkred District, Nonthaburi 11120<br>
            GREATER BANGKOK, THAILAND
        </div>
';

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'default_font' => 'thsarabun',
    'autoScriptToLang' => true,
    'autoLangToFont' => true,
    'format' => 'A4',
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_left' => 15,
    'margin_right' => 15
]);
$pdfFilename = "ipc_" . $rsIpc['pomain']['po_number'] . "_" .  $rsIpc['ipc']['period_number']  . ".pdf";
$mpdf->WriteHTML($html);
$mpdf->Output($pdfFilename, "I"); // แสดง PDF ใน browser
