<?php
require_once 'auth_check.php'; // ตรวจสอบสิทธิก่อนเสมอ!
$page_title = "Page C";
require 'header.php';
?>

<h1>Welcome to Page C</h1>
<p>This page is accessible by: <strong>admin, user, management</strong>.</p>
<p>Current user: <?php echo htmlspecialchars($current_username); ?>, Role: <?php echo htmlspecialchars($user_role); ?></p>
<p>Content for Page C...</p>

<?php require 'footer.php'; // (ถ้ามี footer) ?>