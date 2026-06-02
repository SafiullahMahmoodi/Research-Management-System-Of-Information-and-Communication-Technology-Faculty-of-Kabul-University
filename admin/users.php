<?php
include('../auth.php');




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

        $error = "Password do not match!";
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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Users</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>


</head>

<body>
    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE SECTION -->

        <div class="table-section">

            <!-- SEARCH -->

            <div class="search-wrapper">

                <form method="GET"
                    class="search-form">

                    <input type="text"

                        name="search"

                        class="search-input"

                        placeholder="Search users..."

                        value="<?php echo $search; ?>">

                    <button type="submit"
                        class="search-btn">

                        Search

                    </button>

                </form>

            </div>

            <!-- TABLE -->

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>



                            <th>Username</th>

                            <th>Email</th>

                            <th>User Type</th>

                            <th width="160">Action</th>

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

                                            Edit

                                        </a>
                                        <a href="users.php?delete=<?php echo $row['ID']; ?>"

                                            class="delete-btn"

                                            onclick="return confirm('Are you sure you want to delete this user?')">

                                            Delete

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
                        ? "Edit User"
                        : "Add User";
                    ?>

                </div>

                <form method="POST">

                    <input type="hidden"

                        name="id"

                        value="<?php echo $edit_id; ?>">

                    <!-- Username -->

                    <div class="mb-3">

                        <label class="form-label">

                            Username

                        </label>

                        <input type="text"

                            name="username"

                            class="form-control"

                            placeholder="Enter username"

                            value="<?php echo $edit_username; ?>"

                            required>

                    </div>

                    <!-- Email -->

                    <div class="mb-3">

                        <label class="form-label">

                            Email

                        </label>

                        <input type="email"

                            name="email"

                            class="form-control"

                            placeholder="Enter email"

                            value="<?php echo $edit_email; ?>"

                            required>

                    </div>

                    <!-- User Type -->

                    <div class="mb-3">

                        <label class="form-label">

                            User Type

                        </label>

                        <select name="user_type"
                            class="custom-select">

                            <option value="Admin"
                                <?php if ($edit_type == "Admin") echo "selected"; ?>>

                                Admin

                            </option>

                            <option value="User"
                                <?php if ($edit_type == "User") echo "selected"; ?>>

                                User

                            </option>

                        </select>

                    </div>

                    <!-- Password -->

                    <div class="mb-3">

                        <label class="form-label">

                            Password

                        </label>

                        <input type="password"

                            name="password"

                            class="form-control"

                            placeholder="Enter password"

                            required>

                    </div>

                    <!-- Confirm Password -->

                    <div class="mb-4">

                        <label class="form-label">

                            Confirm Password

                        </label>
                        <?php if ($error != "") { ?>

                            <div class="alert alert-danger">

                                <?php echo $error; ?>

                            </div>

                        <?php } ?>

                        <input type="password"

                            name="confirm_password"

                            class="form-control"

                            placeholder="Confirm password"

                            required>

                    </div>
                    <!-- BUTTON -->

                    <button class="save-btn"

                        name="<?php
                                echo isset($_GET['edit'])
                                    ? 'update_user'
                                    : 'save_user';
                                ?>">

                        <?php
                        echo isset($_GET['edit'])
                            ? 'Update User'
                            : 'Save User';
                        ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>