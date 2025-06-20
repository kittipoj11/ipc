<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>บันทึกข้อมูลงวดงาน (AJAX)</title>
</head>

<body>
  <h1>บันทึกข้อมูลงวดงาน</h1>

  <form id="periods-form">
    <table>
      <thead>
        <tr>
          <th>งวดที่</th>
          <th>% งาน</th>
          <th>จำนวนเงิน</th>
          <th>% การชำระ</th>
          <th>หมายเหตุ</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="periods-tbody">
        <tr data-period-id="101">
          <td><input type="number" name="period" value="1" readonly></td>
          <td><input type="number" name="work_percent" value="30"></td>
          <td><input type="number" name="interim_payments" value="50000"></td>
          <td><input type="number" name="interim_payment_percents" value="50"></td>
          <td><input type="text" name="remarks" value="remark1_updated"></td>
          <td><input type="text" name="action" value="update" readonly></td>
        </tr>
        <tr data-period-id="">
          <td><input type="number" name="period" value="2" readonly></td>
          <td><input type="number" name="work_percent" value="30"></td>
          <td><input type="number" name="interim_payments" value="30000"></td>
          <td><input type="number" name="interim_payment_percents" value="30"></td>
          <td><input type="text" name="remarks" value="remark2"></td>
          <td><input type="text" name="action" value="insert" readonly></td>
        </tr>
        <tr data-period-id="103">
          <td><input type="number" name="period" value="3" readonly></td>
          <td><input type="number" name="work_percent" value="40"></td>
          <td><input type="number" name="interim_payments" value="20000"></td>
          <td><input type="number" name="interim_payment_percents" value="20"></td>
          <td><input type="text" name="remarks" value="remark3"></td>
          <td><input type="text" name="action" value="delete" readonly></td>
        </tr>
      </tbody>
    </table>

    <br>
    <button type="button" id="save-periods-btn">บันทึกข้อมูลทั้งหมด</button>
  </form>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script src="app_periods.js"></script>
</body>

</html>

<!-- 
เพิ่ม id="periods-tbody" ให้กับ <tbody> เพื่อให้ JavaScript อ้างอิงได้ง่าย
เพิ่ม data-period-id="..." ในแต่ละ <tr> เพื่อระบุ ID ของงวดงานที่มีอยู่แล้ว (สำหรับการ update หรือ delete)
เพิ่มปุ่ม <button id="save-periods-btn"> แยกออกมาเพื่อเป็นตัวสั่งงาน
   -->