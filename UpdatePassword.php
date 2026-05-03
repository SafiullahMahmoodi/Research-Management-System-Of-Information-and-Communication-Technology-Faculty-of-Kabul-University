<?php
// updatepassword.php

session_start();

// Include Database Connection
include('db_connection.php');

$message = "";
$error   = "";

if(isset($_POST['update_password'])){

    $email            = trim($_POST['email']);
    $new_password     = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check Password Match
    if($new_password != $confirm_password){

        $error = "Passwords do not match!";

    }else{

        // Check Email Exists
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($check);

        if($result->num_rows > 0){

            // Hash New Password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update Password
            $sql = "UPDATE users 
                    SET password='$hashed_password'
                    WHERE email='$email'";

            if($conn->query($sql) === TRUE){

                $message = "Password Updated Successfully!";

            }else{

                $error = "Something went wrong!";
            }

        }else{

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
    <link rel="stylesheet" href="Css/style.css">
    <title>Update Password</title>

 
</head>
<body>

    <!-- Header -->
    <?php include('loginheader.php'); ?>

    <!-- Main Content -->
    <div class="main-container">

        <div class="login-container">

            <h2>Update Password</h2>

            <?php
                if($error != ""){
                    echo "<div class='error'>$error</div>";
                }

                if($message != ""){
                    echo "<div class='success'>$message</div>";
                }
            ?>

            <form method="POST">

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>

                <div class="input-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>

                <div class="input-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit" name="update_password" class="login-btn">
                    Update Password
                </button>

            </form>

            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>

        </div>

    </div>

</body>
</html>