<?php
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
<html>
<head>
<title>PDF</title>
<link href="https://fonts.googleapis.com/css?family=Sarabun&display=swap" rel="stylesheet">
<style>
body {
    font-family: sarabun;
}
table {
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
<body>

<h1>ตัวอย่างในการเก็บโค้ด HTML มาเป็น PDF</h1>
<table>
  <tr>
    <th>ชื่อ</th>
    <th>ที่อยู่</th>
    <th>ประเทศ</th>
  </tr>
  <tr>
    <td>น้องไก่ คนงาม</td>
    <td>นายบ้าน บ้าน</td>
    <td>ไทย</td>
  </tr>
  <tr>
    <td>นายรักเรียน</td>
    <td>Francisco Chang</td>
    <td>Mexico</td>
  </tr>
  <tr>
    <td>นายรักดี</td>
    <td>Roland Mendel</td>
    <td>Austria</td>
  </tr>
</table>

</body>
</html>

<?php
$html = ob_get_contents();
$mpdf->WriteHTML($html);

// สร้าง QR
$text = "เยี่ยมชมเว็บไซต์: www.impact.co.th";
$text_utf8 = mb_convert_encoding($text, 'UTF-8', 'auto');
$mpdf->WriteHTML('<barcode code="' . $text_utf8 . '" type="QR" size="0.3" error="M" disableborder="1" />');
// end QR

$mpdf->Output("MyPDF.pdf");
ob_end_flush()
?>

ดาวโหลดรายงานในรูปแบบ PDF <a href="MyPDF.pdf">คลิกที่นี้</a>