<?php
session_start();
$_SESSION = array(); // ล้างค่าทั้งหมดใน Session
// remove all session variables
session_destroy();
// session_unset();
// $_SESSION = [];

header("location: login.php");
exit();
