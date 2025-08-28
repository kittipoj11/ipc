<?php
require_once __DIR__ . '/vendor/autoload.php';

// สร้างอ็อบเจกต์ mPDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'thsarabun', // หรือชื่อฟอนต์ที่คุณต้องการ
    'autoScriptToLang' => true,
    'autoLangToFont' => true
]);

// กำหนดค่า Header และ Footer
$mpdf->SetHeader('รายงานทดสอบ');
$mpdf->SetFooter('{PAGENO}');

// สร้างเนื้อหา HTML
$html = '

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
';

// เขียน HTML ลงใน PDF
$mpdf->WriteHTML($html);

// ส่งออก PDF
$mpdf->Output('รายงาน.pdf', 'I'); // 'I' เพื่อแสดงผลในเบราว์เซอร์, 'D' เพื่อดาวน์โหลด
?>