<?php
// index.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap Local CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        body {
            background-image: url("img/ictimage.jpeg");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .main-header {

            background: #0f9d58;
            color: white;
            padding: 18px 10px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        -->
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