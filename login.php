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
    <link rel="stylesheet" href="css/signinup.css">

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
            <div class="col-md-6 col-lg-4">
                <div class="login-wrap p-0">
                    <div class="logo">
                        <img src="images/logo-impact.svg" alt="">
                    </div>
                    <div class="text-center mt-4 name">
                        IPC
                    </div>
                    <form name="frmLogin" id="frmLogin" action="" method="" class="signin-form">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Username" name="username" id="username" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <input type="password" class="form-control me-1" placeholder="Password" name="password" id="password" required>
                            <i class="fa fa-eye" id="togglePassword"
                                style="cursor: pointer"></i>
                        </div>

                        <div class="form-group">
                            <button type="submit" name="login" id="login" class="btn mt-3">Login</button>
                        </div>
                        <div class="form-group d-md-flex">
                            <div class="text-center fs-6 fw-bold" style="color: white">
                                <a href="#" style="color: gold">Forget password?</a> or <a href="signup.php" style="color: gold">Sign up</a>
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

        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {

            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // toggle the eye icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>