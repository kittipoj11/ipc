<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="main-container">
        <nav class="side-menu">
            <div class="menu-header">
                <h3>ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                <p>(สิทธิ์: <?php echo htmlspecialchars($_SESSION['role_name']); ?>)</p>
            </div>
            <ul>
                <?php foreach ($_SESSION['user_menu'] as $menu_data): 
                //$_SESSION['user_menu'] เมนูสำหรับ role ของ user ที่ login ถูกเก็บลง session 
                //ซึ่งมีการทำงานในไฟล์ login_process.php หลังจาก login ในหน้า login.html    
                ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($menu_data['url']); ?>">
                            <i class="icon <?php echo htmlspecialchars($menu_data['icon']); ?>"></i>
                            <span><?php echo htmlspecialchars($menu_data['title']); ?></span>
                        </a>
                        <?php if (!empty($menu_data['sub_menus'])): ?>
                            <ul class="sub-menu">
                                <?php foreach ($menu_data['sub_menus'] as $submenu_data): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($submenu_data['url']); ?>">
                                            <i class="icon <?php echo htmlspecialchars($submenu_data['icon']); ?>"></i>
                                            <span><?php echo htmlspecialchars($submenu_data['title']); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <?php if ($_SESSION['role_name'] === 'System Admin'): ?>
                    <li class="admin-menu-separator">
                        <hr>
                    </li>
                    <li>
                        <a href="admin/manage_roles.php">
                            <i class="icon fa-solid fa-user-shield"></i>
                            <span>จัดการสิทธิ์ (Roles)</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin/manage_menus.php">
                            <i class="icon fa-solid fa-list-check"></i>
                            <span>จัดการเมนู (Menus)</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="logout.php" class="logout-link">
                        <i class="icon fa-solid fa-right-from-bracket"></i>
                        <span>ออกจากระบบ</span>
                    </a>
                </li>
            </ul>
        </nav>
        <main class="content">
            <h1>หน้าหลัก Dashboard</h1>
            <p>เนื้อหาของหน้าเว็บจะแสดงที่นี่...</p>
            <p>ลองล็อกอินด้วย user ที่มี Role ต่างกันเพื่อดูเมนูที่เปลี่ยนไป</p>
        </main>
    </div>
</body>

</html>