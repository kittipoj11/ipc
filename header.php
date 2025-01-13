<?php
@session_start();
require_once 'config.php';
require_once 'auth.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>IPC | Test</title>
    <link rel="shortcut icon" href="images/inspection.png" type="image/png">

    <!-- Bootstrap 5.3.3 add by Poj-->
    <link rel="stylesheet" href="plugins/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom fonts for this template-->
    <!-- <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css"> -->
    <link rel="stylesheet" href="plugins/fontawesome-free-6.5.1-web/css/all.min.css" type="text/css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <!-- flaticon dist\bootstrap-icons-1.11.3\font\bootstrap-icons.min.css-->
    <link rel="stylesheet" href="plugins/dist/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <!-- flaticon -->
    <link rel="stylesheet" href="plugins/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/DataTables/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/DataTables/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/DataTables/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="plugins/dist/css/adminlte.min.css">
</head>



<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">


    <!-- Page Wrapper -->
    <div class="wrapper">
