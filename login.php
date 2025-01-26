<?php
@session_start();

require_once 'config.php';
?>

<!doctype html>
<html lang="en">

<head>
    <?php include 'header_main.php'; ?>


    <!-- Bootstrap 5 add by Poj-->
    <link rel="stylesheet" href="plugins/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="plugins/fontawesome-free-6.5.1-web/css/all.min.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- flaticon -->
    <link rel="stylesheet" href="plugins/uicons-regular-rounded/css/uicons-regular-rounded.css">

    <!-- adminlte template-->
    <link rel="stylesheet" href="plugins/dist/css/adminlte.min.css">

    <!-- Sweet Alert2 -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script> -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <!-- <link rel="stylesheet" href="_css/login.css"> -->
    <!-- <link rel="stylesheet" href="css/styles.css" /> -->
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link rel="stylesheet" href="css/login.css">

    <script>
        var check = function() {
            if (document.getElementById('enter_password').value == document.getElementById('confirm_password')
                .value) {
                document.getElementById('message').style.color = 'green';
                document.getElementById('message').innerHTML = '';
            } else {
                document.getElementById('message').style.color = 'red';
                document.getElementById('message').innerHTML = 'not matching';
            }
        }
    </script>

</head>


<body>
    <!-- <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container px-5">
            <a class="navbar-brand" href="http://www.impact.co.th/">
                <img src="images/logo-impact.svg" alt="Impact" width="100px" class="d-inline-block align-text-top">
            </a>
        </div>
    </nav> -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-5">
                <h2 class="heading-section">IPC</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-wrap p-0">
                    <form name="frmLogin" id="frmLogin" action="" method="" class="signin-form">
                        <div class="form-group">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Username"
                                name="username" id="username"
                                required />
                        </div>
                        <div class="form-group">
                            <input
                                type="password"
                                class="form-control"
                                placeholder="Password"
                                name="password" id="password"
                                required />
                        </div>
                        <div class="form-group">
                            <button
                                type="submit"
                                name="login" id="login"
                                class="form-control btn btn-primary submit px-3">
                                Sign In
                            </button>
                        </div>
                        <div class="form-group d-md-flex">
                            <div class="w-50">
                                <label class="checkbox-wrap checkbox-primary">Remember Me
                                    <input type="checkbox" checked />
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="w-50 text-md-right">
                                <a href="#" style="color: #00f">Forgot Password?</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jQuery-3.7.1/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="plugins/bootstrap-5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <!-- <script src="../vendor/jquery-easing/jquery.easing.min.js"></script> -->

    <!-- Custom scripts for all pages-->
    <script src="plugins/dist/js/adminlte.min.js"></script>
    <script src="javascript/signinup.js"></script>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>