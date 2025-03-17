<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ไม่พบ ID งาน");
}

$work_item_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

try {
    // ดึงข้อมูลงานและสถานะการอนุมัติปัจจุบันสำหรับผู้ใช้คนนี้
    $stmt = $pdo->prepare("SELECT wi.*, wia.approval_level, wia.approval_status
                           FROM work_items wi
                           JOIN work_item_approvals wia ON wi.id = wia.work_item_id
                           WHERE wi.id = :work_item_id AND wia.approver_id = :user_id
                           AND wi.current_approval_level = wia.approval_level AND wia.approval_status = 'Pending'");
    $stmt->bindParam(':work_item_id', $work_item_id);
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
    $work_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$work_item) {
        echo "<p>ไม่พบงานนี้ หรือไม่ถึงรอบการอนุมัติของคุณ</p>";
        exit;
    }
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูลงาน: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>รายละเอียดงาน</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#approvalForm').submit(function(event) {
                event.preventDefault();

                var formData = $(this).serialize() + '&work_item_id=' + <?php echo $work_item['id']; ?>;
                var action = $('input[name="action"]:checked').val();

                if (action) {
                    $.ajax({
                        type: 'POST',
                        url: 'process_approval.php?action=' + action, // แยกไฟล์สำหรับจัดการการอนุมัติ/ปฏิเสธ
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#message').html('<p style="color: green;">' + response.message + ' <a href="approval_list.php">กลับไปยังรายการรออนุมัติ</a></p>');
                                // อาจจะ disable ปุ่ม หรือ redirect
                            } else {
                                $('#message').html('<p style="color: red;">' + response.message + '</p>');
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#message').html('<p style="color: red;">เกิดข้อผิดพลาดในการดำเนินการ: ' + error + '</p>');
                        }
                    });
                } else {
                    $('#message').html('<p style="color: orange;">กรุณาเลือกการดำเนินการ (อนุมัติ/ปฏิเสธ).</p>');
                }
            });
        });
    </script>
</head>

<body>
    <h1>รายละเอียดงาน</h1>
    <div id="message"></div>
    <h2><?php echo htmlspecialchars($work_item['title']); ?></h2>
    <p><strong>ประเภทงาน:</strong> <?php echo htmlspecialchars($work_item['work_type']); ?></p>
    <p><strong>รายละเอียด:</strong><br><?php echo nl2br(htmlspecialchars($work_item['description'])); ?></p>

    <form id="approvalForm">
        <div>
            <label for="comment">ความคิดเห็น (ถ้ามี):</label><br>
            <textarea id="comment" name="comment"></textarea>
        </div>
        <br>
        <div>
            <input type="radio" id="approve" name="action" value="approve">
            <label for="approve">อนุมัติ</label><br>
            <input type="radio" id="reject" name="action" value="reject">
            <label for="reject">ปฏิเสธ</label>
        </div>
        <br>
        <button type="submit">ยืนยันการดำเนินการ</button>
    </form>
    <p><a href="approval_list.php">กลับไปยังรายการรออนุมัติ</a></p>
</body>

</html>