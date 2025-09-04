<?php
// require_once __DIR__ . '/vendor/autoload.php'; // ติดตั้ง mPDF ด้วย composer
require_once $_SERVER["DOCUMENT_ROOT"] . '/ipc/vendor/autoload.php';
require_once  'class/connection_class.php';
require_once  'class/inspection_class.php';
require_once  'class/ipc_class.php';

$connection = new Connection;
$pdo = $connection->getDbConnection();

$ipcId = $_REQUEST['ipc_id'];
$inspectionId = $_REQUEST['inspection_id'];

$ipc = new Ipc($pdo);
$rsIpc = $ipc->getIpcByIpcId($ipcId);

$inspection = new Inspection($pdo);
$rsInspection = $inspection->getByInspectionId($inspectionId);

$html1 = '
<style>
      body {
        font-family: "dejavusans", sans-serif;
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
        padding: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }
      .header {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;
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
      .header .title-block h2 {
        margin: 0;
      }
      .header .title-block h2 {
        font-size: 18px;
        font-weight: normal;
        margin: 5px 0 0 0;
      }
      .header .refs {
        text-align: right;
        font-size: 12px;
      }
      .header .refs p {
        margin: 0;
      }
      .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 5px;
      }
      .info-label {
        font-weight: bold;
      }
      .content {
        margin-top: 2px;
      }
      .content .salutation {
        margin-bottom: 20px;
        font-size: 12px;
      }
      .content .body-text {
        font-size: 12px;
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
        margin-top: 25px;
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
      table .left-footer {
        text-align: left;
      }
      table .right-footer {
        text-align: right;
      }
      table td p {
        margin: 0;
        font-size: 10px;
      }
      .small-text {
        font-size: 10px;
      }
    .company-footer {
        margin-top: 5px;
        font-size: 10px;
        text-align: right;
        border-top: 1px solid #ccc;
        padding-top: 0px;
    }

    </style>

<div class="card-body m-0 p-0">
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="images/impact_logo.jpg" alt="IMPACT MUANG THONG THANI Logo">
            </div>
        </div>
        <div class="header">
            <div class="title-block">
                <h2>PROJECT MANAGEMENT DEPARTMENT</h2>
            </div>
        </div>
        <div class="header">
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
            <div class="info-labelx">IMPACT Exhibition Management Co., Ltd.</div>
            <div class="info-value"></div>
        </div>
        <div class="info-row">
            <div class="info-labelx">47/569-576, 10th floor, Bangkok Land Building,</div>
            <div class="info-value"></div>
        </div>
        <div class="info-row">
            <div class="info-labelx">Popular 3 Road, Banmai Sub-district,</div>
            <div class="info-value"></div>
        </div>
        <div class="info-row">
            <div class="info-labelx">Pakkred District, Nonthaburi 11120</div>
            <div class="info-value"></div>
        </div>

        <br>
        <p style="text-align: center; font-weight: bold; font-size: 14px;">STRICTLY CONFIDENTIAL</p>
        <br>

        <div class="content">
            <div class="salutation">
                Dear Sir,
                <br><br>
                Impact Exhibition Management Co., Ltd.
            </div>

            <div class="body-text">
                <ol>
                    <li>1. The Project management Department of IMPACT Exhibition Management Company Limited is in charge of the management of the construction and development works in respect of all project at IMPACT Exhibition & Convention Center Muang Thong Thani, Chaeng wattana, Nonthaburi Province.</li>
                    <li>2. In accordance with the terms of the Agreement between IMPACT Exhibition Management Co.,Ltd. and Winstar corp Co.,Ltd., IMPACT is obliged to make interim payment for the executed work.</li>
                    <li>3. Interim payment for ' . $rsIpc["pomain"]["project_name"] . ' is now due and we attach to this letter certificate certifying that Such payment is due.</li>
                </ol>
            </div>
        </div>

        <div class="signature-block">
            <p>By : <img src="images/signature.jpg" width="120"></p>
            <div class="sig-line"></div>
            <div class="signature-details">
                <p>( Kunwadee Jintavorn )</p>
                <p>Executive Director</p>
            </div>
        </div>

<div class="company-footer">
        <table>


            <tr>
                <td class="left-footer">
                    <div class="left-footer small-text">
                        IMPACT ARENA<br>
                        IMPACT FORUM<br>
                        IMPACT CHALLENGER<br>
                        IMPACT EXHIBITION CENTER<br>
                        NOVOTEL BANGKOK IMPACT<br>
                    </div>
                </td>

                <td class="right-footer">
                    <div class="right-footer small-text">
                        IMPACT EXHIBITION MANAGEMENT CO., LTD.<br>
                        10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,<br>
                        Banmai Sub-district, Pakkred District, Nonthaburi 11120<br>
                        GREATER BANGKOK, THAILAND<br>
                        Tel : <span class="small-text">+66(0) 2833-4455</span> Fax : <span class="small-text">+66(0) 2833-4456</span><br>
                        E-mail : <span class="small-text">info@impact.co.th</span> Website : <span class="small-text">www.impact.co.th</span><br>
                    </div>
                </td>
            </tr>
        </table>
        </div>
</div>


    </div>

</div>
';

$html2 = '
    <style>
      body {
        font-family: "dejavusans", sans-serif;
        font-size: 12px;
      }

      .container {
        max-width: 800px;
        margin: 0 auto;
        border: 1px solid #ddd;
        padding: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid red;
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

      .col-fixed-width {
        width: 150px; /* คอลัมน์แรก fix 150px */
      }

      .title {
        text-align: start;
        font-weight: bold;
        font-size: 12pt;
        margin-bottom: 15px;
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
        color: #fff;
      }

      .right {
        text-align: right;
      }

      .center {
        text-align: center;
      }

      .signature-block {
        margin-top: 5px;
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
        margin-top: 5px;
      }
      .signature-details p {
        margin: 0;
        font-size: 12px;
      }

    table .left-footer {
        text-align: left;
      }
      table .right-footer {
        text-align: right;
      }
      table td p {
        margin: 0;
        font-size: 10px;
      }
      .small-text {
        font-size: 10px;
      }

    .company-footer {
        margin-top: 5px;
        font-size: 10px;
        text-align: right;
        border-top: 1px solid #ccc;
        padding-top: 0px;
    }
    </style>

<div class="container">
    <div class="title black-box">INTERIM CERTIFICATE</div>

    <table>
    <tr>
        <td class="col-fixed-width"><span class="label">DATE</span></td>
        <td>18<sup>th</sup> May 2023</td>
    </tr>
    <tr>
        <td class="col-fixed-width"><span class="label">PROJECT</span></td>
        <td>' . $rsIpc['pomain']['project_name'] . '</td>
    </tr>
    <tr>
        <td class="col-fixed-width"><span class="label">OWNER</span></td>
        <td>IMPACT Exhibition Management Co., Ltd.<br>47/569-576, 10th floor, Bangkok Land Building,<br>Popular 3 Road, Banmai Sub-district,<br>Pakkred District, Nonthaburi 11120</td>
    </tr>
    <tr>
        <td class="col-fixed-width"><span class="label">AGREEMENT DATE</span></td>
        <td>' . $rsIpc['ipc']['agreement_date'] . '</td>
    </tr>
    <tr>
        <td class="col-fixed-width"><span class="label">CONTRACTOR</span></td>
        <td>' . $rsIpc['ipc']['contractor'] . '</td>
    </tr>
    <tr>
        <td class="col-fixed-width"><span class="label">CONTRACT VALUE</span></td>
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
        <td class="col-fixed-width center">
            <div class="signature-block">
                <p>By : <img src="images/signature.jpg" width="120"></p>
                <div class="sig-line"></div>
                <div class="signature-details">
                    <p>( Watchara Chanthrasopa )</p>
                    <p>Head of Project Management Department</p>
                </div>
            </div>
        </td>

        <td class="col-fixed-width center">
            <div class="signature-block">
                By : <img src="images/signature.jpg" width="120"><br>
                <div class="sig-line"></div>
                <div class="signature-details">
                    ( Tanawat Worasakdinan ) <br>
                    Cost Control Manager
                </div>
            </div>
        </td>
    </tr>

    <tr>
        <td colspan="2" style="height:25px;"></td>
    </tr>  

    <tr>
        <td class="col-fixed-width center">

        </td>

        <td class="col-fixed-width center">
            <div class="signature-block">
                By : <img src="images/signature.jpg" width="120"><br>
                    ( Apichaya Sindhuprama ) <br>
                    Project Manager
            </div>
        </td>
    </tr>
    </table>
<div class="company-footer">
        <table>


            <tr>
                <td class="left-footer">
                    <div class="left-footer small-text">
                        IMPACT ARENA<br>
                        IMPACT FORUM<br>
                        IMPACT CHALLENGER<br>
                        IMPACT EXHIBITION CENTER<br>
                        NOVOTEL BANGKOK IMPACT<br>
                    </div>
                </td>

                <td class="right-footer">
                    <div class="right-footer small-text">
                        IMPACT EXHIBITION MANAGEMENT CO., LTD.<br>
                        10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,<br>
                        Banmai Sub-district, Pakkred District, Nonthaburi 11120<br>
                        GREATER BANGKOK, THAILAND<br>
                        Tel : <span class="small-text">+66(0) 2833-4455</span> Fax : <span class="small-text">+66(0) 2833-4456</span><br>
                        E-mail : <span class="small-text">info@impact.co.th</span> Website : <span class="small-text">www.impact.co.th</span><br>
                    </div>
                </td>
            </tr>
        </table>
        </div>
</div>
';

$html3= '    
<style>
      body {
        font-family: "dejavusans", sans-serif;
        font-size: 12px;
      }

      .container {
        max-width: 800px;
        margin: 0 auto;
        border: 1px solid #ddd;
        padding: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid red;
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

      .col-fixed-width {
        width: 150px; /* คอลัมน์แรก fix 150px */
      }

      .title {
        text-align: center;
        font-weight: bold;
        font-size: 12pt;
        margin-bottom: 15px;
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
        color: #fff;
      }

      .right {
        text-align: right;
      }

      .center {
        text-align: center;
      }

    table .left-footer {
        text-align: left;
      }
      table .right-footer {
        text-align: right;
      }
      table td p {
        margin: 0;
        font-size: 10px;
      }
      .small-text {
        font-size: 10px;
      }

    .company-footer {
        margin-top: 5px;
        font-size: 10px;
        text-align: right;
        border-top: 1px solid #ccc;
        padding-top: 0px;
    }
    </style>

    <div class="container">
    <div class="title center">การตรวจรับงาน<br>Project Management Department</div>

    <table>
      <tr>
        <td>
          <table style="width:100%; border:0;padding:0;">
            <tr>
              <td class="col-fixed-width"><span class="label">ผู้รับเหมา</span></td>
              <td>' . $rsInspection['header']['supplier_name'] . '</td>
            </tr>
          </table>
        </td>
        <td>
          <table style="width:100%; border:0;padding:0;">
            <tr>
              <td class="col-fixed-width"><span class="label">โครงการ</span></td>
              <td>' . $rsInspection['header']['project_name'] . '</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table style="width:100%; border:0;padding:0;">
            <tr>
        <td class="col-fixed-width"><span class="label">สถานที่</span></td>
        <td>' . $rsInspection['header']['location_name'] . '</td>
            </tr>
          </table>
        </td>
        <td>
          <table style="width:100%; border:0;padding:0;">
            <tr>
        <td class="col-fixed-width"><span class="label">งาน</span></td>
        <td>' . $rsInspection['header']['working_name_th'] . '(' . $rsInspection['header']['working_name_en'] . ')</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <table>
      <tr>
          <td class="col-fixed-width"><span class="label">ระยะเวลาดำเนินการ</span></td>
          <td>' . $rsInspection['header']['working_date_from'] . '</td>
          <td class="col-fixed-width"><span class="label">ถึง</span></td>
          <td>' . $rsInspection['header']['working_date_to'] . '</td>
          <td>(รวม ' . $rsInspection['header']['working_day'] . ' วัน)</td>
      </tr>
    </table>

    <hr class="hr border border-dark">

    <table>
      <tr>
        <td>
          <table style="width:100%; border:0;padding:0;">
            <tr>
              <td class="col-fixed-width"><span class="label">เลขที่ PO</span></td>
              <td>' . $rsInspection['header']['po_number'] . '</td>
            </tr>
          </table>
        </td>
        <td>
          <table style="width:100%; border:0;padding:0;">
            <tr>
              <td class="col-fixed-width"><span class="label">มูลค่างานตาม PO</span></td>
              <td>' . number_format($rsInspection['header']['contract_value'], 2) . ' บาท(Includeing VAT' . $rsInspection['header']['is_include_vat'] . '%)</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <hr class="hr border border-dark">

    <table>
      <tr>
        <td style="width:30%;">
          <table style="width:100%; border:0;padding:0;">
            <tr>
              <td class="col-fixed-width"><span class="label">เบิกงวดงานที่</span></td>
              <td>' . $rsInspection['period']['period_number'] . '</td>
            </tr>
            <tr>
              <td class="col-fixed-width"><input type="checkbox" checked></td>
              <td>มี Deposit' . $rsInspection['header']['deposit_percent'] . '</td>
            </tr>
            <tr>
              <td class="col-fixed-width"><input type="checkbox"></td>
              <td>ไม่มี Deposit</td>
            </tr>
          </table>
        </td>
        <td>
          <table style="width:100%; border:0;padding:0;">
            <tr>
              <td class="col-fixed-width"><span class="label">ยอดเบิกเงินงวดปัจจุบัน</span></td>
              <td class="col-fixed-width"><span class="label">' . number_format($rsInspection['period']['interim_payment'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right; border:0;padding:0;">' . $rsInspection['period']['interim_payment_percent'] . ' %</td>
            </tr>
            <tr>
              <td class="col-fixed-width"><span class="label">ยอดเบิกเงินงวดสะสมไม่รวมปัจจุบัน</span></td>
              <td class="col-fixed-width"><span class="label">' . number_format($rsInspection['period']['interim_payment_less_previous'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right; border:0;padding:0;">' . $rsInspection['period']['interim_payment_less_previous_percent'] . ' %</td>
            </tr>
            <tr>
              <td class="col-fixed-width"><span class="label">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</span></td>
              <td class="col-fixed-width"><span class="label">' . number_format($rsInspection['period']['interim_payment_accumulated'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right; border:0;padding:0;">' . $rsInspection['period']['interim_payment_accumulated_percent'] . ' %</td>
            </tr>
            <tr>
              <td class="col-fixed-width"><span class="label">ยอดเงินงวดคงเหลือ</span></td>
              <td class="col-fixed-width"><span class="label">' . number_format($rsInspection['period']['interim_payment_remain'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right; border:0;padding:0;">' . $rsInspection['period']['interim_payment_remain_percent'] . ' %</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <hr class="hr border border-dark">

    <table style="width:100%; border:0;padding:5px;">
      <tr>
        <td class="col-fixed-width"><span class="label">ปริมาณที่ต้องแล้วเสร็จตามแผนงาน</span>' . $rsInspection['period']['workload_planned_percent'] . ' %</td>
        <td class="col-fixed-width"><span class="label">ปริมาณที่แล้วเสร็จจริง</span>' . $rsInspection['period']['workload_actual_completed_percent'] . ' %</td>
        <td class="col-fixed-width"><span class="label">ปริมาณงานคงเหลือ</span>' . $rsInspection['period']['workload_remaining_percent'] . ' %</td>
      </tr>
    </table>

    <table style="width:100%; border:1;padding:5px;">
      <thead>
      <tr style="border:1;">
          <th style="border:1;padding:5px;" width="10%">ลำดับที่</th>
          <th style="border:1;padding:5px;" width="25%">รายละเอียดการตรวจสอบ</th>
          <th style="border:1;padding:5px;" >หมายเหตุ</th>
      </tr>
      </thead>
    <tbody>';

foreach ($rsInspection['periodDetails'] as $row) {
    $tableBody .= '
      <tr style="border:1;">
        <td style="border:1;padding:5px;">'. $row['order_no'] . '</td>
        <td style="border:1;padding:5px;">'. $row['details'] . '</td>
        <td style="border:1;padding:5px;">'. $row['remark'] .'</td>
      </tr>       
      ';
};

$html3 .= $tableBody;
$html3 .= '
    </tbody>
  </table>

  <table style="width:100%; border:0;padding:0;">
    <tr>
      <td class="col-fixed-width"><span class="label">ปริมาณที่ต้องแล้วเสร็จเมื่อเปรียบเทียบกับแผนงาน</span>' . $rsInspection['plan_status']['plan_status_name'] . ' %</td>
    </tr>
  </table>
  
  <table style="width:100%; border:0;padding:0;">
    <tr>
      <td class="col-fixed-width"><span class="label">หมายเหตุ:</span>' . $rsInspection['plan_status']['plan_status_name'] . ' %</td>
    </tr>
    <tr>
      <textarea rows="4" style="min-height: 4em;height: auto;">' . $rsInspection['period']['remark'] . '</textarea>
    </tr>
  </table>
  
  <table style="width:100%; border:0;padding:0;">
    <tr>
      <td class="col-fixed-width"><span class="label">ผู้รับเหมาได้ดำเนินการตามรายละเอียดดังกล่าวข้างต้น จึงเห็นสมควร</span></td>
      <td class="col-fixed-width"><span class="label"><input type="radio" checked>อนุมัติเบิกจ่าย</span></td>
      <td class="col-fixed-width"><span class="label"><input type="radio">ไม่อนุมัติเบิกจ่าย</span></td>
    </tr>
  </table>

<div class="company-footer">
        <table>


            <tr>
                <td class="left-footer">
                    <div class="left-footer small-text">
                        IMPACT ARENA<br>
                        IMPACT FORUM<br>
                        IMPACT CHALLENGER<br>
                        IMPACT EXHIBITION CENTER<br>
                        NOVOTEL BANGKOK IMPACT<br>
                    </div>
                </td>

                <td class="right-footer">
                    <div class="right-footer small-text">
                        IMPACT EXHIBITION MANAGEMENT CO., LTD.<br>
                        10<sup>th</sup> Floor, Bangkok Land Building, 47/569-576 Popular 3 Road,<br>
                        Banmai Sub-district, Pakkred District, Nonthaburi 11120<br>
                        GREATER BANGKOK, THAILAND<br>
                        Tel : <span class="small-text">+66(0) 2833-4455</span> Fax : <span class="small-text">+66(0) 2833-4456</span><br>
                        E-mail : <span class="small-text">info@impact.co.th</span> Website : <span class="small-text">www.impact.co.th</span><br>
                    </div>
                </td>
            </tr>
        </table>
        </div>
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
$pdfFilename = "IPC_" . $rsIpc['pomain']['po_number'] . "_" .  $rsIpc['ipc']['period_number']  . ".pdf";
$mpdf->WriteHTML($html1);
$mpdf->AddPage();
$mpdf->WriteHTML($html2);
$mpdf->AddPage();
$mpdf->WriteHTML($html3);

$mpdf->Output($pdfFilename, "I"); // แสดง PDF ใน browser
