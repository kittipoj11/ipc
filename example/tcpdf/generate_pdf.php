<?php
    require_once  '../class/po_class.php';
    // require_once  class/supplier_class.php';
    // require_once  class/location_class.php';

    // $_SESSION['Request'] = $_REQUEST;
    // $po_id = $_REQUEST['po_id'];
    // $period_id = $_REQUEST['period_id'];
    // $inspection_id = $_REQUEST['inspection_id'];

    $po = new Po;
    $results = $po->getExampleRecord();
    // $rsInspectionPeriod = $po->getInspectionPeriodByPeriodId($po_id, $period_id);
    // $rsInspectionFiles = $po->getInspectionFilesByInspectionId($po_id, $period_id, $inspection_id);

    // $supplier = new Supplier;
    // $supplier_rs = $supplier->fetchAll();

    // $location = new Location;
    // $location_rs = $location->fetchAll();

    

// ดึงข้อมูลจากฟอร์ม
$companyLogo = $_FILES['company_logo']['tmp_name'];
$reportTitle = $_POST['report_title'];
$headerText = $_POST['header_text'];
$filterCategory = $_POST['filter_category'];
$statusFilter = isset($_POST['status']) ? $_POST['status'] :'';
$searchKeyword = $_POST['search_keyword'];

// สร้าง query SQL แบบมีเงื่อนไข
// $sql = "SELECT * FROM your_table_name WHERE 1=1"; // เริ่มต้นด้วยเงื่อนไขที่เป็นจริงเสมอ

// if (!empty($filterCategory)) {
//     $sql .= " AND category = :category";
// }

// if (!empty($statusFilter)) {
//     $sql .= " AND status IN (" . implode(',', array_map(function($s){ return "'".$s."'"; }, $statusFilter)) . ")";
// }

// if (!empty($searchKeyword)) {
//     $sql .= " AND (column1 LIKE :keyword OR column2 LIKE :keyword)"; // ปรับให้เข้ากับคอลัมน์ที่ต้องการค้นหา
// }

// $stmt=$myConnect->myConnect->prepare($sql);
// $stmt = $pdo->prepare($sql);

// Bind ค่า parameters ถ้ามี
// if (!empty($filterCategory)) {
//     $stmt->bindParam(':category', $filterCategory);
// }

// if (!empty($searchKeyword)) {
//     $keyword = "%" . $searchKeyword . "%";
//     $stmt->bindParam(':keyword', $keyword);
// }

// $stmt->execute();
// $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include the TCPDF library
require_once('../class/TCPDF/tcpdf.php'); // ปรับ path ให้ถูกต้องกับตำแหน่งของ TCPDF
// กำหนด Path แบบเต็มไปยังไฟล์ฟอนต์ของคุณ
$font_path = 'D:/_xampp/htdocs/iclass/TCPDF/fonts/THSarabunNew/thsarabunnew.ttf';

// เพิ่มฟอนต์ TH Sarabun New
$fontname = TCPDF_FONTS::addTTFfont($font_path, 'TrueTypeUnicode', '', 32);

// สร้าง object ของ TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// กำหนดข้อมูลเอกสาร
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle($reportTitle);
$pdf->SetSubject($reportTitle);
$pdf->SetKeywords('TCPDF, PDF, MySQL, Report');

// กำหนดส่วนหัวเริ่มต้น
$pdf->SetHeaderData('', 30, $headerText, $reportTitle, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// กำหนด font เริ่มต้น
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// กำหนด margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// กำหนดการพักหน้าอัตโนมัติ
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// กำหนดอัตราส่วนภาพ
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// กำหนด font สำหรับภาษาไทย (สำคัญมาก)
// แบบที่ 1
// $pdf->SetFont('thsarabun', '', 16); // 'thsarabun' เป็น font ที่รองรับภาษาไทย คุณอาจต้องมีไฟล์ .ttf สำหรับ font นี้ใน directory font ของ TCPDF
$pdf->SetFont('thsarabunnew', '', 16, '', true, 'fonts/THSarabunNew/');
// แบบที่ 2
// กำหนด Path แบบเต็มไปยังไฟล์ฟอนต์ของคุณ
// $font_path = 'D:/_xampp/htdocs/iclass/TCPDF/fonts/THSarabunNew/thsarabunnew.ttf';

// เพิ่มฟอนต์ TH Sarabun New
// $fontname = TCPDF_FONTS::addTTFfont($font_path, 'TrueTypeUnicode', '', 32);

// กำหนดค่าคงที่สำหรับชื่อฟอนต์ (นำไปใช้กับ SetFont)
// define('THSARABUN_NEW', $fontname);

// กำหนด font สำหรับภาษาไทย (สำคัญมาก)
// $pdf->SetFont('THSARABUN_NEW', '', 16);

// เพิ่มหน้าแรก
$pdf->AddPage();

// แสดงโลโก้บริษัท (ถ้ามีการอัปโหลด)
if (!empty($companyLogo)) {
    $pdf->Image($companyLogo, 10, 10, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    // $pdf->Image($companyLogo, 10, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
}

// สร้างตาราง HTML สำหรับแสดงข้อมูล
$html = '<table border="1" cellpadding="5">';
$html .= '<thead>';
$html .= '<tr style="background-color:#f0f0f0;">';
$html .= '<th width="10%">ID</th>';
$html .= '<th width="30%">คอลัมน์ 1</th>';
$html .= '<th width="30%">คอลัมน์ 2</th>';
$html .= '<th width="30%">คอลัมน์ 3</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';

foreach ($results as $row) {
    $html .= '<tr>';
    $html .= '<td>' . $row['id'] . '</td>';
    $html .= '<td>' . $row['column1'] . '</td>';
    $html .= '<td>' . $row['column2'] . '</td>';
    $html .= '<td>' . $row['column3'] . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>';
$html .= '</table>';

// แสดงผลตาราง HTML ใน PDF
$pdf->writeHTML($html, true, false, true, false, '');

// สร้าง output PDF
$pdf->Output('report.pdf', 'I'); // 'I' แสดงในเบราว์เซอร์, 'D' ให้ดาวน์โหลด
?>