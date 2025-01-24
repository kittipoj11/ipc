<?php
@session_start();

require_once 'config.php';
// require_once 'class/LineLogin.class.php';

// $line = new LineLogin();
// $state = $line->getState();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- <meta name="description" content=""> -->
    <!-- <meta name="author" content=""> -->

    <title>IPC</title>
    <link rel="shortcut icon" href="images/inspection.png" type="image/png">

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
    <link rel="stylesheet" href="css/style.css">

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

    <style>
        * {
            margin: 0;
            padding: 0;
            /* border: 1px red; */
            box-sizing: border-box;
        }

        html {
            /* height: 100vh;
            width: 100%;
            background-color: #fbfbfb; */
            height: 100%;
        }

        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.4),
                    rgba(0, 0, 0, 0.7)),
                url("images/impact_muangthong.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
        }

        .impact-image {
            /* background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7)), url("../_images/impact-muang-thong-thani.png"); */
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7)), url("images/impact_muangthong.jpg");
            /* height: 100%; */
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        /* .impact-image {
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7));
        } */

        #image-sub {
            /* border: 1px solid red; */
            height: 20%;
            /* position: absolute; */
            /* left: 0; */
            /* bottom: 0; */
        }

        @media (min-width: 991.98px) {
            main {
                padding-left: 240px;
            }
        }

        .card-registration .select-input.form-control[readonly]:not([disabled]) {
            font-size: 1rem;
            line-height: 2.15;
            padding-left: .75em;
            padding-right: .75em;
        }

        .card-registration .select-arrow {
            top: 13px;
        }

        .navbar-custom {
            padding-top: 1rem;
            padding-bottom: 1rem;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .navbar-custom .navbar-brand {
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: 0.1rem;
            font-weight: 700;
        }

        .navbar-custom .navbar-nav .nav-item .nav-link {
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.1rem;
        }

        #button {
            background-color: #06C755;
            /* color: white; */
            /* text-decoration: none; */
            cursor: pointer;
            /* max-width: 160px; */
            max-width: 100%;
            height: 36px;
            margin: 0 auto;
            display: flex;
            text-align: center;
            justify-content: center;
            align-items: center;
            border: none;
            border-radius: 5px;
            padding-left: 10px;
        }

        #button_logout {
            background-color: #06C755;
            /* color: white; */
            /* text-decoration: none; */
            cursor: pointer;
            /* max-width: 160px; */
            max-width: 100%;
            height: 36px;
            /* margin: 0 0 50px 0; */
            display: flex;
            text-align: center;
            justify-content: center;
            align-items: center;
            border: none;
            border-radius: 5px;
            padding-left: 10px;
            /* border: 1px solid blue; */
        }

        #button:hover {
            background-color: #07ad4c;
            opacity: 0.9;
        }

        #button:active {
            background-color: #067935;
            opacity: 0.7;
        }

        #img {
            content: url("_images/Line_Login_Button_Image/images/DeskTop/1x/32dp/btn_base.png");
            /* margin: auto auto; */
            margin-right: 10px;
        }

        #button:hover #img {
            content: url("_images/Line_Login_Button_Image/images/DeskTop/1x/32dp/btn_hover.png");
            opacity: 0.9;
        }

        #button:active #img {
            content: url("_images/Line_Login_Button_Image/images/DeskTop/1x/32dp/btn_press.png");
            opacity: 0.7;
        }

        #lineSignin,
        #lineSignup {
            display: flex;
            text-decoration: none;
            /* border: 1px solid red; */
            text-align: center;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .div_text {
            font-family: 'Helvetica', 'sans-serif';
            font-weight: bold;
            font-size: 12px;
            /* background-color: #06C755; */
            color: white;
            /* text-decoration: none; */
            cursor: pointer;
            /* max-width: 160px; */
            max-width: 100%;
            height: 36px;
            /* margin: 0 auto; */
            display: flex;
            text-align: center;
            justify-content: center;
            align-items: center;
            border: none;

            /* border-radius: 5px; */
            /* border: 1px solid red; */
        }
    </style>
</head>


<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container px-5">
            <a class="navbar-brand" href="http://www.impact.co.th/">
                <img src="images/logo-impact.svg" alt="Impact" width="100px" class="d-inline-block align-text-top">
            </a>

            <!-- Hamburger bar -->
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button> -->

            <!-- <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#!" data-bs-toggle="modal"
                            data-bs-target="#registrationForm">Sign Up</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!" data-bs-toggle="modal"
                            data-bs-target="#loginForm">Sign In</a></li>
                </ul>
            </div> -->
        </div>
    </nav>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-5">
                    <h2 class="heading-section">Login</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="login-wrap p-0">
                        <form action="#" class="signin-form">
                            <div class="form-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Username"
                                    required />
                            </div>
                            <div class="form-group">
                                <input
                                    id="password-field"
                                    type="password"
                                    class="form-control"
                                    placeholder="Password"
                                    required />
                                <span
                                    toggle="#password-field"
                                    class="fa fa-fw fa-eye field-icon toggle-password"></span>
                            </div>
                            <div class="form-group">
                                <button
                                    type="submit"
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
                                    <a href="#" style="color: #fff">Forgot Password</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

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