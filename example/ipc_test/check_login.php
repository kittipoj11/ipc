<?php
@session_start();
require_once 'config.php';
require_once 'class/user_class.php';

$user = new User;

$result = $user->checkLogin($_POST['username'],$_POST['password']);
$_SESSION['result1'] = $result;
if($result){
    $_SESSION['result2']= $result;
    $permissions = $user->getPermissions();
    foreach($permissions as $row){
        // $_SESSION[$row['menu_name']] = $row['display_status'];
        $_SESSION[$row['menu_name']] = 'd-none';
    }

    $user_permissions = $user->getPermissionByUsername($_POST['username']);
    foreach($user_permissions as $row){
        // $_SESSION[$row['menu_name']] = $row['display_status'];
        $_SESSION[$row['menu_name']] = '';
    }
}

// Check

echo $result;

?>