<?php
session_start();

require_once  'class/menu_class.php';
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 1; // Default role เป็น 1
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 1; // Default role เป็น 1

$menu = new Menu;
$rsMenu = $menu->getMenuByUsername($username);

// ในระบบจริง คุณจะต้องดึง role_id จาก session
// นี่คือการจำลอง role_id เพื่อให้ทดสอบได้
// $role_id = 1; // สมมติผู้ใช้มีบทบาท ID เป็น 1

$menus = array();

foreach($rsMenu as $row){
    $menus[$row['menu_name']] = array('menu_name' => $row['menu_name'], 'link' => '#', 'content_filename' => $row['content_filename'], 'function_name' => $row['function_name']);
}

if ($role_id == 1) {
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php', 'init_script' => 'initContent1');
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content' => 'content2.php', 'init_script' => 'initContent2');
} elseif ($role_id == 2) {
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content' => 'content2.php', 'init_script' => 'initContent2');
} else {
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php', 'init_script' => 'initContent1');
}

echo '<ul>';
foreach ($menus as $menu) {
    echo '<li><a href="' . $menu['link'] . '" data-content="' . $menu['content'] . '" data-init="' . $menu['init_script'] . '">' . $menu['name'] . '</a></li>';
}
echo '</ul>';
?>


<!-- 
$menus = array();

if ($role_id == 1) {
    // บทบาท 1 สามารถเข้าถึงเมนู 1 และ 2
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php', 'init_script' => 'initContent');
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content' => 'content2.php', 'init_script' => 'initContent');
    $menus[] = array('name' => 'เมนูที่ 3', 'link' => '#', 'content' => 'content3.php', 'init_script' => 'initContent');
} elseif ($role_id == 2) {
    // บทบาท 2 สามารถเข้าถึงเมนู 2 และ 3
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content' => 'content2.php', 'init_script' => 'initContent');
    $menus[] = array('name' => 'เมนูที่ 3', 'link' => '#', 'content' => 'content3.php', 'init_script' => 'initContent');
} elseif ($role_id == 3) {
    // บทบาท 2 สามารถเข้าถึงเมนู 2 และ 3
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php', 'init_script' => 'initContent');
    $menus[] = array('name' => 'เมนูที่ 3', 'link' => '#', 'content' => 'content3.php', 'init_script' => 'initContent');
} else {
    // บทบาทอื่นๆ เข้าถึงได้แค่เมนู 1
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php', 'init_script' => 'initContent');
}

SELECT
U.user_id,
U.username,
P.permission_id,
P.permission_name,
CASE
WHEN RP.permission_id IS NOT NULL THEN 'yes'
ELSE 'no'
END AS role_status
FROM users U
CROSS JOIN permissions P
LEFT JOIN role_permissions RP
ON RP.role_id = U.role_id
AND RP.permission_id = P.permission_id
ORDER BY U.user_id, P.permission_id; -->
