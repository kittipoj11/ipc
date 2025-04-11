<?php
require_once('tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');

$pdf->SetCreator('Nathapat');
$pdf->SetAuthor('Nathapat Developer');
$pdf->SetTitle('Nathapat Example 05');
$pdf->SetSubject('Nathapat Example');
$pdf->SetKeywords('Nathapat, TCPDF, PDF, example, guide');

$pdf->setHeaderFont(array('freeserif', 'B', 12));
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Nathapat Example 05', 'การเพิ่มตัวอักษร Font (ฟอนต์) และการนำไปใช้', array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

$pdf->SetFont('freeserif', '', 20);
$pdf->Cell('', '', 'ยินดีต้อนรับสู่ NATHAPAT.COM', 0, 0, 'C', false, 'http://www.Nathapat.com');

$pdf->Output('Nathapat05.pdf', 'I');
