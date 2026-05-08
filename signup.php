<?php
// signup.php

session_start();

// Include Database Connection
include('db_connection.php');

$message = "";
$error   = "";

if (isset($_POST['signup'])) {

    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $user_type = trim($_POST['user_type']);

    // Hash Password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check Email or Username Already Exists

    $check = "SELECT * FROM users
              WHERE username='$username'
              OR email='$email'";

    $result = $conn->query($check);

    if ($result->num_rows > 0) {

        $error = "Username or Email already exists!";
    } else {

        // Insert User

        $sql = "INSERT INTO users(username, email, usertype, password)
                VALUES('$username', '$email', '$user_type', '$password')";

        if ($conn->query($sql) === TRUE) {

            $message = "Registration Successfully Completed!";
        } else {

            $error = "Something went wrong!";
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

    <title>Sign Up</title>
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

        <!-- Signup Card -->

        <div class="signup-card">

            <!-- Title -->

            <h2 class="signup-title">

                Create Account

            </h2>

            <!-- Error Message -->

            <?php
            if ($error != "") {
                echo "<div class='alert alert-danger text-center'>$error</div>";
            }

            if ($message != "") {
                echo "<div class='alert alert-success text-center'>$message</div>";
            }
            ?>

            <!-- Signup Form -->

            <form method="POST">

                <!-- Username -->

                <div class="mb-3">

                    <label class="form-label">

                        Username

                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control custom-input"
                        placeholder="Enter username"
                        required>

                </div>

                <!-- Email -->

                <div class="mb-3">

                    <label class="form-label">

                        Email

                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control custom-input"
                        placeholder="Enter email"
                        required>

                </div>

                <!-- User Type -->

                <div class="mb-3">

                    <label class="form-label">

                        User Type

                    </label>

                    <select
                        name="user_type"
                        class="form-select custom-input"
                        required>

                        <option value="">

                            Select User Type

                        </option>

                        <option value="admin">

                            Admin

                        </option>

                        <option value="user">

                            User

                        </option>

                    </select>

                </div>

                <!-- Password -->

                <div class="mb-3">

                    <label class="form-label">

                        Password

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control custom-input"
                        placeholder="Enter password"
                        required>

                </div>

                <!-- Signup Button -->

                <button
                    type="submit"
                    name="signup"
                    class="btn btn-success w-100 signup-btn">

                    Sign Up

                </button>

            </form>

            <!-- Links -->

            <div class="links">

                <a href="login.php">

                    Already have an account? Login

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