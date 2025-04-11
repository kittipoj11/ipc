<?php
session_start();

// ในระบบจริง คุณจะต้องดึง role_id จาก session
// นี่คือการจำลอง role_id เพื่อให้ทดสอบได้
$role_id = 1; // สมมติผู้ใช้มีบทบาท ID เป็น 1

$menus = array();

if ($role_id == 1) {
    // บทบาท 1 สามารถเข้าถึงเมนู 1 และ 2
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php');
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content' => 'content2.php');
    $menus[] = array('name' => 'เมนูที่ 3', 'link' => '#', 'content' => 'content3.php');
} elseif ($role_id == 2) {
    // บทบาท 2 สามารถเข้าถึงเมนู 2 และ 3
    $menus[] = array('name' => 'เมนูที่ 2', 'link' => '#', 'content' => 'content2.php');
    $menus[] = array('name' => 'เมนูที่ 3', 'link' => '#', 'content' => 'content3.php');
} elseif ($role_id == 3) {
    // บทบาท 2 สามารถเข้าถึงเมนู 2 และ 3
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php');
    $menus[] = array('name' => 'เมนูที่ 3', 'link' => '#', 'content' => 'content3.php');
} else {
    // บทบาทอื่นๆ เข้าถึงได้แค่เมนู 1
    $menus[] = array('name' => 'เมนูที่ 1', 'link' => '#', 'content' => 'content1.php');
}

echo '<ul>';
foreach ($menus as $menu) {
    echo '<li><a href="' . $menu['link'] . '" data-content="' . $menu['content'] . '">' . $menu['name'] . '</a></li>';
}
echo '</ul>';
?>
