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

        if(password_verify($password, $row['password'])){

            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
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

    <!-- Local Bootstrap CSS -->

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>Login</title>

    <style>

        body{

            background:#ffffff;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        /* Header */

        .main-header{

            background:#0f9d58;
            color:white;
            padding:10px 18px;
            box-shadow:0 2px 8px rgba(0,0,0,0.12);
        }

        .header-title{

            font-size:16px;
            font-weight:600;
        }

        .home-btn{

            background:white;
            color:#0f9d58;
            border-radius:6px;
            padding:5px 14px;
            font-size:13px;
            font-weight:600;
            border:none;
            text-decoration:none;
            transition:0.3s;
        }

        .home-btn:hover{

            background:#e5e7eb;
            color:#0c7c45;
        }

        /* Main Container */

        .main-container{

            flex:1;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
        }

        /* Login Card */

        .login-card{

            width:100%;
            max-width:420px;
            background:#f1f3f5;
            border:1px solid #d1d5db;
            border-radius:15px;
            padding:25px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
        }

        /* Title */

        .login-title{

            text-align:center;
            font-weight:bold;
            font-size:24px;
            margin-bottom:22px;
            color:#1f2937;
        }

        /* Labels */

        .form-label{

            font-weight:600;
            font-size:14px;
            color:#374151;
            margin-bottom:6px;
        }

        /* Inputs */

        .custom-input{

            width:100%;
            height:42px;
            border-radius:8px;
            border:1px solid #cbd5e1;
            padding:8px 12px;
            font-size:13px;
            background:#fff;
        }

        .custom-input:focus{

            border-color:#0f9d58;
            box-shadow:0 0 5px rgba(15,157,88,0.3);
            outline:none;
        }

        select.custom-input{

            cursor:pointer;
        }

        /* Button */

        .login-btn{

            background:#0f9d58;
            border:none;
            border-radius:8px;
            padding:9px;
            font-size:16px;
            font-weight:600;
            margin-top:5px;
        }

        .login-btn:hover{

            background:#0c7c45;
        }

        /* Links */

        .links{

            margin-top:18px;
        }

        .links a{

            text-decoration:none;
            font-size:13px;
        }

        .links a:hover{

            text-decoration:underline;
        }

        /* Footer */

        .main-footer{

            background:#1f2937;
            color:white;
            text-align:center;
            padding:10px;
            font-size:12px;
        }

        /* Responsive */

        @media(max-width:768px){

            .header-title{

                font-size:12px;
                width:70%;
                line-height:18px;
            }

            .home-btn{

                font-size:11px;
                padding:4px 10px;
            }

            .login-card{

                padding:20px;
            }

            .login-title{

                font-size:20px;
            }

            .custom-input{

                height:40px;
                font-size:12px;
            }

            .login-btn{

                font-size:14px;
            }

            .links a{

                font-size:12px;
            }
        }

    </style>

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

                    Home

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