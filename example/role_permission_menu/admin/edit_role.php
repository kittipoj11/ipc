<?php
session_start();
require '../db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'System Admin' || !isset($_GET['id'])) { die("Access Denied."); }

$role_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
$stmt->execute([$role_id]);
$role = $stmt->fetch();
if (!$role) { die("Role not found."); }

$all_menus_raw = $pdo->query("SELECT * FROM menu_items ORDER BY parent_id, order_num")->fetchAll();
$all_menus_structured = [];
$itemsById = [];
foreach ($all_menus_raw as $item) { $itemsById[$item['id']] = $item; $itemsById[$item['id']]['sub_menus'] = []; }
foreach ($itemsById as $id => &$item) {
    if ($item['parent_id'] !== null && isset($itemsById[$item['parent_id']])) { $itemsById[$item['parent_id']]['sub_menus'][] = &$item; } 
    else { $all_menus_structured[] = &$item; }
}
unset($item);

$stmt = $pdo->prepare("SELECT menu_item_id FROM role_menu_permissions WHERE role_id = ?");
$stmt->execute([$role_id]);
$current_permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

function display_menu_checkboxes($menus, $current_permissions) {
    echo '<ul>';
    foreach ($menus as $menu) {
        $checked = in_array($menu['id'], $current_permissions) ? 'checked' : '';
        echo '<li><label><input type="checkbox" name="permissions[]" value="' . $menu['id'] . '" ' . $checked . '> ' . htmlspecialchars($menu['title']) . '</label>';
        if (!empty($menu['sub_menus'])) { display_menu_checkboxes($menu['sub_menus'], $current_permissions); }
        echo '</li>';
    }
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไข Role</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="admin-container">
    <h1>แก้ไข Role: <?php echo htmlspecialchars($role['role_name']); ?></h1>
    <a href="manage_roles.php">&laquo; กลับไปหน้ารายการ</a>
    <div class="admin-form">
        <form action="role_process.php" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
            <div class="form-group">
                <label for="role_name">ชื่อ Role:</label>
                <input type="text" id="role_name" name="role_name" value="<?php echo htmlspecialchars($role['role_name']); ?>" required>
            </div>
            <div class="form-group">
                <h3>สิทธิ์การเข้าถึงเมนู</h3>
                <div class="permissions-tree"><?php display_menu_checkboxes($all_menus_structured, $current_permissions); ?></div>
            </div>
            <button type="submit">บันทึกการเปลี่ยนแปลง</button>
        </form>
    </div>
</div>
</body>
</html>