<?php
// forgotpassword.php

session_start();

// Include Database Connection
include('db_connection.php');

$error = "";

if (isset($_POST['check_email'])) {

    $email = trim($_POST['email']);

    // Check Email

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        // Save Email In Session

        $_SESSION['reset_email'] = $email;

        // Redirect To Update Password Page

        header("Location: updatepassword.php");
        exit();
    } else {

        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <!-- Local Bootstrap CSS -->

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>Forgot Password</title>

</head>

<body>

    <!-- Header -->

    <header class="main-header">

        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center">

                <!-- Left Text -->

                <div class="header-title">

                    Research Management of Information and Communication Technology Faculty

                </div>

                <!-- Right Button -->

                <a href="index.php" class="btn home-btn">

                    Home Page

                </a>

            </div>

        </div>

    </header>

    <!-- Main Container -->

    <div class="main-container">

        <!-- Forgot Password Card -->

        <div class="forgot-card">

            <!-- Title -->

            <h2 class="forgot-title">

                Forgot Password

            </h2>

            <!-- Error Message -->

            <?php
            if ($error != "") {
                echo "<div class='alert alert-danger text-center'>$error</div>";
            }
            ?>

            <!-- Form -->

            <form method="POST">

                <!-- Email -->

                <div class="mb-3">

                    <label class="form-label">

                        Email Address

                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control custom-input"
                        placeholder="Enter your email"
                        required>

                </div>

                <!-- Button -->

                <button
                    type="submit"
                    name="check_email"
                    class="btn btn-success w-100 continue-btn">

                    Continue

                </button>

            </form>

        </div>

    </div>

    <!-- Footer -->

    <footer class="main-footer">

        &copy; <?php echo date("Y"); ?>
        Information & Communication Technology Faculty of Kabul University

    </footer>

    <!-- Bootstrap JS -->

    <script src="css/bootstrap.bundle.min.js"></script>

</body>

</html>