<?php
session_start();
require 'db_connection.php';

/**
 * ฟังก์ชันสำหรับดึงเมนูตามสิทธิ์และสร้างเป็น Array แบบมีลำดับชั้น
 * @param PDO $pdo - Object การเชื่อมต่อฐานข้อมูล
 * @param int $role_id - ID ของสิทธิ์ผู้ใช้
 * @return array - Array ของเมนูที่พร้อมใช้งาน
 */
function buildMenuForRole(PDO $pdo, int $role_id): array {
    $sql = "
        SELECT m.*
        FROM menu_items m
        JOIN role_menu_permissions p ON m.id = p.menu_item_id
        WHERE p.role_id = :role_id
        ORDER BY m.parent_id, m.order_num
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['role_id' => $role_id]);
    $menuItems = $stmt->fetchAll();

    // สร้าง Array โดยใช้ ID ของเมนูเป็น Key เพื่อให้ง่ายต่อการค้นหา
    $itemsById = [];
    foreach ($menuItems as $item) {
        $itemsById[$item['id']] = $item;
        $itemsById[$item['id']]['sub_menus'] = []; // เตรียม Array สำหรับเมนูย่อย
    }

    // จัดโครงสร้างแบบลำดับชั้น (parent-child)
    $structuredMenu = [];
    foreach ($itemsById as $id => &$item) { // ใช้ & เพื่ออ้างอิงถึง Array ต้นฉบับ
        if ($item['parent_id'] && isset($itemsById[$item['parent_id']])) {
            $itemsById[$item['parent_id']]['sub_menus'][] = &$item;
        } else {
            $structuredMenu[] = &$item;
        }
    }
    unset($item);
    
    return $structuredMenu;
}


// --- ส่วนประมวลผลการล็อกอิน ---
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// **คำเตือนด้านความปลอดภัย**: ในระบบจริงต้องใช้ password_verify() กับ hash ที่เก็บใน DB
// $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
// $stmt->execute([$username]);
// $user = $stmt->fetch();
// if ($user && password_verify($password, $user['password'])) { ... }

// ตัวอย่างแบบง่าย (ไม่ปลอดภัย)
$stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ? AND u.password = ?");
$stmt->execute([$username, $password]);
$user = $stmt->fetch();

if ($user) {
    // ล็อกอินสำเร็จ
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role_name'] = $user['role_name'];
    
    // สร้างเมนูสำหรับ Role นี้และเก็บลง Session
    $_SESSION['user_menu'] = buildMenuForRole($pdo, $user['role_id']);
    
    // ไปยังหน้า Dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // ล็อกอินไม่สำเร็จ
    echo "Username หรือ Password ไม่ถูกต้อง";
    header("Location: login.html?error=1");
    exit();
}