<?php
// login.php

session_start();

// Include Database Connection
include('db_connection.php');

$error = "";

if (isset($_POST['login'])) {

    $username_email = trim($_POST['username_email']);
    $usertype       = trim($_POST['user_type']);
    $password       = trim($_POST['password']);

    // Check user in database
    $sql = "SELECT * FROM users 
            WHERE (username='$username_email' OR email='$username_email')
            AND usertype='$usertype'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        // Verify Hashed Password
        if (password_verify($password, $row['Password'])) {

            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['usertype'] = $row['usertype'];

            // Redirect Based On User Type
            if ($row['usertype'] == "admin") {

                header("Location: admin/dashboard.php");
                exit();

            } elseif ($row['usertype'] == "user") {

                header("Location: user/dashboard.php");
                exit();
            }

        } else {

            $error = "Incorrect Password or Username!";
        }

    } else {

        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/style.css">
    <title>Login</title>

</head>
<body>

    <!-- Header -->
    <?php include('loginheader.php'); ?>

    <!-- Main Content -->
    <div class="main-container">

        <div class="login-container">

            <h2>Login Form</h2>

            <?php
                if($error != ""){
                    echo "<div class='error'>$error</div>";
                }
            ?>

            <form method="POST">

                <div class="input-group">
                    <label>Username or Email</label>
                    <input type="text" name="username_email" required>
                </div>

                <div class="input-group">
                    <label>User Type</label>

                    <select name="user_type" value="Select user type" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" name="login" class="login-btn">
                    Login
                </button>

            </form>

            <div class="links">
             <p>   <a href="signup.php">Don't have account?</a></p>
               <p> <a href="forgotpassword.php">Forgot Password</a></p>
            </div>

        </div>

    </div>

</body>
</html>