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

$html3='    
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
    <div class="title black-box center">การตรวจรับงาน<br>Project Management Department</div>

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

<div class="d-flex justify-content-between">
        <div class="col d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">ผู้รับเหมา</div>
        <div>'.$rsInspection['header']['supplier_name'].'</div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <div class="col d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">โครงการ</div>
        <div >'.$rsInspection['header']['project_name'].'</div>
        </div>

        <div class="col col-4 d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">สถานที่</div>
        <div>'.$rsInspection['header']['location_name'].'</div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <div class="col d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">งาน</div>
        <div>'.$rsInspection['header']['working_name_th']. '('.$rsInspection['header']['working_name_en'].')</div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <div class="col col-4 d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">ระยะเวลาดำเนินการ</div>
        <div >'.$rsInspection['header']['working_date_from'].'</div>
        </div>

        <div class="col col-4 d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">ถึง</div>
        <div>'.$rsInspection['header']['working_date_to'].'</div>
        </div>

        <div class="col col-4 d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">(รวม '.$rsInspection['header']['working_day'].' วัน)</div>
        </div>
    </div>

    <hr class="hr border border-dark">

    <div class="d-flex justify-content-between">
        <div class="col col-6 d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">เลขที่ PO</div>
        <div >'.$rsInspection['header']['po_number'].'</div>
        </div>

        <div class="col col-6 d-flex justify-content-start">
        <div class="fw-bold" style="width: 200px;">มูลค่างานตาม PO</div>
        <div>'. number_format($rsInspection['header']['contract_value'],2).' บาท(Includeing VAT'.$rsInspection['header']['is_include_vat'].'%)</div>
        </div>
    </div>

    <hr class="hr border border-dark">

    <div class="d-flex">
        <div class="col-3 border-end border-dark-subtle m-0 p-0">
        <div class="col d-flex justify-content-start">
            <div class="fw-bold" style="width: 200px;">เบิกงวดงานที่</div>
            <div>'. $rsInspection['period']['period_number'].'</div>
        </div>

        <div class="col d-flex flex-column justify-content-start">
            <div class="col d-flex justify-content-start">
            <input type="checkbox" name="deposit" onclick="return false;" checked>
            <div class="fw-bold" style="width: 200px;">มี Deposit</div>
            <div>'.$rsInspection['header']['deposit_percent'].'</div>
            </div>
            
            <div class="col d-flex justify-content-start">
            <input type="checkbox" name="deposit" onclick="return false;">
            <div class="fw-bold" style="width: 200px;">ไม่มี Deposit</div>
            </div>
        </div>
        </div>

        <div class="col-9">
        <div class="col d-flex justify-content-between m-1">
            <div class="col-5">ยอดเบิกเงินงวดปัจจุบัน</div>
            <div class="col-5 text-end">'. number_format($rsInspection['period']['interim_payment'],2).' บาท (Including VAT7%) คิดเป็น</div>
            <div class="col-2 text-end ">'. $rsInspection['period']['interim_payment_percent'].' %</div>
        </div>

        <div class="col d-flex justify-content-between m-1">
            <div class="col-5">ยอดเบิกเงินงวดสะสมไม่รวมปัจจุบัน</div>
            <div class="col-5 text-end">'. number_format($rsInspection['period']['interim_payment_less_previous'],2).' บาท (Including VAT7%) คิดเป็น</div>
            <div class="col-2 text-end ">'. $rsInspection['period']['interim_payment_less_previous_percent'].' %</div>
        </div>

        <div class="col d-flex justify-content-between m-1">
            <div class="col-5">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</div>
            <div class="col-5 text-end">'. number_format($rsInspection['period']['interim_payment_accumulated'],2).' บาท (Including VAT7%) คิดเป็น</div>
            <div class="col-2 text-end ">'. $rsInspection['period']['interim_payment_accumulated_percent'].' %</div>
        </div>

        <div class="col d-flex justify-content-between m-1">
            <div class="col-5">ยอดเงินงวดคงเหลือ</div>
            <div class="col-5 text-end">'. number_format($rsInspection['period']['interim_payment_remain'],2).' บาท (Including VAT7%) คิดเป็น</div>
            <div class="col-2 text-end ">'. $rsInspection['period']['interim_payment_remain_percent'].' %</div>
        </div>

        </div>
    </div>

    <hr class="hr border border-dark">

    <div class="d-flex justify-content-between">
        <div class="col col-4 d-flex justify-content-between">
        <div>ปริมาณที่ต้องแล้วเสร็จตามแผนงาน</div>
        <div>'. $rsInspection['period']['workload_planned_percent'].' %</div>
        </div>

        <div class="col col-4 d-flex justify-content-between">
        <div>ปริมาณที่แล้วเสร็จจริง</div>
        <div>'. $rsInspection['period']['workload_actual_completed_percent'].' %</div>
        </div>

        <div class="col col-4 d-flex justify-content-between">
        <div>ปริมาณงานคงเหลือ</div>
        <div>'. $rsInspection['period']['workload_remaining_percent'].' %</div>
        </div>
    </div>

    <div class="card border border-1 border-dark m-1">
        <div class="card-body p-0">
        <table class="table table-bordered justify-content-center text-center" id="tableOrder">
            <thead>
            <tr>
                <th class="p-1" width="10%">ลำดับที่</th>
                <th class="p-1" width="20%">รายละเอียดการตรวจสอบ</th>
                <th class="p-1">หมายเหตุ</th>
            </tr>
            </thead>
            <tbody id="tbody-order">';

foreach ($rsInspection['periodDetails'] as $row) {
    $tableBody .= '
                    <tr>
                      <td class="tdPeriod text-right py-0 px-1">'. $row['order_no'] .'</td>
                      <td class="tdPeriod text-right py-0 px-1">'. $row['details'] .'</td>
                      <td class="tdPeriod text-right py-0 px-1">'. $row['remark'] .'</td>
                    </tr>       
                    ';
};

$html3 .= $tableBody;
$html3 .=          '</tbody>
                    </table>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <div class="col d-flex justify-content-start">
                    <div>ปริมาณที่ต้องแล้วเสร็จเมื่อเปรียบเทียบกับแผนงาน : '. $rsInspection['plan_status']['plan_status_name'].' %</div>
                  </div>
                </div>

                <div>หมายเหตุ:</div>
                <div class="form-floating">
                  <textarea name="remark" class="form-control" id="remark" rows="4" style="min-height: 4em;height: auto;" readonly>'. $rsInspection['period']['remark'].'</textarea>
                </div>

                <div class="col d-flex justify-content-start">
                  <div>ผู้รับเหมาได้ดำเนินการตามรายละเอียดดังกล่าวข้างต้น จึงเห็นสมควร</div>
                  <div class="col d-flex justify-content-start">
                    <input type="radio" name="disbursement" onclick="return false;" checked>
                    <div class="fw-bold" style="width: 200px;">อนุมัติเบิกจ่าย</div>
                  </div>
                  
                  <div class="col d-flex justify-content-start">
                    <input type="radio" name="disbursement" onclick="return false;">
                    <div class="fw-bold" style="width: 200px;">ไม่อนุมัติเบิกจ่าย</div>
                  </div>
                </div>';

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
$mpdf->WriteHTML($html1);
$mpdf->AddPage();
$mpdf->WriteHTML($html2);
$mpdf->AddPage();
$mpdf->WriteHTML($html3);
$mpdf->Output($pdfFilename, "I"); // แสดง PDF ใน browser
