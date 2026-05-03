<?php
// login.php

session_start();


// Include Database Connection
include('db_connection.php');

$error = "";


if (isset($_POST['login'])) {

    $username_email = trim($_POST['username_email']);
    $user_type      = trim($_POST['user_type']);
    $password       = trim($_POST['password']);

    // Check user in database
    $sql = "SELECT * FROM users 
            WHERE (username='$username_email' OR email='$username_email')
            AND user_type='$user_type'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        // Check Password
        // If you use password_hash() use password_verify()
        if ($password == $row['password']) {

            $_SESSION['user_id']   = $row['id'];
            $_SESSION['username']  = $row['username'];
            $_SESSION['user_type'] = $row['user_type'];

            // Redirect حسب نوع استفاده کننده
            if ($row['user_type'] == "admin") {

                header("Location: admin/dashboard.php");
                exit();

            } elseif ($row['user_type'] == "user") {

                header("Location: user/dashboard.php");
                exit();
            }

        } else {
            $error = "Incorrect Password!";
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
    <title>Login</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Segoe UI, sans-serif;
        }

        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#ffffff;
        }

        .login-container{

            width:400px;
            background:#f1f3f5;
            padding:35px;
            border:1px solid #d1d5db;
            border-radius:15px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);

        }

        .login-container h2{
            text-align:center;
            margin-bottom:25px;
            color:#1f2937;
        }

        .input-group{
            margin-bottom:18px;
        }

        .input-group label{
            display:block;
            margin-bottom:6px;
            font-weight:600;
            color:#374151;
        }

        .input-group input,
        .input-group select{

            width:100%;
            padding:12px;
            border:1px solid #cbd5e1;
            border-radius:8px;
            outline:none;
            font-size:15px;
            background:white;
        }

        .input-group input:focus,
        .input-group select:focus{

            border-color:#2563eb;
        }

        .login-btn{

            width:100%;
            padding:12px;
            background:#0f9d58;
            color:white;
            border:none;
            border-radius:8px;
            font-size:17px;
            cursor:pointer;
            transition:0.3s;
            margin-top:10px;
        }

        .login-btn:hover{
            background:#0b7d46;
        }

        .links{

            margin-top:18px;
            display:flex;
            justify-content:space-between;
        }

        .links a{

            text-decoration:none;
            color:#2563eb;
            font-size:14px;
        }

        .links a:hover{
            text-decoration:underline;
        }

        .error{

            background:#fee2e2;
            color:#b91c1c;
            padding:10px;
            border-radius:8px;
            margin-bottom:15px;
            text-align:center;
        }

    </style>
</head>
<body>

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

                <select name="user_type" required>
                    <option value="">Select User Type</option>
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
            <a href="register.php">Don't have account?</a>
            <a href="forgot_password.php">Forgot Password</a>
        </div>

    </div>

</body>
</html>