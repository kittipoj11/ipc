<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];
    // ในระบบจริงต้องมีการตรวจสอบ password_hash
    // ที่นี่เราจะ query role โดยตรงจาก username ที่จำลองไว้
    $stmt = $pdo->prepare("SELECT u.user_id, u.username, r.role_name, r.role_id
                           FROM users u
                           JOIN roles r ON u.role_id = r.role_id
                           WHERE u.username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username (for demo purpose, no password check). Try: admin01, user01, manager01";
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // ถ้า login แล้วให้ไปหน้า index
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f0f0; }
        .login-container { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label, input, button { display: block; margin-bottom: 10px; width: 200px; }
        input { padding: 8px; }
        button { padding: 10px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login (Demo)</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <p style="font-size:0.8em;">(Try: admin01, user01, manager01)</p>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>