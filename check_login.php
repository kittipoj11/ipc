<?php
@session_start();
require_once 'config.php';
require_once 'class/user_class.php';

$user = new User;

$result = $user->checkLogin($_POST['username'],$_POST['password']);
$_SESSION['result'] = $result;
$_SESSION['menu1'] = 'd-none';
// Check

echo $result;

?>