<?php
// login.php

session_start();

// Include Database Connection
include('db_connection.php');

$error = "";

if(isset($_POST['login'])){

    $username_email = trim($_POST['username_email']);
    $usertype       = trim($_POST['user_type']);
    $password       = trim($_POST['password']);

    // Check User

    $sql = "SELECT * FROM users
            WHERE (username='$username_email' OR email='$username_email')
            AND usertype='$usertype'";

    $result = $conn->query($sql);

    if($result->num_rows > 0){

        $row = $result->fetch_assoc();

        // Verify Password

        if(password_verify($password, $row['Password'])){

            $_SESSION['user_id']  = $row['ID'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['usertype'] = $row['usertype'];

            // Redirect Based On User Type

            if($row['usertype'] == "admin"){

                header("Location: admin/dashboard.php");
                exit();

            }elseif($row['usertype'] == "user"){

                header("Location: user/dashboard.php");
                exit();
            }

        }else{

            $error = "Incorrect Password!";
        }

    }else{

        $error = "User not found!";
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

    <title>Login</title>

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

        <!-- Login Card -->

        <div class="login-card">

            <!-- Title -->

            <h2 class="login-title">

                Login Form

            </h2>

            <!-- Error Message -->

            <?php
                if($error != ""){
                    echo "<div class='alert alert-danger text-center'>$error</div>";
                }
            ?>

            <!-- Login Form -->

            <form method="POST">

                <!-- Username -->

                <div class="mb-3">

                    <label class="form-label">

                        Username or Email

                    </label>

                    <input
                        type="text"
                        name="username_email"
                        class="form-control custom-input"
                        placeholder="Enter username or email"
                        required
                    >

                </div>

                <!-- User Type -->

                <div class="mb-3">

                    <label class="form-label">

                        User Type

                    </label>

                    <select
                        name="user_type"
                        class="form-select custom-input"
                        required
                    >

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
                        required
                    >

                </div>

                <!-- Login Button -->

                <button
                    type="submit"
                    name="login"
                    class="btn btn-success w-100 login-btn"
                >

                    Login

                </button>

            </form>

            <!-- Links -->

            <div class="links d-flex justify-content-between">

                <a href="signup.php">

                    Don't have account?

                </a>

                <a href="forgotpassword.php">

                    Forgot Password

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