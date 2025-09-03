<?php
@session_start();
// require_once __DIR__ . '/vendor/autoload.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/ipc/vendor/autoload.php';
// $_SESSION['doc root']=$_SERVER["DOCUMENT_ROOT"];
if (!isset($_POST['booking'])) {
    die("No data");
}

$booking = json_decode($_POST['booking'], true);

// $mpdf = new \Mpdf\Mpdf();
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'thsarabun', // หรือชื่อฟอนต์ที่คุณต้องการ
    'autoScriptToLang' => true,
    'autoLangToFont' => true
]);
$html = "
<h2 style='text-align:center'>Booking Details</h2>
<h4>ข้อมูลผู้จอง</h4>
<ul>
  <li><b>ID:</b> {$booking['id']}</li>
  <li><b>ชื่อผู้จอง:</b> {$booking['booking_name']}</li>
  <li><b>Email:</b> " . ($booking['email'] ?? '-') . "</li>
  <li><b>Phone:</b> " . ($booking['phone'] ?? '-') . "</li>
  <li><b>Booth:</b> {$booking['booth']}</li>
  <li><b>Reservation ID:</b> {$booking['reservation_id']}</li>
</ul>
<hr>
<h4>รายละเอียดการจอง (Cars)</h4>
<table border='1' cellpadding='5' cellspacing='0' width='100%'>
  <thead>
    <tr>
      <th>วันที่</th>
      <th>เวลาเริ่ม</th>
      <th>เวลาสิ้นสุด</th>
      <th>ทะเบียนรถ</th>
      <th>ประเภทรถ</th>
      <th>ชื่อคนขับ</th>
      <th>มือถือ</th>
    </tr>
  </thead>
  <tbody>";

foreach ($booking['details'] as $d) {
    $html .= "
    <tr>
      <td>{$d['booking_date']}</td>
      <td>{$d['booking_time_start']}</td>
      <td>{$d['booking_time_end']}</td>
      <td>{$d['car_license_number']}</td>
      <td>{$d['car_type_id']}</td>
      <td>" . ($d['driver_name'] ?? '-') . "</td>
      <td>" . ($d['driver_mobile'] ?? '-') . "</td>
    </tr>";
}

$html .= "</tbody></table>";

$mpdf->WriteHTML($html);

// ส่งเป็นไฟล์ PDF กลับไป
$pdfContent = $mpdf->Output('', 'S');
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=booking_{$booking['id']}.pdf");
echo $pdfContent;
