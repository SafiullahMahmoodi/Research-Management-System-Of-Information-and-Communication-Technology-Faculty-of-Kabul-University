<?php
// signup.php

session_start();
$lang = $_SESSION['lang'] ?? 'en';

// Include Database Connection
include('db_connection.php');

$message = "";
$error   = "";

if (isset($_POST['signup'])) {

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
    if ($result->num_rows > 0) {

        $error = ($lang == 'fa')
            ? "نام کاربری یا ایمیل قبلاً ثبت شده است!"
            : "Username or Email already exists!";
    } else {

        $sql = "INSERT INTO users(username, email, usertype, password)
            VALUES('$username', '$email', '$user_type', '$password')";

        if ($conn->query($sql) === TRUE) {

            $message = ($lang == 'fa')
                ? "ثبت نام با موفقیت انجام شد!"
                : "Registration Successfully Completed!";
        } else {

            $error = ($lang == 'fa')
                ? "خطایی رخ داده است!"
                : "Something went wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?php echo ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <!-- Local Bootstrap CSS -->

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>
        <?php
        echo ($lang == 'fa')
            ? 'ایجاد حساب کاربری'
            : 'Create Account';
        ?>
    </title>
    <style>
        html[dir="rtl"] {
            direction: rtl;
            text-align: right;
        }

        html[dir="ltr"] {
            direction: ltr;
            text-align: left;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .form-select {
            text-align: right;
        }

        html[dir="ltr"] .form-control,
        html[dir="ltr"] .form-select {
            text-align: left;
        }

        /* Persian (RTL) */
        html[dir="rtl"] .form-label {
            display: block;
            text-align: right;
            width: 100%;
        }

        /* English (LTR) */
        html[dir="ltr"] .form-label {
            display: block;
            text-align: left;
            width: 100%;
        }

        .show-password-container {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .show-password-container input[type="checkbox"] {
            margin: 0;
        }

        .show-password-container label {
            margin: 0;
            cursor: pointer;
        }

        .show-password {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        html[dir="rtl"] .show-password {
            justify-content: flex-start;
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

                    <?php
                    echo ($lang == 'fa')
                        ? 'سیستم مدیریت تحقیقات پوهنځی تکنالوژی معلوماتی و مخابراتی'
                        : 'Research Management of Information and Communication Technology Faculty';
                    ?>

                </div>

                <!-- Right Button -->

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

        <!-- Signup Card -->

        <div class="signup-card">

            <!-- Title -->

            <h2 class="signup-title">

                <?php
                echo ($lang == 'fa')
                    ? 'ایجاد حساب کاربری'
                    : 'Create Account';
                ?>

            </h2>

            <!-- Error Message -->

            <?php
            if ($error != "") {
                echo "<div class='alert alert-danger text-center'>$error</div>";
            }

            if ($message != "") {
                echo "<div class='alert alert-success text-center'>$message</div>";
            }
            ?>

            <!-- Signup Form -->

            <form method="POST">

                <!-- Username -->

                <div class="mb-3">
                    <label class="form-label">

                        <?php
                        echo ($lang == 'fa')
                            ? 'نام کاربری'
                            : 'Username';
                        ?>

                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control custom-input"
                        placeholder="<?php echo ($lang == 'fa') ? 'نام کاربری را وارد کنید' : 'Enter username'; ?>"
                        required>

                </div>

                <!-- Email -->

                <div class="mb-3">

                    <label class="form-label">

                        <?php
                        echo ($lang == 'fa')
                            ? 'ایمیل'
                            : 'Email';
                        ?>

                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control custom-input"
                        placeholder="<?php echo ($lang == 'fa') ? 'ایمیل را وارد کنید' : 'Enter email'; ?>"
                        required>

                </div>

                <!-- User Type -->

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

                        <option value="">

                            <?php
                            echo ($lang == 'fa')
                                ? 'نوع کاربر را انتخاب کنید'
                                : 'Select User Type';
                            ?>

                        </option>

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

                <!-- Password -->

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
                        class="form-control custom-input"
                        placeholder="<?php echo ($lang == 'fa') ? 'رمز عبور را وارد کنید' : 'Enter password'; ?>"
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


                <!-- Signup Button -->

                <button
                    type="submit"
                    name="signup"
                    class="btn btn-success w-100 signup-btn">

                    <?php
                    echo ($lang == 'fa')
                        ? 'ثبت نام'
                        : 'Sign Up';
                    ?>

                </button>

            </form>

            <!-- Links -->

            <div class="links">

                <div class="links">

                    <a href="login.php">

                        <?php
                        echo ($lang == 'fa')
                            ? 'قبلاً حساب دارید؟ وارد شوید'
                            : 'Already have an account? Login';
                        ?>

                    </a>

                </div>

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