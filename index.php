<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Management System</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        body{
            height:100vh;
            display:flex;
            flex-direction:column;
            background:#f4f6f9;
        }

        /* Header */
        header{
            background:#0f9d58;
            color:white;
            text-align:center;
            padding:20px;
            font-size:28px;
            font-weight:bold;
            box-shadow:0 2px 8px rgba(0,0,0,0.2);
        }

        /* Main Section */
        .hero{
            flex:1;
            position:relative;
            background:url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1600&auto=format&fit=crop') no-repeat center center/cover;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        /* Dark Overlay */
        .overlay{
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.45);
        }

        /* Buttons Container */
        .buttons{
            position:relative;
            z-index:2;
            display:flex;
            gap:20px;
        }

        .btn{
            text-decoration:none;
            padding:15px 35px;
            font-size:20px;
            border-radius:10px;
            font-weight:bold;
            transition:0.3s;
            border:none;
            cursor:pointer;
            box-shadow:0 4px 10px rgba(0,0,0,0.3);
        }

        /* Color style inspired from your system */
        .signin{
            background:#1d4ed8;
            color:white;
        }

        .signin:hover{
            background:#163fb3;
            transform:translateY(-3px);
        }

        .signup{
            background:#0f9d58;
            color:white;
        }

        .signup:hover{
            background:#0c7c45;
            transform:translateY(-3px);
        }

        /* Footer */
        footer{
            background:#1f2937;
            color:white;
            text-align:center;
            padding:15px;
            font-size:15px;
        }

        @media(max-width:768px){
            header{
                font-size:20px;
                padding:15px;
            }

            .buttons{
                flex-direction:column;
            }

            .btn{
                width:220px;
                text-align:center;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        Research Management System of Information & Communication Technology
    </header>

    <!-- Main Body -->
    <section class="hero">
        <div class="overlay"></div>

        <div class="buttons">
            <a href="login.php" class="btn signin">Sign in</a>
            <a href="signup.php" class="btn signup">Sign up</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        &copy; <?php echo date("Y"); ?> Information & Communication Technology Faculty of Kabul University
    </footer>

</body>
</html>