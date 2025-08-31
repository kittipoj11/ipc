<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ระบบจัดการการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">สำหรับผู้ดูแลระบบ</h1>
        <div id="message-container"></div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>1. สร้างงาน (Event)</h5>
                    </div>
                    <div class="card-body">
                        <form id="create-event-form">
                            <input type="hidden" name="action" value="create_event">
                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่องาน</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="เช่น Motor2025" required>
                            </div>
                            <div class="mb-3">
                                <label for="parking_lots" class="form-label">ลานจอดและจำนวน</label>
                                <input type="text" class="form-control" id="parking_lots" name="parking_lots" placeholder="รูปแบบ: A:50, B:60, C:100" required>
                                <div class="form-text">ใส่ชื่อลานจอด คั่นด้วย ':' และจำนวน จากนั้นคั่นแต่ละลานจอดด้วย ','</div>
                            </div>
                            <button type="submit" class="btn btn-primary">สร้างงาน</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>2. กำหนดช่วงวันที่และเวลา</h5>
                    </div>
                    <div class="card-body">
                        <form id="add-slot-form">
                            <input type="hidden" name="action" value="add_slot">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">เลือกงาน</label>
                                <select class="form-select" id="event_id" name="event_id" required>
                                    <option value="">-- กรุณาเลือกงาน --</option>
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="date_from" class="form-label">จากวันที่</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" required>
                                </div>
                                <div class="col">
                                    <label for="date_to" class="form-label">ถึงวันที่</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="time_from" class="form-label">จากเวลา</label>
                                    <input type="time" class="form-control" id="time_from" name="time_from" required>
                                </div>
                                <div class="col">
                                    <label for="time_to" class="form-label">ถึงเวลา</label>
                                    <input type="time" class="form-control" id="time_to" name="time_to" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">เพิ่มช่วงเวลา</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./assets/admin_script.js"></script>
</body>

</html>