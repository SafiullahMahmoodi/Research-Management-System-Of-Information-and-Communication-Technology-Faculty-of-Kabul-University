<?php
// updatepassword.php

session_start();

// Include Database Connection
include('db_connection.php');

$message = "";
$error   = "";

if (isset($_POST['update_password'])) {

    $email            = trim($_POST['email']);
    $new_password     = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check Password Match

    if ($new_password != $confirm_password) {

        $error = "Passwords do not match!";
    } else {

        // Check Email Exists

        $check = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {

            // Hash New Password

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update Password

            $sql = "UPDATE users
                    SET password='$hashed_password'
                    WHERE email='$email'";

            if ($conn->query($sql) === TRUE) {

                $message = "Password Updated Successfully!";
            } else {

                $error = "Something went wrong!";
            }
        } else {

            $error = "Email not found!";
        }
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

    <title>Update Password</title>



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

        <!-- Update Password Card -->

        <div class="update-card">

            <!-- Title -->

            <h2 class="update-title">

                Update Password

            </h2>

            <!-- Error & Success Message -->

            <?php
            if ($error != "") {
                echo "<div class='alert alert-danger text-center'>$error</div>";
            }

            if ($message != "") {
                echo "<div class='alert alert-success text-center'>$message</div>";
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

                <!-- New Password -->

                <div class="mb-3">

                    <label class="form-label">

                        New Password

                    </label>

                    <input
                        type="password"
                        name="new_password"
                        class="form-control custom-input"
                        placeholder="Enter new password"
                        required>

                </div>

                <!-- Confirm Password -->

                <div class="mb-3">

                    <label class="form-label">

                        Confirm Password

                    </label>

                    <input
                        type="password"
                        name="confirm_password"
                        class="form-control custom-input"
                        placeholder="Confirm password"
                        required>

                </div>

                <!-- Button -->

                <button
                    type="submit"
                    name="update_password"
                    class="btn btn-success w-100 update-btn">

                    Update Password

                </button>

            </form>

            <!-- Link -->

            <div class="links">

                <a href="login.php">

                    Back to Login

                </a>

            </div>

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