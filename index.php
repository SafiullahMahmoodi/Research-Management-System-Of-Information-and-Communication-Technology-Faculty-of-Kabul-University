<?php
// index.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Management System</title>

    <!-- Bootstrap Local CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        body{
            min-height:100vh;
            display:flex;
            flex-direction:column;
            background:#f4f6f9;
        }

        /* Header */

        .main-header{

            background:#0f9d58;
            color:white;
            padding:18px 10px;
            text-align:center;
            font-size:28px;
            font-weight:bold;
            box-shadow:0 2px 10px rgba(0,0,0,0.15);
        }

        /* Hero Section */

        .hero-section{

            flex:1;
            position:relative;

            background:
            linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
            url('images/research-bg.jpg');

            background-size:cover;
            background-position:center;

            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
        }

        /* Content */

        .hero-content{
            text-align:center;
        }

        .hero-title{

            color:white;
            font-size:42px;
            font-weight:bold;
            margin-bottom:20px;
        }

        .hero-text{

            color:#f1f5f9;
            font-size:18px;
            margin-bottom:35px;
        }

        /* Buttons */

        .btn-custom{

            width:180px;
            padding:12px;
            font-size:18px;
            font-weight:600;
            border-radius:10px;
            transition:0.3s;
            margin:10px;
        }

        .btn-signin{

            background:#1d4ed8;
            color:white;
            border:none;
        }

        .btn-signin:hover{

            background:#163fb3;
            transform:translateY(-3px);
            color:white;
        }

        .btn-signup{

            background:#0f9d58;
            color:white;
            border:none;
        }

        .btn-signup:hover{

            background:#0c7c45;
            transform:translateY(-3px);
            color:white;
        }

        /* Footer */

        .main-footer{

            background:#1f2937;
            color:white;
            text-align:center;
            padding:15px;
            font-size:15px;
        }

        /* Responsive */

        @media(max-width:768px){

            .main-header{
                font-size:20px;
                padding:15px;
            }

            .hero-title{
                font-size:28px;
            }

            .hero-text{
                font-size:15px;
            }

            .btn-custom{

                width:100%;
                max-width:250px;
                font-size:16px;
            }
        }

    </style>

</head>
<body>

    <!-- Header -->

    <header class="main-header">

        Research Management System for Information & Communication Technology

    </header>

    <!-- Hero Section -->

    <section class="hero-section">

        <div class="hero-content">

            <h1 class="hero-title">
                Welcome to Research Management System
            </h1>

            <p class="hero-text">
                Manage Researches, Users and Academic Information Easily
            </p>

            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center">

                <a href="login.php" class="btn btn-custom btn-signin">
                    Sign In
                </a>

                <a href="signup.php" class="btn btn-custom btn-signup">
                    Sign Up
                </a>

            </div>

        </div>

    </section>

    <!-- Footer -->

    <footer class="main-footer">

        &copy; <?php echo date("Y"); ?>
        Information & Communication Technology Faculty of Kabul University

    </footer>

    <!-- Bootstrap Local JS -->

    <script src="css/bootstrap.bundle.min.js"></script>

</body>
</html>