<?php
// login.php

session_start();
$lang = $_SESSION['lang'] ?? 'en';
$dir = ($lang == 'fa') ? 'rtl' : 'ltr';

// جلوگیری از cache مرورگر

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Include Database Connection

include('db_connection.php');

$error = "";

if (isset($_POST['login'])) {

    $username_email = trim($_POST['username_email']);
    $usertype       = trim($_POST['user_type']);
    $password       = trim($_POST['password']);

    // Check User

    $sql = "SELECT * FROM users
            WHERE (Username='$username_email' OR Email='$username_email')
            AND usertype='$usertype'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        // Verify Password

        if (password_verify($password, $row['Password'])) {

            // =========================
            // CREATE SESSION
            // =========================

            $_SESSION['user']      = $row['Username'];
            $_SESSION['user_id']   = $row['ID'];
            $_SESSION['username']  = $row['Username'];
            $_SESSION['usertype']  = $row['usertype'];

            // =========================
            // REDIRECT
            // =========================

            if ($row['usertype'] == "admin") {

                header("Location: admin/dashboard.php");
                exit();
            } elseif ($row['usertype'] == "user") {

                header("Location: user/dashboard.php");
                exit();
            }
        } else {

            $error = ($lang == 'fa')
                ? "رمز عبور اشتباه است!"
                : "Incorrect Password!";
        }
    } else {

        $error = ($lang == 'fa')
            ? "کاربر یافت نشد!"
            : "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet"
        href="css/bootstrap.min.css">

    <link rel="stylesheet"
        href="css/style.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            background: #f3f6fb;

            font-family: Segoe UI;

            min-height: 100vh;

            display: flex;

            flex-direction: column;
        }

        .main-header {

            background: #0f9d58;

            color: white;

            padding: 16px 25px;
        }

        .header-title {

            font-size: 18px;

            font-weight: 700;
        }

        .home-btn {

            background: white;

            color: #0f9d58;

            font-weight: 700;

            border-radius: 8px;

            padding: 8px 18px;
        }

        .home-btn:hover {

            background: #e8fff3;

            color: #0f9d58;
        }

        .main-container {

            flex: 1;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 30px;
        }

        .login-card {

            width: 100%;

            max-width: 430px;

            background: white;

            border-radius: 18px;

            padding: 30px;

            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .login-title {

            text-align: center;

            margin-bottom: 25px;

            color: #0f9d58;

            font-weight: 700;
        }

        .custom-input {
            padding: 10px 12px;
            min-height: 45px;
            line-height: 1.5;
        }

        .login-btn {

            border-radius: 10px;

            padding: 12px;

            font-size: 15px;

            font-weight: 700;

            background: #0f9d58;

            border: none;
        }

        .login-btn:hover {

            background: #0c8047;
        }

        .links {

            margin-top: 18px;
        }

        .links a {

            text-decoration: none;

            font-size: 13px;

            color: #0f9d58;

            font-weight: 600;
        }

        .main-footer {

            background: #0f9d58;

            color: white;

            text-align: center;

            padding: 14px;

            font-size: 14px;
        }

        .rtl {
            direction: rtl;
        }

        .rtl .form-label {
            display: block;
            text-align: right;
        }

        .rtl .form-control,
        .rtl .form-select {
            text-align: right;
        }

        .rtl .links {
            text-align: right;
        }

        .rtl input::placeholder,
        .rtl textarea::placeholder {
            text-align: right;
        }

        .rtl select {
            text-align: right;
        }

        html[dir="rtl"] .custom-input {
            text-align: right;
            padding-right: 12px;
            padding-left: 12px;
        }

        .rtl .form-check {
            text-align: right;
        }

        .rtl .form-check-input {
            float: right;
            margin-left: 8px;
            margin-right: 0;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        html[dir="rtl"] .form-check {
            flex-direction: row-reverse;
        }
    </style>

</head>

<body class="<?php echo ($lang == 'fa') ? 'rtl' : ''; ?>">

    <!-- Header -->

    <header class="main-header">

        <div class="container-fluid">

            <div class="d-flex
justify-content-between
align-items-center">

                <div class="header-title">

                    <?php
                    echo ($lang == 'fa')
                        ? 'سیستم مدیریت تحقیقات پوهنځی تکنالوژی معلوماتی و مخابراتی'
                        : 'Research Management of Information and Communication Technology Faculty';
                    ?>

                </div>

                <a href="index.php" class="btn home-btn">

                    <?php
                    echo ($lang == 'fa')
                        ? 'صفحه اصلی'
                        : 'Home Page';
                    ?>

                </a>

            </div>

        </div>

    </header>

    <!-- Main Container -->

    <div class="main-container">

        <div class="login-card">

            <h2 class="login-title">

                <?php
                echo ($lang == 'fa')
                    ? 'فورم ورود'
                    : 'Login Form';
                ?>

            </h2>

            <!-- Error -->

            <?php

            if ($error != "") {

                echo "
    <div class='alert alert-danger text-center'>
    $error
    </div>";
            }

            ?>

            <!-- Form -->

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">

                        <?php
                        echo ($lang == 'fa')
                            ? 'نام کاربری یا ایمیل'
                            : 'Username or Email';
                        ?>

                    </label>

                    <input
                        type="text"
                        name="username_email"
                        class="form-control custom-input"
                        placeholder="<?php echo ($lang == 'fa') ? 'نام کاربری یا ایمیل را وارد کنید' : 'Enter username or email'; ?>"
                        required>
                </div>

                <div class="mb-3">

                    <label class="form-label">

                        <?php
                        echo ($lang == 'fa')
                            ? 'نوع کاربر'
                            : 'User Type';
                        ?>

                    </label>

                    <select
                        name="user_type"
                        class="form-select custom-input"
                        required>


                        <option value="admin">

                            <?php
                            echo ($lang == 'fa')
                                ? 'مدیر'
                                : 'Admin';
                            ?>

                        </option>

                        <option value="user">

                            <?php
                            echo ($lang == 'fa')
                                ? 'کاربر'
                                : 'User';
                            ?>

                        </option>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        <?php
                        echo ($lang == 'fa')
                            ? 'رمز عبور'
                            : 'Password';
                        ?>

                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required>
                    <div style="display:flex; align-items:center; gap:8px;
     flex-direction:<?= ($lang == 'fa') ? 'row' : 'row'; ?>;">

                        <input
                            type="checkbox"
                            id="showPassword"
                            onclick="document.getElementById('password').type = this.checked ? 'text' : 'password';">

                        <label for="showPassword" style="margin:0;">

                            <?php
                            echo ($lang == 'fa')
                                ? 'نمایش رمز عبور'
                                : 'Show Password';
                            ?>

                        </label>

                    </div>


                </div>
                <button
                    type="submit"
                    name="login"
                    class="btn btn-success w-100 login-btn">

                    <?php
                    echo ($lang == 'fa')
                        ? 'ورود'
                        : 'Login';
                    ?>

                </button>

            </form>

            <div class="links d-flex justify-content-between">

                <a href="signup.php">

                    <?php
                    echo ($lang == 'fa')
                        ? 'حساب ندارید؟ ثبت نام کنید'
                        : "Don't have account?";
                    ?>

                </a>

                <a href="forgotpassword.php">

                    <?php
                    echo ($lang == 'fa')
                        ? 'رمز عبور را فراموش کرده‌اید؟'
                        : 'Forgot Password';
                    ?>

                </a>

            </div>

        </div>

    </div>

    <!-- Footer -->

    <footer class="main-footer">

        &copy; <?php echo date("Y"); ?>

        Information & Communication Technology
        Faculty of Kabul University

    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>