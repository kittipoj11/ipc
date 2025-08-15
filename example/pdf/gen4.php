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
<h1>หัวข้อรายงาน</h1>
<p>ย่อหน้าแรกของรายงาน.</p>
<p><b>ข้อความตัวหนา</b> และ <i>ข้อความตัวเอียง</i></p>
<table>
    <tr>
        <th>Column 1</th>
        <th>Column 2</th>
    </tr>
    <tr>
        <td>ข้อมูลแถว 1 คอลัมน์ 1</td>
        <td>ข้อมูลแถว 1 คอลัมน์ 2</td>
    </tr>
</table>
';

// เขียน HTML ลงใน PDF
$mpdf->WriteHTML($html);

// ส่งออก PDF
$mpdf->Output('รายงาน.pdf', 'I'); // 'I' เพื่อแสดงผลในเบราว์เซอร์, 'D' เพื่อดาวน์โหลด
?>