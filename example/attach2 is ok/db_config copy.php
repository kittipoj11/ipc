<?php
$host = 'localhost';
$dbname = 'inspection_db';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


/* คำแนะนำ:

สำหรับข้อมูลผู้ใช้ (เช่น username, user ID, สิทธิ์การใช้งาน): Sessions เป็นตัวเลือกที่ดีที่สุด
ตัวอย่างการใช้งาน Session:
page1.php (หน้าเว็บเริ่มต้น session และเก็บค่า):
< ?php
session_start(); // เริ่มต้น session

$_SESSION['username'] = 'JohnDoe'; // เก็บ username ใน session
$_SESSION['user_id'] = 123;       // เก็บ user ID ใน session

echo "Session started and data stored.";
?>

page2.php (หน้าเว็บเรียกใช้ค่าจาก session):
< ?php
session_start(); // เริ่มต้น session (ในทุกหน้าที่ต้องการใช้ session)

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $userId = $_SESSION['user_id'];

    echo "Welcome, " . $username . "! (User ID: " . $userId . ")";
} else {
    echo "No session data found.";
}
?>

สำหรับ preferences ของผู้ใช้ (เช่น theme, ภาษา, การตั้งค่าอื่นๆ): Cookies อาจจะเหมาะสมกว่า เพราะต้องการให้คงอยู่แม้ปิด browser
ตัวอย่างการใช้งาน Cookies:
set_cookie.php (หน้าเว็บตั้งค่า Cookie):
< ?php
$cookie_name = "user_preference";
$cookie_value = "dark_theme";
$cookie_expiry = time() + (86400 * 30); // Cookie มีอายุ 30 วัน

setcookie($cookie_name, $cookie_value, $cookie_expiry, "/"); // ตั้งค่า cookie

echo "Cookie set successfully.";
?>

get_cookie.php (หน้าเว็บเรียกใช้ค่าจาก Cookie):
< ?php
if (isset($_COOKIE["user_preference"])) {
    $preference = $_COOKIE["user_preference"];
    echo "User preference: " . $preference;
} else {
    echo "Cookie 'user_preference' is not set.";
}
?>

สำหรับค่า configuration ของระบบ (เช่น URL เว็บไซต์, ข้อมูล database): Constants เป็นทางเลือกที่ดี เพราะเป็นค่าที่ไม่เปลี่ยนแปลง และต้องการให้เข้าถึงได้ง่ายจากทุกส่วนของโค้ด
การกำหนด Constant: ใช้ฟังก์ชัน define()
< ?php
define("SITE_URL", "https://www.example.com");
define("DB_HOST", "localhost");
define("DB_USER", "username");
define("DB_PASSWORD", "password");

echo "Site URL: " . SITE_URL;
echo "<br>Database Host: " . DB_HOST;
?>

สำหรับข้อมูลที่ต้องแชร์ระหว่างผู้ใช้ หรือข้อมูลจำนวนมากที่ต้องการ persistence: Database เป็นสิ่งที่หลีกเลี่ยงไม่ได้ */

?>