<?php
require '../partials/auth_check.php'; // เรียกใช้ auth_check.php จาก parent directory

// ตรวจสอบสิทธิ์สำหรับหน้านี้โดยเฉพาะ
$currentPageFilename = basename(__FILE__); // A.php
if (!hasAccessToPage($pdo, $_SESSION['role_id'], $currentPageFilename)) {
    // ถ้าไม่มีสิทธิ์ อาจจะ redirect หรือแสดงข้อความ
    // header("Location: ../index.php"); // หรือหน้า access_denied.php
    // exit();
    die("<h1>Access Denied</h1><p>You do not have permission to view this page.</p><p><a href='../index.php'>Go to Dashboard</a></p>");
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page A</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../partials/menu.php'; // เรียกใช้ menu.php จาก parent directory ?>

    <div class="content">
        <h1>Page A</h1>
        <p>This content is for <strong>Admin</strong> role only.</p>
        <p>Current User: <?php echo htmlspecialchars($_SESSION['username']); ?>, Role: <?php echo htmlspecialchars($_SESSION['role_name']); ?></p>
    </div>
</body>
</html>

<!-- pages/B.php และ pages/C.php: สร้างคล้ายกับ A.php แต่เปลี่ยนเนื้อหา และตรวจสอบสิทธิ์ตาม basename(__FILE__) ของแต่ละหน้า -->