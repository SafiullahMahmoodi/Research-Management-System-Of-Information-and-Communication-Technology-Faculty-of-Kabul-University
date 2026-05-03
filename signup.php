<?php
// signup.php

session_start();

// Include Database Connection
include('db_connection.php');

$message = "";
$error   = "";

if(isset($_POST['signup'])){

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

    if($result->num_rows > 0){

        $error = "Username or Email already exists!";

    }else{

        // Insert User
        $sql = "INSERT INTO users(username, email, usertype, password)
                VALUES('$username', '$email', '$user_type', '$password')";

        if($conn->query($sql) === TRUE){

            $message = "Registration Successfully Completed!";

        }else{

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
    <link rel="stylesheet" href="Css/style.css">
    <title>Sign Up</title>

</head>
<body>

    <!-- Header -->
    <?php include('loginheader.php'); ?>

    <!-- Main Content -->
    <div class="main-container">

        <div class="signup-container">

            <h2>Create Account</h2>

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
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="input-group">
                    <label>User Type</label>

                    <select name="user_type" value="selct user type" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" name="signup" class="signup-btn">
                    Sign Up
                </button>

            </form>

            <div class="links">
                <a href="login.php">Already have an account? Login</a>
            </div>

        </div>

    </div>

</body>
</html>