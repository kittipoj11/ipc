<?php
require_once('TCPDF/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');
$pdf->SetCreator('Nathapat');
$pdf->SetAuthor('Nathapat Developer');
$pdf->SetTitle('Nathapat Example 03');
$pdf->SetSubject('Nathapat Example');
$pdf->SetKeywords('Nathapat, TCPDF, PDF, example, guide');

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Nathapat Example 03', 'This is PDF Header', array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// กำหนดให้ไม่แสดงส่วนหัวของเอกสาร
$pdf->setPrintHeader(false);
// กำหนดให้ไม่แสดงส่วนท้ายของเอกสาร
$pdf->setPrintFooter(false);

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

$html = "Welcome to <b>Nathapat</b> - Without Header & Footer";
$pdf->SetFont('dejavusans', '', 14);
$pdf->writeHTMLCell(0, 0, '', 50, $html, 0, 1, 0, true, 'C', true);

$pdf->Output('Nathapat03.pdf', 'I');
