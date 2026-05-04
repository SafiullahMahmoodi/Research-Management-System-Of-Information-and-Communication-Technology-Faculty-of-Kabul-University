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

    <!-- Local Bootstrap CSS -->

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>Forgot Password</title>

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

        .forgot-card{

            width:100%;
            max-width:420px;
            background:#f1f3f5;
            border:1px solid #d1d5db;
            border-radius:15px;
            padding:25px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
        }

        /* Title */

        .forgot-title{

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

        .continue-btn{

            background:#0f9d58;
            border:none;
            border-radius:8px;
            padding:9px;
            font-size:16px;
            font-weight:600;
            margin-top:5px;
        }

        .continue-btn:hover{

            background:#0c7c45;
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

            .forgot-card{

                padding:20px;
            }

            .forgot-title{

                font-size:20px;
            }

            .custom-input{

                height:40px;
                font-size:12px;
            }

            .continue-btn{

                font-size:14px;
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

        <!-- Forgot Password Card -->

        <div class="forgot-card">

            <!-- Title -->

            <h2 class="forgot-title">

                Forgot Password

            </h2>

            <!-- Error Message -->

            <?php
                if($error != ""){
                    echo "<div class='alert alert-danger text-center'>$error</div>";
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

                <!-- Button -->

                <button
                    type="submit"
                    name="check_email"
                    class="btn btn-success w-100 continue-btn"
                >

                    Continue

                </button>

            </form>

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