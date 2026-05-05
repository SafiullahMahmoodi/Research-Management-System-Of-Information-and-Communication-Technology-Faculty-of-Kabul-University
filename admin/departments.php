<?php
// departments.php

session_start();

include('../db_connection.php');

// ===========================
// Insert Department
// ===========================

if(isset($_POST['save_department'])){

    $id         = $_POST['id'];
    $department = $_POST['department'];

    $insert_query = "INSERT INTO department
    (ID, Name)

    VALUES

    ('$id','$department')";

    $conn->query($insert_query);

    header("Location: departments.php");
    exit();
}

// ===========================
// Delete Department
// ===========================

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];

    $delete_query = "DELETE FROM department
    WHERE ID='$delete_id'";

    $conn->query($delete_query);

    header("Location: departments.php");
    exit();
}

// ===========================
// Edit Department
// ===========================

$edit_id         = "";
$edit_department = "";

if(isset($_GET['edit'])){

    $edit_id = $_GET['edit'];

    $edit_query = "SELECT * FROM department
    WHERE ID='$edit_id'";

    $edit_result = $conn->query($edit_query);

    if($edit_result->num_rows > 0){

        $edit_row = $edit_result->fetch_assoc();

        $edit_department = $edit_row['Name'];
    }
}

// ===========================
// Update Department
// ===========================

if(isset($_POST['update_department'])){

    $id         = $_POST['id'];
    $department = $_POST['department'];

    $update_query = "UPDATE department SET

    Name='$department'

    WHERE ID='$id'";

    $conn->query($update_query);

    header("Location: departments.php");
    exit();
}

// ===========================
// Search
// ===========================

$search = "";

if(isset($_GET['search'])){

    $search = $_GET['search'];

    $department_query = "SELECT * FROM department

    WHERE

    ID LIKE '%$search%'
    OR Name LIKE '%$search%'";

}else{

    $department_query = "SELECT * FROM department";
}

$department_result = $conn->query($department_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Departments</title>

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

    color:white;

    text-decoration:none;

    font-size:13px;

    font-weight:500;

    padding:8px 14px;

    border-radius:8px;

    transition:0.3s;
}

.header-menu a:hover{

    background:white;

    color:#0f9d58;
}

.header-menu .active{

    background:white;

    color:#0f9d58;
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

    width:75%;

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

    transition:0.3s;
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

    transition:0.3s;
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

    transition:0.3s;
}

.delete-btn:hover{

    background:#b91c1c;
}

/* =========================
   Form Section
========================= */

.form-section{

    width:25%;

    min-width:260px;

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

    font-size:20px;

    font-weight:700;

    color:#0f172a;

    margin-bottom:20px;

    text-align:center;
}

.form-label{

    font-size:13px;

    font-weight:700;

    margin-bottom:6px;

    color:#374151;
}

/* =========================
   Inputs
========================= */

.form-control{

    height:40px;

    border-radius:8px;

    border:1px solid #d1d5db;

    font-size:13px;

    background:#ffffff;

    padding:0 10px;

    transition:0.3s;
}

.form-control:focus{

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

    transition:0.3s;
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

    .main-header{

        flex-direction:column;

        height:auto;

        gap:10px;

        padding:15px;
    }

    .header-menu{

        flex-wrap:wrap;

        justify-content:center;
    }
}

</style>

</head>

<body>

<header class="main-header">

    <div class="header-menu">

        <a href="users.php">Users</a>

        <a href="departments.php"
        class="active">

            Departments

        </a>

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

        <div class="search-wrapper">

            <form method="GET"
            class="search-form">

                <input type="text"

                name="search"

                class="search-input"

                placeholder="Search departments..."

                value="<?php echo $search; ?>">

                <button type="submit"
                class="search-btn">

                    Search

                </button>

            </form>

        </div>

        <div class="table-card">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th width="100">ID</th>

                        <th>Department Name</th>

                        <th width="180">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($row = $department_result->fetch_assoc()){ ?>

                    <tr>

                        <td><?php echo $row['ID']; ?></td>

                        <td><?php echo $row['Name']; ?></td>

                        <td>

                            <div class="action-icons">

                                <a href="departments.php?edit=<?php echo $row['ID']; ?>"
                                class="edit-btn">

                                    Edit

                                </a>

                                <a href="departments.php?delete=<?php echo $row['ID']; ?>"
                                class="delete-btn"

                                onclick="return confirm('Are you sure to delete this department?')">

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
                ? "Edit Department"
                : "Add Department";
                ?>

            </div>

            <form method="POST">

                <!-- ID -->

                <div class="mb-3">

                    <label class="form-label">

                        ID

                    </label>

                    <input type="text"

                    name="id"

                    class="form-control"

                    placeholder="Enter department ID"

                    required

                    value="<?php echo $edit_id; ?>"

                    <?php if(isset($_GET['edit'])) echo "readonly"; ?>>

                </div>

                <!-- Department Name -->

                <div class="mb-4">

                    <label class="form-label">

                        Department Name

                    </label>

                    <input type="text"

                    name="department"

                    class="form-control"

                    placeholder="Enter department name"

                    required

                    value="<?php echo $edit_department; ?>">

                </div>

                <!-- Save Button -->

                <button type="submit"

                class="save-btn"

                name="<?php
                echo isset($_GET['edit'])
                ? 'update_department'
                : 'save_department';
                ?>">

                    <?php
                    echo isset($_GET['edit'])
                    ? 'Update Department'
                    : 'Save Department';
                    ?>

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>