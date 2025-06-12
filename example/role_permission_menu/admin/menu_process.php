<?php
session_start();
require '../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Admin' || !isset($_POST['action']) && !isset($_GET['action'])) {
    die("Access Denied or Invalid Action.");
}

$action = $_POST['action'] ?? $_GET['action'];

switch ($action) {
    case 'add':
    case 'edit':
        $parent_id = empty($_POST['parent_id']) ? null : (int)$_POST['parent_id'];
        $title = trim($_POST['title']);
        $url = trim($_POST['url']);
        $icon = trim($_POST['icon']);
        $order_num = (int)$_POST['order_num'];
        $id = $_POST['id'] ?? null;
        
        // ป้องกันการเลือกตัวเองเป็น Parent
        if ($id && $id == $parent_id) {
            die("Error: A menu cannot be its own parent.");
        }

        if ($action === 'add') {
            $sql = "INSERT INTO menu_items (parent_id, title, url, icon, order_num) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$parent_id, $title, $url, $icon, $order_num]);
        } else { // edit
            $sql = "UPDATE menu_items SET parent_id = ?, title = ?, url = ?, icon = ?, order_num = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$parent_id, $title, $url, $icon, $order_num, $id]);
        }
        break;

    case 'delete':
        $id = $_GET['id'];
        // การตั้งค่า ON DELETE CASCADE ในฐานข้อมูลจะช่วยลบเมนูย่อยทั้งหมดอัตโนมัติ
        $sql = "DELETE FROM menu_items WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        break;
}

// เมื่อประมวลผลเสร็จ ให้กลับไปหน้าจัดการเมนู
header("Location: manage_menus.php");
exit();