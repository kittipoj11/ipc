<?php
session_start();
require '../db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Admin' || (!isset($_POST['action']) && !isset($_GET['action']))) {
    die("Access Denied or Invalid Action.");
}

$action = $_POST['action'] ?? $_GET['action'];

switch ($action) {
    case 'add':
        $role_name = trim($_POST['role_name'] ?? '');
        if (!empty($role_name)) {
            $stmt = $pdo->prepare("INSERT INTO roles (role_name) VALUES (?)");
            $stmt->execute([$role_name]);
        }
        break;
    case 'update':
        $role_id = $_POST['role_id'];
        $role_name = trim($_POST['role_name']);
        $permissions = $_POST['permissions'] ?? [];
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE roles SET role_name = ? WHERE id = ?");
            $stmt->execute([$role_name, $role_id]);
            $stmt = $pdo->prepare("DELETE FROM role_menu_permissions WHERE role_id = ?");
            $stmt->execute([$role_id]);
            if (!empty($permissions)) {
                $stmt = $pdo->prepare("INSERT INTO role_menu_permissions (role_id, menu_item_id) VALUES (?, ?)");
                foreach ($permissions as $menu_id) { $stmt->execute([$role_id, (int)$menu_id]); }
            }
            $pdo->commit();
        } catch (Exception $e) { $pdo->rollBack(); die("เกิดข้อผิดพลาดในการบันทึก: " . $e->getMessage()); }
        break;
    case 'delete':
        $role_id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
        $stmt->execute([$role_id]);
        $user_count = $stmt->fetchColumn();
        if ($user_count > 0) { die("ไม่สามารถลบ Role นี้ได้ เนื่องจากยังมีผู้ใช้งานในระบบ"); } 
        else { $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?"); $stmt->execute([$role_id]); }
        break;
}
header("Location: manage_roles.php");
exit();