<?php
session_start();

// ในระบบจริง คุณจะต้องดึง role_id จาก session
// นี่คือการจำลอง role_id เพื่อให้ทดสอบได้
// $role_id = 1; // สมมติผู้ใช้มีบทบาท ID เป็น 1
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 1; // Default role เป็น 1

$menus = array();

if ($role_id == 1) {
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content_filename' => 'content1.php', 'function_name' => 'initContent1');
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content_filename' => 'content2.php', 'function_name' => 'initContent2');
} elseif ($role_id == 2) {
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content_filename' => 'content2.php', 'function_name' => 'initContent2');
} else {
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content_filename' => 'content1.php', 'function_name' => 'initContent1');
}

echo '<ul>';
foreach ($menus as $menu) {
    echo '<li><a href="' . $menu['link'] . '" data-content_filename="' . $menu['content_filename'] . '" data-function_name="' . $menu['function_name'] . '">' . $menu['name'] . '</a></li>';
}
echo '</ul>';
?>


<!-- SELECT
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