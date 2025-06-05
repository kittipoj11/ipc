<?php
// session_start(); // auth_check.php เรียกแล้ว หรือถ้าหน้านั้นๆ ไม่ได้ require auth_check.php ก็ต้องเรียก
if (!isset($_SESSION['user_role'])) {
    // กรณีนี้อาจจะไม่ควรเกิดขึ้นถ้าทุกหน้ามีการ check แต่ใส่ไว้กันเหนียว
    // หรืออาจจะแสดงเมนูแบบ guest
    // header('Location: login.php');
    // exit;
}
$user_role_for_js = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "My Application"; ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: sans-serif; margin: 0; }
        header { background-color: #333; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        header nav a { color: white; margin: 0 10px; text-decoration: none; }
        header nav a:hover { text-decoration: underline; }
        .user-info { font-size: 0.9em; }
        .content { padding: 20px; }
        .nav-item { display: inline; } /* Default display state for JS */
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <span class="nav-item" data-page="A.php"><a href="A.php">Page A</a></span>
            <span class="nav-item" data-page="B.php"><a href="B.php">Page B</a></span>
            <span class="nav-item" data-page="C.php"><a href="C.php">Page C</a></span>
        </nav>
        <div class="user-info">
            <?php if (isset($_SESSION['username'])): ?>
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['user_role']); ?>) | <a href="logout.php" style="color: #ffc107;">Logout</a>
            <?php else: ?>
                <a href="login.php" style="color: #007bff;">Login</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="content">
    <script>
        // ส่งค่า user_role จาก PHP มาให้ JavaScript
        const currentUserRole = '<?php echo $user_role_for_js; ?>';

        // กำหนดสิทธิการเข้าถึงหน้าต่างๆ สำหรับแต่ละ Role (Client-side)
        // ข้อมูลนี้ควรจะตรงกับที่ตั้งค่าใน database `role_permissions`
        // การทำแบบนี้ซ้ำซ้อน แต่ทำให้ JS สามารถทำงานได้โดยไม่ต้อง query ใหม่
        const pageAccessRules = {
            'admin': ['A.php', 'B.php', 'C.php'],
            'user': ['B.php', 'C.php'],
            'management': ['C.php'],
            '': [] // Guest or no role
        };

        $(document).ready(function() {
            if (currentUserRole) {
                const allowedPages = pageAccessRules[currentUserRole] || [];
                console.log("User Role:", currentUserRole, "Allowed Pages (JS):", allowedPages);

                $('.nav-item').each(function() {
                    const pageFile = $(this).data('page');
                    if (allowedPages.includes(pageFile)) {
                        $(this).show();
                    } else {
                        $(this).hide(); // ซ่อน link ถ้าไม่มีสิทธิ
                    }
                });
            } else {
                // ถ้าไม่ได้ login หรือไม่มี role ซ่อนทุกหน้าที่ต้องการสิทธิ
                 $('.nav-item[data-page="A.php"]').hide();
                 $('.nav-item[data-page="B.php"]').hide();
                 // C.php อาจจะยังแสดงได้ถ้าเป็นหน้าสาธารณะ หรือซ่อนทั้งหมด
                 // $('.nav-item[data-page="C.php"]').hide();
            }
        });
    </script>