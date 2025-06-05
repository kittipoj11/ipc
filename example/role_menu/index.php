<?php
require 'partials/auth_check.php'; // ตรวจสอบ Login ก่อน
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css"> </head>
<body>
    <?php include 'partials/menu.php'; ?>

    <div class="content">
        <h1>Welcome to the Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Your role is: <strong><?php echo htmlspecialchars($_SESSION['role_name']); ?></strong>.</p>
        <p>Please use the menu above to navigate.</p>
    </div>

    </body>
</html>