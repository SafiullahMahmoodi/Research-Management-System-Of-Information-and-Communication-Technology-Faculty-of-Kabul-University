<?php
session_start();

include('../db_connection.php');

// ===========================
// Insert User
// ===========================

if(isset($_POST['save_user'])){

    $username  = $_POST['username'];
    $email     = $_POST['email'];
    $user_type = $_POST['user_type'];
    $password  = md5($_POST['password']);

    $insert_query = "INSERT INTO users
    (Username, Email, usertype, Password)

    VALUES

    ('$username','$email','$user_type','$password')";

    $conn->query($insert_query);

    header("Location: users.php");
    exit();
}

// ===========================
// Delete User
// ===========================

if(isset($_GET['delete'])){

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

if(isset($_GET['edit'])){

    $edit_id = $_GET['edit'];

    $edit_query = "SELECT * FROM users
    WHERE ID='$edit_id'";

    $edit_result = $conn->query($edit_query);

    if($edit_result->num_rows > 0){

        $edit_row = $edit_result->fetch_assoc();

        $edit_username = $edit_row['Username'];
        $edit_email    = $edit_row['Email'];
        $edit_type     = $edit_row['usertype'];
    }
}

// ===========================
// Update User
// ===========================

if(isset($_POST['update_user'])){

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

if(isset($_GET['search'])){

    $search = $_GET['search'];

    $user_query = "SELECT * FROM users

    WHERE

    ID LIKE '%$search%'
    OR Username LIKE '%$search%'
    OR Email LIKE '%$search%'
    OR usertype LIKE '%$search%'";

}else{

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

<link rel="stylesheet"
href="../css/bootstrap.min.css">

<script src="../js/bootstrap.bundle.min.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#eef2f7;
    font-family:Segoe UI;
    overflow:hidden;
}

/* =========================
   Header
========================= */

.main-header{

    background:#0f9d58;

    height:65px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:0 22px;
}

.header-menu{

    display:flex;

    gap:18px;
}

.header-menu a{

    color:#e5e7eb;

    text-decoration:none;

    font-size:13px;

    font-weight:600;

    padding:8px 14px;

    border-radius:8px;

    transition:0.3s;
}

.header-menu a:hover{

    background:white;

    color:#0f9d58;
}

/* Active Menu */

.header-menu a.active{

    background:white;

    color:#0f9d58;

    font-weight:700;
}

.header-buttons{

    display:flex;

    gap:10px;
}

.header-btn{

    background:white;

    color:#0f9d58;

    padding:7px 16px;

    border-radius:10px;

    text-decoration:none;

    font-size:12px;

    font-weight:700;

    transition:0.3s;
}

.header-btn:hover{

    background:#d1fae5;
}

/* =========================
   Main Layout
========================= */

.main-wrapper{

    display:flex;

    gap:18px;

    padding:18px;

    height:calc(100vh - 65px);
}

/* =========================
   Table Section
========================= */

.table-section{

    width:78%;

    display:flex;

    flex-direction:column;

    overflow:hidden;
}

/* =========================
   Search
========================= */

.search-wrapper{

    display:flex;

    justify-content:center;

    margin-bottom:15px;
}

.search-form{

    display:flex;

    width:100%;

    max-width:520px;
}

.search-input{

    width:100%;

    border:none;

    outline:none;

    padding:11px 14px;

    border-radius:12px 0 0 12px;

    border:1px solid #d1d5db;

    background:white;

    font-size:13px;

    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.search-btn{

    border:none;

    background:#0f9d58;

    color:white;

    padding:11px 20px;

    border-radius:0 12px 12px 0;

    font-size:13px;

    font-weight:700;

    cursor:pointer;
}

.search-btn:hover{

    background:#0c7c45;
}

/* =========================
   Table Card
========================= */

.table-card{

    background:white;

    border-radius:18px;

    padding:15px;

    overflow:auto;

    box-shadow:0 5px 18px rgba(0,0,0,0.08);

    flex:1;
}

.table{

    border:1px solid #d1d5db;
}

.table thead{

    background:#0f9d58;

    color:white;
}

.table th,
.table td{

    border:1px solid #d1d5db !important;

    vertical-align:middle;

    font-size:12px;

    padding:10px;
}

/* =========================
   Buttons
========================= */

.action-icons{

    display:flex;

    gap:8px;
}

.edit-btn{

    background:#2563eb;

    color:white;

    padding:6px 12px;

    border-radius:7px;

    text-decoration:none;

    font-size:11px;

    font-weight:700;
}

.edit-btn:hover{

    background:#1d4ed8;

    color:white;
}

.delete-btn{

    border:none;

    background:#dc2626;

    color:white;

    padding:6px 12px;

    border-radius:7px;

    font-size:11px;

    font-weight:700;

    cursor:pointer;
}

.delete-btn:hover{

    background:#b91c1c;
}

/* =========================
   Form Section
========================= */

.form-section{

    width:22%;

    min-width:270px;

    height:100%;
}

/* =========================
   Form Card
========================= */

.form-card{

    background:white;

    border-radius:16px;

    padding:16px;

    box-shadow:0 5px 18px rgba(0,0,0,0.08);

    height:100%;

    overflow-y:auto;

    display:flex;

    flex-direction:column;
}

.form-title{

    font-size:18px;

    font-weight:700;

    color:#0f172a;

    margin-bottom:15px;

    text-align:center;
}

.form-label{

    font-size:12px;

    font-weight:700;

    margin-bottom:6px;

    color:#374151;
}

/* =========================
   Inputs
========================= */

.form-control,
.custom-select{

    height:40px;

    border-radius:8px;

    border:1px solid #d1d5db;

    font-size:12px;

    background:#ffffff;

    padding:0 10px;

    transition:0.3s;
}

.form-control:focus,
.custom-select:focus{

    border-color:#0f9d58;

    box-shadow:0 0 0 0.15rem rgba(15,157,88,0.15);

    outline:none;
}

/* =========================
   Save Button
========================= */

.save-btn{

    width:100%;

    background:#0f9d58;

    color:white;

    border:none;

    padding:10px;

    border-radius:8px;

    font-size:13px;

    font-weight:700;
}

.save-btn:hover{

    background:#0c7c45;
}

/* =========================
   Responsive
========================= */

@media(max-width:992px){

    body{
        overflow:auto;
    }

    .main-wrapper{

        flex-direction:column;

        height:auto;
    }

    .table-section,
    .form-section{

        width:100%;
    }

    .form-card{

        height:auto;
    }

    .header-menu{

        flex-wrap:wrap;
    }
}

</style>

</head>

<body>

<header class="main-header">

    <div class="header-menu">

        <a href="users.php" class="active">Users</a>

        <a href="departments.php">Departments</a>

        <a href="teachers.php">Teachers</a>

        <a href="students.php">Students</a>

        <a href="articles.php">Articles</a>

        <a href="books.php">Books</a>

        <a href="translatedbooks.php">Translated Books</a>

        <a href="thesises.php">Thesises</a>

    </div>

    <div class="header-buttons">

        <a href="dashboard.php"
        class="header-btn">

            Dashboard

        </a>

        <a href="../index.php"
        class="header-btn">

            Logout

        </a>

    </div>

</header>

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

                        <th>ID</th>

                        <th>Username</th>

                        <th>Email</th>

                        <th>User Type</th>

                        <th width="160">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($row = $user_result->fetch_assoc()){ ?>

                <tr>

                    <td><?php echo $row['ID']; ?></td>

                    <td><?php echo $row['Username']; ?></td>

                    <td><?php echo $row['Email']; ?></td>

                    <td><?php echo $row['usertype']; ?></td>

                    <td>

                        <div class="action-icons">

                            <a href="users.php?edit=<?php echo $row['ID']; ?>"
                            class="edit-btn">

                                Edit

                            </a>

                            <button type="button"

                            class="delete-btn"

                            data-bs-toggle="modal"

                            data-bs-target="#deleteModal<?php echo $row['ID']; ?>">

                                Delete

                            </button>

                        </div>

                    </td>

                </tr>

                <!-- DELETE MODAL -->

                <div class="modal fade"

                id="deleteModal<?php echo $row['ID']; ?>">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content"
                        style="border-radius:16px;">

                            <div class="modal-header bg-danger text-white">

                                <h5 class="modal-title">

                                    Delete User

                                </h5>

                                <button class="btn-close btn-close-white"
                                data-bs-dismiss="modal">

                                </button>

                            </div>

                            <div class="modal-body text-center">

                                Delete

                                <strong>

                                    <?php echo $row['Username']; ?>

                                </strong>

                                ?

                            </div>

                            <div class="modal-footer">

                                <button class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                    Cancel

                                </button>

                                <a href="users.php?delete=<?php echo $row['ID']; ?>"

                                class="btn btn-danger">

                                    Delete

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

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
                        <?php if($edit_type=="Admin") echo "selected"; ?>>

                            Admin

                        </option>

                        <option value="User"
                        <?php if($edit_type=="User") echo "selected"; ?>>

                            User

                        </option>

                    </select>

                </div>

                <!-- Password -->

                <?php if(!isset($_GET['edit'])){ ?>

                <div class="mb-4">

                    <label class="form-label">

                        Password

                    </label>

                    <input type="password"

                    name="password"

                    class="form-control"

                    placeholder="Enter password"

                    required>

                </div>

                <?php } ?>

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