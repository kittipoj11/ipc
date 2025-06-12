<?php
session_start();
$_SESSION = array(); // ล้างค่าทั้งหมดใน Session
session_destroy();   // ทำลาย Session
header("Location: login.html");
exit();