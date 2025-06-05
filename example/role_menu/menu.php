<?php
// session_start() ควรถูกเรียกแล้วจาก auth_check.php หรือหน้าหลัก
require_once __DIR__ . '/../db_connect.php';

function getMenuForRole($pdo, $role_id)
{
  $stmt = $pdo->prepare("SELECT p.page_filename, p.menu_title
                           FROM role_permissions rp
                           JOIN pages p ON rp.page_id = p.page_id
                           WHERE rp.role_id = :role_id
                           ORDER BY p.page_id ASC"); // เรียงตาม page_id หรือ menu_title ก็ได้
  $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$menuItems = [];
if (isset($_SESSION['role_id'])) {
  $menuItems = getMenuForRole($pdo, $_SESSION['role_id']);
}
?>
<nav id="main-menu">
  <ul>
    <li><a href="<?php echo (basename($_SERVER['PHP_SELF']) !== 'index.php' ? '../' : ''); ?>index.php">Home</a></li>
    <?php if (!empty($menuItems)): ?>
      <!-- < ?php foreach ($menuItems as $item): ?>
        <li><a href="< ?php echo (basename($_SERVER['PHP_SELF']) !== 'index.php' ? '../pages/' : 'pages/'); ?><?php echo htmlspecialchars($item['page_filename']); ?>">
            < ?php echo htmlspecialchars($item['menu_title']); ?>
          </a></li>
      < ?php endforeach; ?> -->

      <!-- การใช้ jQuery เพื่อ Highlight Active Menu (ตัวอย่างง่ายๆ) -->
      <?php foreach ($menuItems as $item): ?>
        <li><a class="menu-link" href="<?php echo (basename($_SERVER['PHP_SELF']) !== 'index.php' ? '../pages/' : 'pages/'); ?><?php echo htmlspecialchars($item['page_filename']); ?>">
            <?php echo htmlspecialchars($item['menu_title']); ?>
          </a></li>
      <?php endforeach; ?>

    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
      <li><a href="<?php echo (basename($_SERVER['PHP_SELF']) !== 'index.php' ? '../' : ''); ?>logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
    <?php endif; ?>
  </ul>
</nav>
<style>
  #main-menu ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    background-color: #333;
    overflow: hidden;
  }

  #main-menu li {
    float: left;
  }

  #main-menu li a {
    display: block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
  }

  #main-menu li a:hover {
    background-color: #111;
  }
</style>