<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <h3 class="mb-3">📌 Booking Management</h3>

  <!-- ฟอร์ม Booking Header -->
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">ข้อมูลผู้จอง</h5>
      <form id="bookingForm">
        <input type="hidden" name="id" id="booking_id">
        <div class="row mb-2">
          <div class="col-md-4">
            <label>ชื่อผู้จอง</label>
            <input type="text" class="form-control" name="booking_name" required>
          </div>
          <div class="col-md-4">
            <label>Email</label>
            <input type="email" class="form-control" name="email">
          </div>
          <div class="col-md-4">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone">
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4">
            <label>Booth</label>
            <input type="text" class="form-control" name="booth" required>
          </div>
          <div class="col-md-4">
            <label>Reservation ID</label>
            <input type="number" class="form-control" name="reservation_id" required>
          </div>
        </div>

        <h6 class="mt-3">รายละเอียดการจอง (Cars)</h6>
        <table class="table table-bordered" id="detailsTable">
          <thead class="table-light">
            <tr>
              <th>วันที่</th>
              <th>เวลาเริ่ม</th>
              <th>เวลาสิ้นสุด</th>
              <th>ทะเบียนรถ</th>
              <th>ประเภทรถ</th>
              <th>ชื่อคนขับ</th>
              <th>เบอร์มือถือ</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
        <button type="button" id="addDetail" class="btn btn-sm btn-success mb-3">➕ Add Detail</button>

        <div>
          <button type="submit" class="btn btn-primary">💾 Save Booking</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ตาราง Booking ทั้งหมด -->
  <table class="table table-bordered" id="bookingTable">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>ชื่อผู้จอง</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Booth</th>
        <th>Reservation</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/booking.js"></script>
</body>
</html>
