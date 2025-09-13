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
    'margin_right' => 10
]);


$html1 = '
  <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
    <colgroup>
        <col style="width: 30%;">
        <col style="width: 60%;">
        <col style="width: 10%;">
    </colgroup>
    <thead>
      <tr>
        <th style="border: 1px solid black; text-align: left; padding: 5px;">Header 1 (30%)</th>
        <th style="border: 1px solid black; text-align: left; padding: 5px;">Header 2 (60%)</th>
        <th style="border: 1px solid black; text-align: left; padding: 5px;">Header 3 (10%)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="border: 1px solid black; padding: 5px;">Data 1</td>
        <td style="border: 1px solid black; padding: 5px;">Data 2</td>
        <td style="border: 1px solid black; padding: 5px;">Data 3</td>
      </tr>
    </tbody>
</table>
    
';

// $stylesheet = file_get_contents('pdfstyle.css');
// $mpdf->WriteHTML($stylesheet, 1);  // 1 = CSS

$pdfFilename = "IPC_" . $rsIpc['pomain']['po_number'] . "_" .  $rsIpc['ipc']['period_number']  . ".pdf";
$mpdf->WriteHTML($html1);


$mpdf->Output($pdfFilename, "I"); // แสดง PDF ใน browser
