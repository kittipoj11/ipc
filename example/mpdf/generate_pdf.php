<?php
// 3. รับข้อมูลและสร้าง PDF

// เรียกใช้ Autoloader ของ Composer(โหลด mPDF อัตโนมัติ)
require_once __DIR__ . '/vendor/autoload.php';

// รับข้อมูลที่ส่งมาจาก AJAX (ควรมีการตรวจสอบความปลอดภัยของข้อมูลเสมอ)
$data = json_decode($_POST['invoice_data'], true);

// สร้าง HTML สำหรับใส่ใน PDF
// ส่วนนี้สำคัญมาก เราจะสร้าง HTML ขึ้นมาใหม่ทั้งหมดบน Server
// เพื่อความปลอดภัยและเพื่อให้ mPDF ประมวลผลได้ถูกต้อง
$customerName = htmlspecialchars($data['customer']);
$invoiceNumber = htmlspecialchars($data['number']);
$total = 0;

$html = '
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        /* นี่คือจุดสำคัญ: กำหนดฟอนต์ที่รองรับภาษาไทยที่ mPDF มีอยู่แล้ว */
        body {
            font-family: "sarabun", "THSarabunNew", sans-serif;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>ใบเสร็จรับเงิน / Receipt</h1>
    <p>ลูกค้า (Customer): ' . $customerName . '</p>
    <p>เลขที่ (No.): ' . $invoiceNumber . '</p>

    <table>
        <thead>
            <tr>
                <th>รายการ / Description</th>
                <th>จำนวน / Qty</th>
                <th>ราคา / Price</th>
                <th>รวม / Subtotal</th>
            </tr>
        </thead>
        <tbody>';

// วนลูปสร้างรายการสินค้า
foreach ($data['items'] as $item) {
  $description = htmlspecialchars($item['description']);
  $qty = (int)$item['qty'];
  $price = (float)$item['price'];
  $subtotal = $qty * $price;
  $total += $subtotal;

  $html .= '
            <tr>
                <td>' . $description . '</td>
                <td style="text-align:center;">' . $qty . '</td>
                <td style="text-align:right;">' . number_format($price, 2) . '</td>
                <td style="text-align:right;">' . number_format($subtotal, 2) . '</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>
    <h2 style="text-align:right;">ยอดรวม (Total): ' . number_format($total, 2) . ' บาท</h2>
</body>
</html>';


try {
  // สร้างอ็อบเจกต์ mPDF
  // $mpdf = new \Mpdf\Mpdf([
  //     'tempDir' => __DIR__ . '/tmp', // สร้างโฟลเดอร์ tmp และกำหนด permission ให้เขียนได้
  //     'mode' => 'utf-8', 
  //     'format' => 'A4'
  // ]);

  // แก้ไขเป็น
  // $mpdf = new \Mpdf\Mpdf([
  //   'tempDir' => __DIR__ . '/tmp', // สร้างโฟลเดอร์ tmp และกำหนด permission ให้เขียนได้
  //   'default_font' => 'sarabun' // <-- เพิ่มบรรทัดนี้เข้าไป
  // ]);

  // แก้ไขอีกทีเป็น
  $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'thsarabun', // หรือชื่อฟอนต์ที่คุณต้องการ
    'autoScriptToLang' => true,
    'autoLangToFont' => true
]);

  // เขียน HTML ลงใน PDF
  $mpdf->WriteHTML($html);

  // สร้างชื่อไฟล์ที่ไม่ซ้ำกัน
  $filename = 'invoices/receipt-' . uniqid() . '.pdf';
  // สร้างโฟลเดอร์ invoices และกำหนด permission ให้เขียนได้
  if (!file_exists('invoices')) {
    mkdir('invoices', 0775, true);
  }

  // บันทึกไฟล์ลงบน Server
  $mpdf->Output($filename, \Mpdf\Output\Destination::FILE);

  // 5. ส่งผลลัพธ์กลับไปให้ JavaScript
  header('Content-Type: application/json');
  echo json_encode(['status' => 'success', 'file' => $filename]);
} catch (\Mpdf\MpdfException $e) {
  header('Content-Type: application/json');
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
