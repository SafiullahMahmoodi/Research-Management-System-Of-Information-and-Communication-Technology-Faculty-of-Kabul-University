<?php
include('../auth.php');



$lang = $_SESSION['lang'] ?? 'en';

include('../db_connection.php');
$error = "";

// ===========================
// Insert User
// ===========================

$error = "";

if (isset($_POST['save_user'])) {

    $username         = $_POST['username'];
    $email            = $_POST['email'];
    $user_type        = $_POST['user_type'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check Password Match
    if ($password != $confirm_password) {
        $error = ($lang == 'fa')
            ? "رمزهای عبور مطابقت ندارند!"
            : "Passwords do not match!";
    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert_query = "INSERT INTO users
    (
        Username,
        Email,
        usertype,
        Password
    )

    VALUES

    (
        '$username',
        '$email',
        '$user_type',
        '$hashed_password'
    )";

        $conn->query($insert_query);

        header("Location: users.php");
        exit();
    }
}

// ===========================
// Delete User
// ===========================

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    $delete_query = "DELETE FROM users
    WHERE ID='$delete_id'";

    $conn->query($delete_query);

    header("Location: users.php");
    exit();
}

// ===========================
// Edit User
// ===========================

$edit_id       = "";
$edit_username = "";
$edit_email    = "";
$edit_type     = "";

if (isset($_GET['edit'])) {

    $edit_id = $_GET['edit'];

    $edit_query = "SELECT * FROM users
    WHERE ID='$edit_id'";

    $edit_result = $conn->query($edit_query);

    if ($edit_result->num_rows > 0) {

        $edit_row = $edit_result->fetch_assoc();

        $edit_username = $edit_row['Username'];
        $edit_email    = $edit_row['Email'];
        $edit_type     = $edit_row['usertype'];
    }
}

// ===========================
// Update User
// ===========================

if (isset($_POST['update_user'])) {

    $id         = $_POST['id'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $user_type  = $_POST['user_type'];

    $update_query = "UPDATE users SET

    Username='$username',
    Email='$email',
    usertype='$user_type'

    WHERE ID='$id'";

    $conn->query($update_query);

    header("Location: users.php");
    exit();
}

// ===========================
// Search
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = $_GET['search'];

    $user_query = "SELECT * FROM users

    WHERE

  
    OR Username LIKE '%$search%'
    OR Email LIKE '%$search%'
    OR usertype LIKE '%$search%'";
} else {

    $user_query = "SELECT * FROM users";
}

$user_result = $conn->query($user_query);

?>

<!DOCTYPE html>
<html lang="<?= ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= ($lang == 'fa') ? 'کاربران' : 'Users'; ?></title>
    <link rel="stylesheet"
        href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>
    <Style>
        /* ==========================
   MODERN SEARCH BOX
========================== */

        .search-wrapper {
            display: flex;
            justify-content: center;
            margin: 13px 0;
        }

        .search-form {
            width: 100%;
            max-width: 450px;
            display: flex;
            align-items: center;
            background: #fff;
            border: 2px solid #3d3d3d;
            border-radius: 50px;
            overflow: hidden;
            transition: .3s;
        }

        .search-form:focus-within {
            border-color: #0f9d58;
            box-shadow: 0 0 15px rgba(15, 157, 88, .18);
        }

        /* Input */

        .search-input {
            flex: 1;

            border: none;
            outline: none;

            background: transparent;

            padding: 10px 15px;

            font-size: 13px;

            color: #222;
        }

        .search-input::placeholder {
            color: #777;
        }

        /* Button */

        .search-btn {

            width: 60px;
            height: 60px;

            border: none;
            background: transparent;

            cursor: pointer;

            font-size: 26px;

            display: flex;
            justify-content: center;
            align-items: center;

            transition: .25s;
        }

        .search-btn:hover {

            color: #0f9d58;
        }

        /* English */

        html[dir="ltr"] .search-form {
            flex-direction: row;
        }

        html[dir="ltr"] .search-input {
            text-align: left;
        }

        html[dir="ltr"] .search-btn {
            border-left: 1px solid #ddd;
        }

        /* Persian */

        html[dir="rtl"] .search-form {
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .search-input {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .search-btn {
            border-right: 1px solid #ddd;
        }

        html[dir="rtl"] {
            direction: rtl;
        }

        .form-buttons {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .form-buttons button {
            flex: 1 1 0;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 400;
        }

        .save-btn {
            background: #0f9d58;
            color: white;
        }

        .save-btn:hover {
            background: #0c7c45;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }


        html[dir="rtl"] .table-card,
        html[dir="rtl"] .form-card,
        html[dir="rtl"] .search-form,
        html[dir="rtl"] .table,
        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] .form-label,
        html[dir="rtl"] .form-title {
            text-align: right;
        }

        html[dir="rtl"] .action-icons {
            justify-content: flex-start;
        }

        html[dir="rtl"] .search-wrapper {
            direction: rtl;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }
    </Style>

</head>

<body>
    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE SECTION -->

        <div class="table-section">

            <!-- SEARCH -->
            <div class="search-wrapper">

                <form method="GET" class="search-form">

                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی استفاده کننده گان...' : 'Search users...'; ?>"
                        value="<?= htmlspecialchars($search); ?>">

                    <button type="submit" class="search-btn">
                        🔍
                    </button>

                </form>

            </div>

            <!-- TABLE -->

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>



                            <th><?= ($lang == 'fa') ? 'نام استفاده کننده' : 'Username'; ?></th>

                            <th><?= ($lang == 'fa') ? 'ایمیل' : 'Email'; ?></th>

                            <th><?= ($lang == 'fa') ? 'نوع استفاده کننده' : 'User Type'; ?></th>

                            <th width="160">
                                <?= ($lang == 'fa') ? 'عملیات' : 'Action'; ?>
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $user_result->fetch_assoc()) { ?>

                            <tr>



                                <td><?php echo $row['Username']; ?></td>

                                <td><?php echo $row['Email']; ?></td>

                                <td><?php echo $row['usertype']; ?></td>

                                <td>

                                    <div class="action-icons">

                                        <a href="users.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            <?= ($lang == 'fa') ? 'تغییر آوردن' : 'Edit'; ?>

                                        </a>
                                        <a href="users.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('<?= ($lang == 'fa')
                                                                            ? 'آیا مطمئن هستید که می‌خواهید این استفاده کننده را حذف کنید؟'
                                                                            : 'Are you sure you want to delete this user?'; ?>')">

                                            <?= ($lang == 'fa') ? 'حذف' : 'Delete'; ?>

                                        </a>

                                    </div>

                                </td>

                            </tr>



                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- FORM SECTION -->

        <div class="form-section">

            <div class="form-card">

                <div class="form-title">

                    <?php
                    echo isset($_GET['edit'])
                        ? (($lang == 'fa') ? 'تغییر آوردن استفاده کننده' : 'Edit User')
                        : (($lang == 'fa') ? 'اضافه نمودن استفاده کننده' : 'Add User');
                    ?>

                </div>

                <form method="POST">

                    <input type="hidden"

                        name="id"

                        value="<?php echo $edit_id; ?>">

                    <!-- Username -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'نام استفاده کننده' : 'Username'; ?>
                        </label>

                        <input type="text"

                            name="username"

                            class="form-control"



                            value="<?php echo $edit_username; ?>"

                            required>

                    </div>

                    <!-- Email -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'ایمیل' : 'Email'; ?>
                        </label>

                        <input type="email"

                            name="email"

                            class="form-control"


                            value="<?php echo $edit_email; ?>"

                            required>

                    </div>

                    <!-- User Type -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'نوع استفاده کننده' : 'User Type'; ?>
                        </label>

                        <select name="user_type"
                            class="custom-select">

                            <option value="Admin"
                                <?php if ($edit_type == "Admin") echo "selected"; ?>>

                                <?= ($lang == 'fa') ? 'مدیر' : 'Admin'; ?>

                            </option>

                            <option value="User"
                                <?php if ($edit_type == "User") echo "selected"; ?>>

                                <?= ($lang == 'fa') ? 'استفاده کننده عادی' : 'User'; ?>

                            </option>

                        </select>

                    </div>

                    <!-- Password -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'رمز عبور' : 'Password'; ?>
                        </label>

                        <input type="password"

                            name="password"

                            class="form-control"


                            required>

                    </div>

                    <!-- Confirm Password -->

                    <div class="mb-4">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'تأیید رمز عبور' : 'Confirm Password'; ?>
                        </label>
                        <?php if ($error != "") { ?>

                            <div class="alert alert-danger">

                                <?php echo $error; ?>

                            </div>

                        <?php } ?>

                        <input type="password"

                            name="confirm_password"

                            class="form-control"


                            required>

                    </div>
                    <!-- BUTTON -->


                    <?php
                    $isEdit = isset($_GET['edit']);
                    ?>



                    <div class="form-buttons">

                        <button
                            type="submit"
                            class="save-btn"
                            name="<?php echo $isEdit ? 'update_user' : 'save_user'; ?>">

                            <?php
                            echo $isEdit
                                ? ($lang == 'fa' ? 'تغییر آوری استفاده کننده' : 'Update User')
                                : ($lang == 'fa' ? 'ذخیره استفاده کننده' : 'Save User');
                            ?>

                        </button>

                        <button
                            type="button"
                            class="cancel-btn"
                            onclick="window.location.href='users.php'">

                            <?php echo ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>

                        </button>

                    </div>



                </form>

            </div>

        </div>

    </div>

</body>

</html>