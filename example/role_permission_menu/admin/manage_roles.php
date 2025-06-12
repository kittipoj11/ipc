<?php
session_start();
require '../db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Admin') {
    die("Access Denied.");
}
$roles = $pdo->query("SELECT * FROM roles ORDER BY role_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสิทธิ์ (Roles)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="admin-container">
    <h1>จัดการสิทธิ์ (Roles)</h1>
    <a href="../dashboard.php">&laquo; กลับไปหน้า Dashboard</a>

    <div class="admin-form">
        <h3><i class="fa-solid fa-plus"></i> เพิ่ม Role ใหม่</h3>
        <form action="role_process.php" method="post">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label for="role_name">ชื่อ Role:</label><input type="text" id="role_name" name="role_name" required></div>
            <button type="submit">เพิ่ม Role</button>
        </form>
    </div>

    <h3><i class="fa-solid fa-list"></i> Roles ทั้งหมด</h3>
    <table class="admin-table">
        <thead><tr><th>ID</th><th>ชื่อ Role</th><th>จัดการ</th></tr></thead>
        <tbody>
            <?php foreach ($roles as $role): ?>
            <tr>
                <td><?php echo $role['id']; ?></td>
                <td><?php echo htmlspecialchars($role['role_name']); ?></td>
                <td>
                    <a href="edit_role.php?id=<?php echo $role['id']; ?>"><i class="fa-solid fa-pen-to-square"></i> แก้ไข</a>
                    <?php if ($role['role_name'] !== 'Admin'): ?>
                    <a href="role_process.php?action=delete&id=<?php echo $role['id']; ?>" class="action-delete" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบ Role นี้?');"><i class="fa-solid fa-trash"></i> ลบ</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>