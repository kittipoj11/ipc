<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor - ทำการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center mb-0">📝 แบบฟอร์มการจองที่จอดรถ</h3>
                    </div>
                    <div class="card-body p-4">
                        <div id="message-container"></div>
                        <form id="booking-form">
                            <input type="hidden" name="action" value="create_booking">

                            <div class="mb-3">
                                <label for="vendor_name" class="form-label">ชื่อบุคคล/บริษัท/องค์กร</label>
                                <input type="text" class="form-control" id="vendor_name" name="vendor_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="event_id" class="form-label">เลือกงาน</label>
                                <select class="form-select" id="event_id" name="event_id" required>
                                    <option selected disabled value="">-- กรุณาเลือก --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="parking_lot" class="form-label">เลือกลานจอด</label>
                                <select class="form-select" id="parking_lot" name="parking_lot" required disabled>
                                    <option selected disabled value="">-- กรุณาเลือกงานก่อน --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="booking_date" class="form-label">เลือกวันที่</label>
                                <select class="form-select" id="booking_date" name="booking_date" required disabled>
                                    <option selected disabled value="">-- กรุณาเลือกงานก่อน --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="start_time" class="form-label">เลือกเวลา (จองได้ 1 ชั่วโมง)</label>
                                <select class="form-select" id="start_time" name="start_time" required disabled>
                                    <option selected disabled value="">-- กรุณาเลือกวันที่ก่อน --</option>
                                </select>
                            </div>

                            <div id="availability-info" class="alert alert-info" style="display: none;"></div>

                            <div class="mb-3">
                                <label for="num_cars" class="form-label">จำนวนรถ (ไม่เกิน 10 คัน)</label>
                                <input type="number" class="form-control" id="num_cars" name="num_cars" min="1" max="10" required disabled>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">ยืนยันการจอง</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./assets/vendor_script.js"></script>
</body>

</html>