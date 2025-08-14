<?php
@session_start();
require_once  'class/connection_class.php';
require_once  'class/menu_class.php';

$connection=new Connection;
$pdo=$connection->getDbConnection();
$menu = new Menu_Item($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'System Admin') {
    die("Access Denied.");
}

// --- ดึงข้อมูลและฟังก์ชัน ---

// 1. ดึงเมนูทั้งหมดมาสร้างเป็น Tree
$all_menus_raw = $menu->getAll();
$all_menus_structured = [];
$itemsById = [];
foreach ($all_menus_raw as $item) {
    $itemsById[$item['id']] = $item;
    $itemsById[$item['id']]['sub_menus'] = [];
}
foreach ($itemsById as $id => &$item) {
    if ($item['parent_id'] !== null && isset($itemsById[$item['parent_id']])) {
        $itemsById[$item['parent_id']]['sub_menus'][] = &$item;
    } else {
        $all_menus_structured[] = &$item;
    }
}
unset($item);

// 2. ฟังก์ชันสำหรับแสดงโครงสร้างเมนู
function display_menu_tree($menus)
{
    echo '<ul class="menu-tree">';
    foreach ($menus as $menu) {
        echo '<li>';
        echo '<div class="menu-info">';
        echo '<i class="icon ' . htmlspecialchars($menu['icon']) . '"></i>';
        echo '<span class="menu-title">' . htmlspecialchars($menu['title']) . '</span>';
        echo '<span class="menu-url">(' . htmlspecialchars($menu['url']) . ')</span>';
        echo '</div>';
        echo '<div class="menu-actions">';
        echo '<a href="?action=add_submenu&parent_id=' . $menu['id'] . '">Add Sub</a>';
        echo '<a href="?action=edit&id=' . $menu['id'] . '">Edit</a>';
        echo '<a href="menu_process.php?action=delete&id=' . $menu['id'] . '" class="action-delete" onclick="return confirm(\'แน่ใจหรือไม่? การลบเมนูนี้จะลบเมนูย่อยทั้งหมดด้วย\')">Delete</a>';
        echo '</div>';
        echo '</li>';

        if (!empty($menu['sub_menus'])) {
            echo '<li><ul>';
            display_menu_tree($menu['sub_menus']);
            echo '</ul></li>';
        }
    }
    echo '</ul>';
}

// 3. เตรียมข้อมูลสำหรับฟอร์ม Add/Edit
$edit_mode = false;
$menu_data = ['id' => '', 'parent_id' => '', 'title' => '', 'url' => '#', 'icon' => 'fa-solid fa-circle', 'order_num' => 0];
$form_action = 'add';
$form_title = 'เพิ่มเมนูหลักใหม่ (Top-Level Menu)';

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'edit' && isset($_GET['id'])) {
        $edit_mode = true;
        $form_action = 'edit';
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $menu_data = $stmt->fetch();
        $form_title = 'แก้ไขเมนู: ' . htmlspecialchars($menu_data['title']);
    } elseif ($_GET['action'] === 'add_submenu' && isset($_GET['parent_id'])) {
        $edit_mode = true;
        $menu_data['parent_id'] = $_GET['parent_id'];
        $form_title = 'เพิ่มเมนูย่อย';
    }
}

// 4. ฟังก์ชันสำหรับสร้าง Dropdown เลือก Parent
function build_parent_dropdown($menus, $current_parent_id, $prefix = '')
{
    foreach ($menus as $menu) {
        $selected = ($menu['id'] == $current_parent_id) ? 'selected' : '';
        echo '<option value="' . $menu['id'] . '" ' . $selected . '>' . $prefix . htmlspecialchars($menu['title']) . '</option>';
        if (!empty($menu['sub_menus'])) {
            build_parent_dropdown($menu['sub_menus'], $current_parent_id, $prefix . '&nbsp;&nbsp;&ndash; ');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการเมนู</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="admin-container">
        <h1>จัดการเมนู</h1>
        <a href="../dashboard.php">&laquo; กลับไปหน้า Dashboard</a>

        <div class="admin-form">
            <h3><i class="fa-solid fa-pen-to-square"></i> <?php echo $form_title; ?></h3>
            <form action="menu_process.php" method="post">
                <input type="hidden" name="action" value="<?php echo $form_action; ?>">
                <input type="hidden" name="id" value="<?php echo $menu_data['id']; ?>">

                <div class="form-group">
                    <label for="parent_id">เมนูหลัก (Parent)</label>
                    <select id="parent_id" name="parent_id">
                        <option value="">-- เป็นเมนูหลัก (Top-Level) --</option>
                        <?php build_parent_dropdown($all_menus_structured, $menu_data['parent_id']); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">ชื่อเมนู (Title)</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($menu_data['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="text" id="url" name="url" value="<?php echo htmlspecialchars($menu_data['url']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="icon">คลาสไอคอน (Icon Class)</label>
                    <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($menu_data['icon']); ?>">
                </div>
                <div class="form-group">
                    <label for="order_num">ลำดับ (Order)</label>
                    <input type="number" id="order_num" name="order_num" value="<?php echo htmlspecialchars($menu_data['order_num']); ?>" required>
                </div>
                <button type="submit">บันทึก</button>
                <?php if ($edit_mode): ?>
                    <a href="manage_menus.php" style="margin-left:10px;">ยกเลิก</a>
                <?php endif; ?>
            </form>
        </div>

        <h3><i class="fa-solid fa-list-check"></i> โครงสร้างเมนูทั้งหมด</h3>
        <?php display_menu_tree($all_menus_structured); ?>

    </div>
</body>

</html>