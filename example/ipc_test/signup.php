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
                <div class="signup-wrap p-3">
                    <div class="logo">
                        <img src="images/logo-impact.svg" alt="">
                    </div>
                    <div class="text-center mt-4 name">
                        สร้างบัญชีผู้ใช้ใหม่
                    </div>
                    <form name="frmMemberRegistration" id="frmMemberRegistration" action="" method=""
                        class="row g-3 needs-validation" novalidate>
                        <!-- <input type="hidden" name="action" id="" value='insertdata'> -->
                        <!-- <div class="col-sm-12 d-none d-md-block" id="image-sub" style="border:1px solid gray;"> -->

                        <!-- <div class="col-sm-12"> -->
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12 mb-1">
                                    <div class="input-group input-group-sm mb-2">
                                        <label class="input-group-text" for="firstname">Firstname</label>
                                        <input type="text" name="firstname" id="firstname"
                                            class="form-control form-control-sm" required />
                                        <div class="invalid-feedback">
                                            Please enter a firstname.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 mb-1">
                                    <div class="input-group input-group-sm mb-2">
                                        <label class="input-group-text" for="lastname">Lastname</label>
                                        <input type="text" name="lastname" id="lastname"
                                            class="form-control form-control-sm" required />
                                        <div class="invalid-feedback">
                                            Please enter a lastname.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 mb-1">
                                    <div class="input-group input-group-sm mb-2">
                                        <label class="input-group-text" for="username">Username</label>
                                        <input type="text" name="username" id="username"
                                            class="form-control form-control-sm" required />
                                        <div class="invalid-feedback">
                                            Please enter a username.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 mb-1">
                                    <div class="input-group input-group-sm mb-2">
                                        <label class="input-group-text" for="password">Password</label>
                                        <!-- <input type="password" name="password" id="password" class="form-control form-control-sm" onkeyup='check();' required /> -->
                                        <input type="text" name="enter_password" id="enter_password"
                                            class="form-control form-control-sm" onkeyup='check();' required />
                                        <div class="invalid-feedback">
                                            Please enter a password.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 mb-1">
                                    <div class="input-group input-group-sm mb-2">
                                        <label class="input-group-text" for="confirm_password">Confirm
                                            password</label>
                                        <!-- <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-sm" onkeyup='check();' required /> -->
                                        <input type="text" name="confirm_password" id="confirm_password"
                                            class="form-control form-control-sm" onkeyup='check();' required />
                                        <div id='message' class="invalid-feedback">
                                            Please enter a confirm password.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-outline my-3">
                                <!-- Submit button -->
                                <button type="submit" name="login" id="login"
                                    class="btn btn-primary btn-block form-control">สมัครสมาชิก</button>
                            </div>
                            <div class="text-center fs-6" style="color: black">
                                Already have an account?<a href="login.php" class="text-primary fw-bold">Login</a>
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