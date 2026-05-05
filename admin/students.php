<?php
session_start();

include('../db_connection.php');

// ===========================
// Insert Student
// ===========================

if(isset($_POST['save_student'])){

    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $department  = $_POST['department'];

    $insert_query = "INSERT INTO students
    (Name, Last_Name, Email, Contact, Department)

    VALUES

    ('$name','$lastname','$email','$contact','$department')";

    $conn->query($insert_query);

    header("Location: students.php");
    exit();
}

// ===========================
// Delete Student
// ===========================

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];

    $delete_query = "DELETE FROM students
    WHERE ID='$delete_id'";

    $conn->query($delete_query);

    header("Location: students.php");
    exit();
}

// ===========================
// Edit Student
// ===========================

$edit_id         = "";
$edit_name       = "";
$edit_lastname   = "";
$edit_email      = "";
$edit_contact    = "";
$edit_department = "";

if(isset($_GET['edit'])){

    $edit_id = $_GET['edit'];

    $edit_query = "SELECT * FROM students
    WHERE ID='$edit_id'";

    $edit_result = $conn->query($edit_query);

    if($edit_result->num_rows > 0){

        $edit_row = $edit_result->fetch_assoc();

        $edit_name       = $edit_row['Name'];
        $edit_lastname   = $edit_row['Last_Name'];
        $edit_email      = $edit_row['Email'];
        $edit_contact    = $edit_row['Contact'];
        $edit_department = $edit_row['Department'];
    }
}

// ===========================
// Update Student
// ===========================

if(isset($_POST['update_student'])){

    $id          = $_POST['id'];
    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $department  = $_POST['department'];

    $update_query = "UPDATE students SET

    Name='$name',
    Last_Name='$lastname',
    Email='$email',
    Contact='$contact',
    Department='$department'

    WHERE ID='$id'";

    $conn->query($update_query);

    header("Location: students.php");
    exit();
}

// ===========================
// Search
// ===========================

$search = "";

if(isset($_GET['search'])){

    $search = $_GET['search'];

    $student_query = "SELECT students.*, department.Name AS department_name

    FROM students

    LEFT JOIN department
    ON students.Department = department.ID

    WHERE

    students.ID LIKE '%$search%'
    OR students.Name LIKE '%$search%'
    OR students.Last_Name LIKE '%$search%'
    OR students.Email LIKE '%$search%'
    OR students.Contact LIKE '%$search%'
    OR department.Name LIKE '%$search%'";

}else{

    $student_query = "SELECT students.*, department.Name AS department_name

    FROM students

    LEFT JOIN department
    ON students.Department = department.ID";
}

$student_result = $conn->query($student_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Students</title>

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

        <a href="users.php">Users</a>

        <a href="departments.php">Departments</a>

        <a href="teachers.php">Teachers</a>

        <a href="students.php" class="active">Students</a>

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

                placeholder="Search students..."

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

                        <th>Name</th>

                        <th>Last Name</th>

                        <th>Email</th>

                        <th>Contact</th>

                        <th>Department</th>

                        <th width="160">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($row = $student_result->fetch_assoc()){ ?>

                <tr>

                    <td><?php echo $row['ID']; ?></td>

                    <td><?php echo $row['Name']; ?></td>

                    <td><?php echo $row['Last_Name']; ?></td>

                    <td><?php echo $row['Email']; ?></td>

                    <td><?php echo $row['Contact']; ?></td>

                    <td><?php echo $row['department_name']; ?></td>

                    <td>

                        <div class="action-icons">

                            <a href="students.php?edit=<?php echo $row['ID']; ?>"
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

                                    Delete Student

                                </h5>

                                <button class="btn-close btn-close-white"
                                data-bs-dismiss="modal">

                                </button>

                            </div>

                            <div class="modal-body text-center">

                                Delete

                                <strong>

                                    <?php echo $row['Name']; ?>

                                </strong>

                                ?

                            </div>

                            <div class="modal-footer">

                                <button class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                    Cancel

                                </button>

                                <a href="students.php?delete=<?php echo $row['ID']; ?>"

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
                ? "Edit Student"
                : "Add Student";
                ?>

            </div>

            <form method="POST">

                <input type="hidden"

                name="id"

                value="<?php echo $edit_id; ?>">

                <!-- Name -->

                <div class="mb-3">

                    <label class="form-label">

                        Name

                    </label>

                    <input type="text"

                    name="name"

                    class="form-control"

                    placeholder="Enter student name"

                    value="<?php echo $edit_name; ?>"

                    required>

                </div>

                <!-- Last Name -->

                <div class="mb-3">

                    <label class="form-label">

                        Last Name

                    </label>

                    <input type="text"

                    name="lastname"

                    class="form-control"

                    placeholder="Enter last name"

                    value="<?php echo $edit_lastname; ?>"

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

                <!-- Contact -->

                <div class="mb-3">

                    <label class="form-label">

                        Contact

                    </label>

                    <input type="text"

                    name="contact"

                    class="form-control"

                    placeholder="Enter contact"

                    value="<?php echo $edit_contact; ?>"

                    required>

                </div>

                <!-- Department -->

                <div class="mb-4">

                    <label class="form-label">

                        Department

                    </label>

                    <select name="department"
                    class="custom-select">

                        <?php

                        $department_query = "SELECT * FROM department";

                        $department_result = $conn->query($department_query);

                        while($department = $department_result->fetch_assoc()){

                        ?>

                        <option value="<?php echo $department['ID']; ?>"

                        <?php
                        if($edit_department == $department['ID']){
                            echo "selected";
                        }
                        ?>>

                            <?php echo $department['Name']; ?>

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- BUTTON -->

                <button class="save-btn"

                name="<?php
                echo isset($_GET['edit'])
                ? 'update_student'
                : 'save_student';
                ?>">

                    <?php
                    echo isset($_GET['edit'])
                    ? 'Update Student'
                    : 'Save Student';
                    ?>

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>