<?php
session_start();
require_once 'config.php';

// สมมติว่ามีการล็อกอินผู้ใช้แล้ว และมี $_SESSION['user_id']
// ในตัวอย่างนี้เราจะกำหนดให้ผู้สร้างเป็น admin โดยตรงเพื่อความง่าย
$_SESSION['user_id'] = 1; // ID ของผู้ใช้ 'admin' จากตาราง users

if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_work_item'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $work_type = $_POST['work_type'];
    $created_by = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO work_items (title, description, work_type, created_by) VALUES (:title, :description, :work_type, :created_by)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':work_type', $work_type);
        $stmt->bindParam(':created_by', $created_by);
        $stmt->execute();
        $work_item_id = $pdo->lastInsertId();

        // ดึงข้อมูลผู้อนุมัติตามลำดับจาก workflow_steps
        $stmtWorkflow = $pdo->prepare("SELECT approver_id, approval_level FROM workflow_steps WHERE workflow_id = (SELECT id FROM workflows WHERE work_type = :work_type) ORDER BY approval_level ASC");
        $stmtWorkflow->bindParam(':work_type', $work_type);
        $stmtWorkflow->execute();
        $workflowSteps = $stmtWorkflow->fetchAll(PDO::FETCH_ASSOC);

        // เพิ่มรายการอนุมัติเริ่มต้นในตาราง work_item_approvals
        foreach ($workflowSteps as $step) {
            $stmtApproval = $pdo->prepare("INSERT INTO work_item_approvals (work_item_id, approval_level, approver_id) VALUES (:work_item_id, :approval_level, :approver_id)");
            $stmtApproval->bindParam(':work_item_id', $work_item_id);
            $stmtApproval->bindParam(':approval_level', $step['approval_level']);
            $stmtApproval->bindParam(':approver_id', $step['approver_id']);
            $stmtApproval->execute();
        }

        echo "<p style='color: green;'>สร้างงานสำเร็จ! <a href='approval_list.php'>ดูรายการรออนุมัติ</a></p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>เกิดข้อผิดพลาดในการสร้างงาน: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>สร้างงานใหม่</title>
</head>

<body>
    <h1>สร้างงานใหม่</h1>
    <form method="post">
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
        <button type="submit" name="submit_work_item">ส่งงาน</button>
    </form>
</body>

</html>