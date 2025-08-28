<?php
require 'db.php';
require_once __DIR__ . '/vendor/autoload.php'; // ติดตั้ง mPDF ด้วย composer

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM receipts WHERE id=?");
$stmt->execute([$id]);
$receipt = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("SELECT * FROM receipt_items WHERE receipt_id=?");
$stmt2->execute([$id]);
$items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$html = '
<div style="text-align:center;">
  <img src="assets/logo-impact.svg" width="120" style="margin-bottom:10px;">
  <h3>ใบเสร็จรับเงิน / Receipt</h3>
</div>
<p>ลูกค้า (Customer): '.$receipt['customer_name'].'</p>
<p>เลขที่ (No.): '.$receipt['invoice_no'].'</p>

<table border="1" cellpadding="5" cellspacing="0" width="100%">
<tr>
  <th>รายการ / Description</th>
  <th>จำนวน / Qty</th>
  <th>ราคา / Price</th>
  <th>รวม / Subtotal</th>
</tr>';

foreach($items as $item){
  $subtotal = $item['qty'] * $item['price'];
  $html .= '
  <tr>
    <td>'.$item['description_th'].' ('.$item['description_en'].')</td>
    <td align="center">'.$item['qty'].'</td>
    <td align="right">'.number_format($item['price'],2).'</td>
    <td align="right">'.number_format($subtotal,2).'</td>
  </tr>';
}

$html .= '
</table>
<h3 style="text-align:right;">ยอดรวม (Total): '.number_format($receipt['total'],2).' บาท</h3>
';

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
