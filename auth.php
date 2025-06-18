<?php
@session_status();
// or can used
// if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// or can used
// if(session_status() === PHP_SESSION_NONE) session_start();

// ตรวจสอบว่ามีการ login เข้ามาแล้วหรือยังจาก $_SESSION['user_id']
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    // หรือ header('Location: index.php'); //ขึ้นอยู่กับว่าจะให้ไปที่หน้า login ที่ไหน
}

// ตรวจสอบว่ามีการ login เข้ามาแล้วหรือยังจาก $_SESSION['login_status']
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) :
    $message = "คุณยังไม่ได้ Login!";
    // $_SESSION['message'] = $message;
    echo 'alert(' . $message . '); ';
    header('location: login.php');
    exit;
else :
    // $_SESSION['message'] = "Login already";
endif;