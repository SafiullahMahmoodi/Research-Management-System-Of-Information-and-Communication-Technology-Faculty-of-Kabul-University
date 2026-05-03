<?php
// forgotpassword.php

session_start();

// Include Database Connection
include('db_connection.php');

$error = "";

if(isset($_POST['check_email'])){

    $email = trim($_POST['email']);

    // Check Email
    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = $conn->query($sql);

    if($result->num_rows > 0){

        // Save Email In Session
        $_SESSION['reset_email'] = $email;

        // Redirect To Update Password Page
        header("Location: updatepassword.php");
        exit();

    }else{

        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
<link rel="stylesheet" href="Css/style.css">
    <!-- <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Segoe UI, sans-serif;
        }

        body{
            background:#ffffff;
        }

        /* Main Container */

        .main-container{

            height:calc(100vh - 65px);
            display:flex;
            justify-content:center;
            align-items:center;
        }

        /* Forgot Password Container */

        .login-container{

            width:320px;
            background:#f1f3f5;
            padding:22px;
            border:1px solid #d1d5db;
            border-radius:12px;
            box-shadow:0 4px 10px rgba(0,0,0,0.08);

        }

        /* Title */

        .login-container h2{

            text-align:center;
            margin-bottom:18px;
            color:#1f2937;
            font-size:20px;
        }

        /* Input Groups */

        .input-group{
            margin-bottom:14px;
        }

        .input-group label{

            display:block;
            margin-bottom:4px;
            font-weight:600;
            color:#374151;
            font-size:13px;
        }

        .input-group input{

            width:100%;
            padding:9px;
            border:1px solid #cbd5e1;
            border-radius:7px;
            outline:none;
            font-size:13px;
            background:white;
        }

        .input-group input:focus{

            border-color:#2563eb;
        }

        /* Button */

        .login-btn{

            width:100%;
            padding:10px;
            background:#0f9d58;
            color:white;
            border:none;
            border-radius:7px;
            font-size:14px;
            cursor:pointer;
            transition:0.3s;
            margin-top:6px;
        }

        .login-btn:hover{
            background:#0b7d46;
        }

        /* Error Message */

        .error{

            background:#fee2e2;
            color:#b91c1c;
            padding:8px;
            border-radius:6px;
            margin-bottom:12px;
            text-align:center;
            font-size:12px;
        }

    </style>
      -->
</head>
<body>

    <!-- Header -->
    <?php include('loginheader.php'); ?>

    <!-- Main Content -->
    <div class="main-container">

        <div class="login-container">

            <h2>Forgot Password</h2>

            <?php
                if($error != ""){
                    echo "<div class='error'>$error</div>";
                }
            ?>

            <form method="POST">

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>

                <button type="submit" name="check_email" class="login-btn">
                    Continue
                </button>

            </form>

        </div>

    </div>

</body>
</html>