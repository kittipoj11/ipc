<?php
require 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM receipts WHERE id=?");
$stmt->execute([$id]);
$receipt = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("SELECT * FROM receipt_items WHERE receipt_id=?");
$stmt2->execute([$id]);
$items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center mb-3">
  <img src="assets/logo-impact.svg" alt="Company Logo" width="120" height="auto">
  <h3 class="mt-2">ใบเสร็จรับเงิน / Receipt</h3>
  <p>ลูกค้า (Customer): <?= htmlspecialchars($receipt['customer_name']) ?></p>
  <p>เลขที่ (No.): <?= htmlspecialchars($receipt['invoice_no']) ?></p>
</div>

<table>
  <thead>
    <tr>
      <th>รายการ / Description</th>
      <th>จำนวน / Qty</th>
      <th>ราคา / Price</th>
      <th>รวม / Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($items as $item): ?>
    <tr>
      <td><?= $item['description_th']." (".$item['description_en'].")" ?></td>
      <td><?= $item['qty'] ?></td>
      <td><?= number_format($item['price'],2) ?></td>
      <td><?= number_format($item['qty']*$item['price'],2) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>ยอดรวม (Total): <?= number_format($receipt['total'],2) ?> บาท</h3>

<button onclick="printPDF(<?= $receipt['id'] ?>)">พิมพ์ PDF</button>
