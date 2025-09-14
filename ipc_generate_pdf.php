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
$ipcApprover = [];
foreach ($rsIpc['approver'] as $row) {
  $key = $row['order_in_block'];
  unset($row['order_in_block']); // ลบ key 'id' ออกจาก value
  $ipcApprover[$key] = $row;
}

$inspection = new Inspection($pdo);
$rsInspection = $inspection->getByInspectionId($inspectionId);


$inspectionApprover = [];
foreach ($rsInspection['approver'] as $row) {
  $key = $row['order_in_block'];
  unset($row['order_in_block']); // ลบ key 'id' ออกจาก value
  $inspectionApprover[$key] = $row;
}
// $_SESSION['approver']=$inspectionApprover;

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'default_font' => 'thsarabun',
    'autoScriptToLang' => true,
    'autoLangToFont' => true,
    'format' => 'A4',
    'margin_top' => 20,
    'margin_bottom' => 40,
    'margin_left' => 10,
    'margin_right' => 10,
]);

$mpdf->SetHTMLHeader('
<div class="header">
    <div class="logo">
        <img src="images/impact_logo.jpg" alt="IMPACT MUANG THONG THANI Logo">
    </div>
</div>
');

$mpdf->SetHTMLFooter('
<div class="company-footer">
  <table>
    <tr>
      <td class="left-footer td-w50">
        <div class="left-footer small-text">
            IMPACT ARENA<br>
            IMPACT FORUM<br>
            IMPACT CHALLENGER<br>
            IMPACT EXHIBITION CENTER<br>
            NOVOTEL BANGKOK IMPACT<br>
        </div>
      </td>

      <td class="right-footer td-w50">
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
<div style="border-top:1px solid #000; font-size:9pt; text-align: center; padding-top:5px;">
    © 2025 Impact Exhibition Management Co.,Ltd. — หน้า {PAGENO} / {nb}
</div>
');

$html1 = '
  <div class="card-body m-0 p-0">
    <div class="container">
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

        <table>
          <tr>
              <td colspan="2" style="height:25px;"></td>
          </tr>  
          <tr>
              <td class="center td-w50">
                <table>
                  <tr>
                    <td class="by">By :</td>
                    <td class="center">
                      <div class="signature-block">
                        <p><img src="' . $ipcApprover['4']['signature'] . '" width="120" style="display:' . $ipcApprover['4']['display'] . '"></p>
                        <div class="sig-line">____________________________</div>
                        <div class="signature-details">
                            <p>( ' . $ipcApprover['4']['full_name'] . ' )</p>
                            <p>Executive Director</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>
              </td>

              <td class="center td-w50">
                  <div class="signature-block">

                  </div>
              </td>
          </tr>
        </table>
    </div>
</div>
';

$html2 = '
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
        <table style="width:100%;">
            <tr>
            <td style="text-align:left;">(Including Vat 7%)</td>
            <td style="text-align:right;">' . number_format($rsIpc['ipc']['contract_value'], 2) . '</td>
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
        <td class="center td-w50">
          <table>
            <tr>
              <td class="by">By :</td>
              <td>
                <div class="signature-block">
                  <p><img src="' . $ipcApprover['3']['signature'] . '" width="120" style="display:' . $ipcApprover['3']['display'] . '"></p>
                  <div class="sig-line">____________________________</div>
                  <div class="signature-details">
                      <p>( ' . $ipcApprover['3']['full_name'] . ' )</p>
                      <p>Head of Project Management Department</p>
                  </div>
                </div>
              </td>
            </tr>
          </table>        
        </td>

        <td class="center td-w50">
          <table>
            <tr>
              <td class="by">By :</td>
              <td>
                <div class="signature-block">
                  <p><img src="' . $ipcApprover['2']['signature'] . '" width="120" style="display:' . $ipcApprover['2']['display'] . '"></p>
                  <div class="sig-line">____________________________</div>
                  <div class="signature-details">
                      <p>( ' . $ipcApprover['2']['full_name'] . ' )</p>
                      <p>Cost Control Manager</p>
                  </div>
                </div>
              </td>
            </tr>
          </table> 
        </td>
    </tr>

    <tr>
        <td colspan="2" style="height:25px;"></td>
    </tr>  

    <tr>
        <td class="center td-w50">

        </td>

        <td class="center td-w50">
          <table>
            <tr>
              <td class="by">By :</td>
              <td>
                <div class="signature-block">
                  <p><img src="' . $ipcApprover['1']['signature'] . '" width="120" style="display:' . $ipcApprover['1']['display'] . '"></p>
                  <div class="sig-line">____________________________</div>
                  <div class="signature-details">
                      <p>( ' . $ipcApprover['1']['full_name'] . ' )</p>
                      <p>Project Manager</p>
                  </div>
                </div>
              </td>
            </tr>
          </table> 
        </td>
    </tr>
    </table>
';

$tableDetails='';
$html3= '    
    <div class="container">
    <div class="title center">การตรวจรับงาน<br>Project Management Department</div>

    <table class="small-text">
      <tr>
        <td colspan=2>
          <table style="">
            <tr>
              <td class="col-fixed-width"><span class="label">ชื่อผู้รับเหมา/SUPPLIER :</span></td>
              <td>' . $rsInspection['header']['supplier_name'] . '</td>
            </tr>
          </table>
        </td>
      </tr>
      
      <tr>
        <td>
          <table style="">
            <tr>
              <td class="col-fixed-width"><span class="label">โครงการ :</span></td>
              <td>' . $rsInspection['header']['project_name'] . '</td>
            </tr>
          </table>
        </td>
        <td>
          <table style="">
            <tr>
        <td class="col-fixed-width"><span class="label">สถานที่ :</span></td>
        <td>' . $rsInspection['header']['location_name'] . '</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td colspan=2>
          <table style="">
            <tr>
              <td class="col-fixed-width"><span class="label">งาน :</span></td>
              <td>' . $rsInspection['header']['working_name_th'] . '(' . $rsInspection['header']['working_name_en'] . ')</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <table>
      <tr>
          <td class="col-fixed-width"><span class="label">ระยะเวลาดำเนินการ :</span></td>
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
          <table style="">
            <tr>
              <td class="col-fixed-width"><span class="label">เลขที่ PO</span></td>
              <td>' . $rsInspection['header']['po_number'] . '</td>
            </tr>
          </table>
        </td>
        <td>
          <table style="">
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
        <td style="width:25%;">
          <table style="">
            <tr>
              <td colspan=2><span class="label">เบิกงวดงานที่</span></td>
              <td width="30%">' . $rsInspection['period']['period_number'] . '</td>
            </tr>
            <tr>
              <td width="30"><input type="checkbox" checked="checked"></td>
              <td colspan=2>มี Deposit ' . $rsInspection['header']['deposit_percent'] . ' %</td>
            </tr>
            <tr>
              <td width="30"><input type="checkbox"></td>
              <td colspan=2>ไม่มี Deposit</td>
            </tr>
          </table>
        </td>
        <td>
          <table  style="border-left: 1px solid black; width:100%; table-layout:fixed;">
            <colgroup>
              <col style="width:20%">
              <col>
              <col style="width:5%">
            </colgroup>
            <tbody>
            <tr>
              <td><span class="label-normal">ยอดเบิกเงินงวดปัจจุบัน</span></td>
              <td style="text-align:right;"><span class="label-normal">' . number_format($rsInspection['period']['interim_payment'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right;">' . $rsInspection['period']['interim_payment_percent'] . ' %</td>
            </tr>
            <tr>
              <td><span class="label-normal">ยอดเบิกเงินงวดสะสมไม่รวมปัจจุบัน</span></td>
              <td style="text-align:right;"><span class="label-normal">' . number_format($rsInspection['period']['interim_payment_less_previous'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right;">' . $rsInspection['period']['interim_payment_less_previous_percent'] . ' %</td>
            </tr>
            <tr>
              <td><span class="label-normal">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</span></td>
              <td style="text-align:right;"><span class="label-normal">' . number_format($rsInspection['period']['interim_payment_accumulated'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right;">' . $rsInspection['period']['interim_payment_accumulated_percent'] . ' %</td>
            </tr>
            <tr>
              <td><span class="label-normal">ยอดเงินงวดคงเหลือ</span></td>
              <td style="text-align:right;"><span class="label-normal">' . number_format($rsInspection['period']['interim_payment_remain'], 2) . ' บาท (Including VAT7%) คิดเป็น</span></td>
              <td style="text-align:right;">' . $rsInspection['period']['interim_payment_remain_percent'] . ' %</td>
            </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </table>

    <hr class="hr border border-dark">

    <table style="border:0;padding:5px;">
      <tr>
        <td class="col-fixed-width"><span class="label">ปริมาณที่ต้องแล้วเสร็จตามแผนงาน </span>' . $rsInspection['period']['workload_planned_percent'] . ' %</td>
        <td class="col-fixed-width"><span class="label">ปริมาณที่แล้วเสร็จจริง </span>' . $rsInspection['period']['workload_actual_completed_percent'] . ' %</td>
        <td class="col-fixed-width"><span class="label">ปริมาณงานคงเหลือ </span>' . $rsInspection['period']['workload_remaining_percent'] . ' %</td>
      </tr>
    </table>

    <table style=" border:1;padding:5px;">
      <thead>
      <tr style="border:1;">
          <th style="border:1;padding:5px;width=50px" >ลำดับที่</th>
          <th style="border:1;padding:5px;" width="25%">รายละเอียดการตรวจสอบ</th>
          <th style="border:1;padding:5px;" >หมายเหตุ</th>
      </tr>
      </thead>
    <tbody>';

foreach ($rsInspection['periodDetails'] as $row) {
    $tableDetails .= '
      <tr style="border:1;">
        <td style="border:1;padding:5px;">'. $row['order_no'] . '</td>
        <td style="border:1;padding:5px;">'. $row['details'] . '</td>
        <td style="border:1;padding:5px;">'. $row['remark'] .'</td>
      </tr>       
      ';
};

$html3 .= $tableDetails;
$html3 .= '
</tbody>
  </table>

  <table style="">
    <tr>
      <td class="col-fixed-width"><span class="label">ปริมาณที่ต้องแล้วเสร็จเมื่อเปรียบเทียบกับแผนงาน: </span>' . $rsInspection['plan_status']['plan_status_name'] . '</td>
    </tr>
  </table>
  
  <table style="">
    <tr>
      <td class="col-fixed-width"><span class="label">หมายเหตุ:</span></td>
    </tr>
    <tr>
      <textarea rows="5" style="min-height: 4em;height: auto;width:100%;">' . $rsInspection['period']['remark'] . '</textarea>
    </tr>
  </table>
  
  <table style="">
    <tr>
      <td class="col-fixed-width"><span class="label">ผู้รับเหมาได้ดำเนินการตามรายละเอียดดังกล่าวข้างต้น จึงเห็นสมควร</span></td>
      <td class="col-fixed-width"><span class="label"><input type="radio" checked="checked">อนุมัติเบิกจ่าย</span></td>
      <td class="col-fixed-width"><span class="label"><input type="radio">ไม่อนุมัติเบิกจ่าย</span></td>
    </tr>
  </table>

<table>
    <tr>
        <td colspan="2" style="height:25px;"></td>
    </tr>  
    <tr>
        <td class="center td-w50">
          <table>
            <tr>
              <td class="by">By :</td>
              <td>
                <div class="signature-block">
                  <p><img src="' . $inspectionApprover['1']['signature'] . '" width="120" style="display:' . $inspectionApprover['1']['display'] . '"></p>
                  <div class="sig-line">____________________________</div>
                  <div class="signature-details">
                      <p>( ' . $inspectionApprover['1']['full_name'] . ' )</p>
                      <p>ผู้ตรวจสอบ</p>
                  </div>
                </div>
              </td>
            </tr>
          </table> 
        </td>

        <td class="center td-w50">
          <table>
            <tr>
              <td class="by">By :</td>
              <td>
                <div class="signature-block">
                  <p><img src="' . $inspectionApprover['2']['signature'] . '" width="120" style="display:' . $inspectionApprover['2']['display'] . '"></p>
                  <div class="sig-line">____________________________</div>
                  <div class="signature-details">
                      <p>( ' . $inspectionApprover['2']['full_name'] . ' )</p>
                      <p>หัวหน้าฝ่ายบริหารโครงการ</p>
                  </div>
                </div>
              </td>
            </tr>
          </table> 
        </td>
    </tr>
</table>


';

$html4 = '
<form>
    <p><strong>1. ท่านพึงพอใจกับการให้บริการหรือไม่?</strong><br>
        <input type="radio" name="q1" value="yes" checked="checked"> พึงพอใจ<br>
        <input type="radio" name="q1" value="no"> ไม่พึงพอใจ
    </p>

    <p><strong>2. สิ่งที่ท่านต้องการเพิ่มเติม (เลือกได้มากกว่า 1 ข้อ):</strong><br>
        <input type="checkbox" name="q2[]" value="delivery" checked="checked"> จัดส่งรวดเร็ว<br>
        <input type="checkbox" name="q2[]" value="support"> บริการหลังการขาย<br>
        <input type="checkbox" name="q2[]" value="price"> ราคาย่อมเยา<br>
        <input type="checkbox" name="q2[]" value="quality"> คุณภาพสินค้า
    </p>
</form>
  <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
    <tbody>
      <tr>
        <td style="width: 20%;border: 1px solid red; padding: 5px;">Data 1</td>
        <td style="border: 1px solid red; padding: 5px;">Data 2</td>
        <td style="width: 5%;border: 1px solid red; padding: 5px;">%</td>
      </tr>
      <tr>
        <td style="border: 1px solid red; padding: 5px;">Data 4Data 4Data 4Data 4Data 4Data 4Data 4</td>
        <td style="border: 1px solid red; padding: 5px;">Data 5Data 5Data 5Data 5Data 5Data 5Data 5Data 5Data 5Data 5</td>
        <td style="border: 1px solid red; padding: 5px;">%</td>
      </tr>
      <tr>
        <td style="border: 1px solid red; padding: 5px;">Data 7</td>
        <td style="border: 1px solid red; padding: 5px;">Data 8</td>
        <td style="border: 1px solid red; padding: 5px;">%</td>
      </tr>
    </tbody>
</table>
    
';

$stylesheet = file_get_contents('pdfstyle.css');
$mpdf->WriteHTML($stylesheet, 1);  // 1 = CSS

$pdfFilename = "IPC_" . $rsIpc['pomain']['po_number'] . "_" .  $rsIpc['ipc']['period_number']  . ".pdf";
$mpdf->WriteHTML($html1);
$mpdf->AddPage();
$mpdf->WriteHTML($html2);
$mpdf->AddPage();
$mpdf->WriteHTML($html3);
$mpdf->AddPage();
$mpdf->WriteHTML($html4);

$mpdf->Output($pdfFilename, "I"); // แสดง PDF ใน browser
