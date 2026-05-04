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

    <!-- Local Bootstrap CSS -->

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>Update Password</title>

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

        /* Card */

        .update-card{

            width:100%;
            max-width:430px;
            background:#f1f3f5;
            border:1px solid #d1d5db;
            border-radius:15px;
            padding:25px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
        }

        /* Title */

        .update-title{

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

        /* Button */

        .update-btn{

            background:#0f9d58;
            border:none;
            border-radius:8px;
            padding:9px;
            font-size:16px;
            font-weight:600;
            margin-top:5px;
        }

        .update-btn:hover{

            background:#0c7c45;
        }

        /* Links */

        .links{

            margin-top:18px;
            text-align:center;
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

            .update-card{

                padding:20px;
            }

            .update-title{

                font-size:20px;
            }

            .custom-input{

                height:40px;
                font-size:12px;
            }

            .update-btn{

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

        <!-- Update Password Card -->

        <div class="update-card">

            <!-- Title -->

            <h2 class="update-title">

                Update Password

            </h2>

            <!-- Error & Success Message -->

            <?php
                if($error != ""){
                    echo "<div class='alert alert-danger text-center'>$error</div>";
                }

                if($message != ""){
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
                        required
                    >

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
                        required
                    >

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
                        required
                    >

                </div>

                <!-- Button -->

                <button
                    type="submit"
                    name="update_password"
                    class="btn btn-success w-100 update-btn"
                >

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