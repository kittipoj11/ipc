<?php
session_start();
require_once 'config.php';

// สมมติว่ามีการล็อกอินผู้ใช้แล้ว และมี $_SESSION['user_id']
$_SESSION['user_id'] = 10; // ID ของผู้ใช้ 'admin'

if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

// **ส่วนของ PHP จะถูกย้ายไปจัดการ request แบบ AJAX ในไฟล์อื่น (เช่น process_work_item.php)**
?>

<!DOCTYPE html>
<html>

<head>
    <title>สร้างงานใหม่</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createWorkItemForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'process_work_item.php?action=create', // แยกไฟล์สำหรับจัดการการสร้างงาน
                    data: $(this).serialize(),
                    dataType: 'json', // คาดหวังผลลัพธ์เป็น JSON
                    success: function(response) {
                        if (response.success) {
                            $('#message').html('<p style="color: green;">' + response.message + ' <a href="approval_list.php">ดูรายการรออนุมัติ</a></p>');
                            $('#createWorkItemForm')[0].reset(); // ล้างฟอร์ม
                        } else {
                            $('#message').html('<p style="color: red;">' + response.message + '</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#message').html('<p style="color: red;">เกิดข้อผิดพลาดในการสร้างงาน: ' + error + '</p>');
                    }
                });
            });
        });
    </script>
</head>

<body>
    <h1>สร้างงานใหม่</h1>
    <div id="message"></div>
    <form id="createWorkItemForm">
        <div>
            <label for="title">หัวข้อ:</label><br>
            <input type="text" id="title" name="title" required>
        </div>
        <br>
        <div>
            <label for="description">รายละเอียด:</label><br>
            <textarea id="description" name="description"></textarea>
        </div>
        <br>
        <div>
            <label for="work_type">ประเภทงาน:</label><br>
            <select id="work_type" name="work_type" required>
                <option value="">เลือกประเภทงาน</option>
                <option value="เอกสารทั่วไป">เอกสารทั่วไป</option>
                <option value="คำขอลาพักร้อน">คำขอลาพักร้อน</option>
            </select>
        </div>
        <br>
        <button type="submit">ส่งงาน</button>
    </form>
</body>

</html>