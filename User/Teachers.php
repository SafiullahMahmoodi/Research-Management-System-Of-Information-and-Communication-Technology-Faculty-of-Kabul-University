<?php
include('../auth.php');



include('../db_connection.php');

// ===========================
// Insert Teacher
// ===========================
if(isset($_POST['save_teacher'])){

    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $education   = $_POST['education'];
    $department  = $_POST['department'];

    $conn->query("INSERT INTO teacher
    (Name, Last_Name, Email, Contact, Education, Department)

    VALUES

    ('$name','$lastname','$email','$contact','$education','$department')");

    header("Location: teachers.php");
    exit();
}

// ===========================
// Delete
// ===========================
if(isset($_GET['delete'])){

    $id = $_GET['delete'];

    $conn->query("DELETE FROM teacher
    WHERE ID='$id'");

    header("Location: teachers.php");
    exit();
}

// ===========================
// Edit
// ===========================
$edit_id="";
$edit_name="";
$edit_lastname="";
$edit_email="";
$edit_contact="";
$edit_education="";
$edit_department="";

if(isset($_GET['edit'])){

    $id=$_GET['edit'];

    $res=$conn->query("SELECT * FROM teacher
    WHERE ID='$id'");

    if($res->num_rows>0){

        $row=$res->fetch_assoc();

        $edit_id=$row['ID'];
        $edit_name=$row['Name'];
        $edit_lastname=$row['Last_Name'];
        $edit_email=$row['Email'];
        $edit_contact=$row['Contact'];
        $edit_education=$row['Education'];
        $edit_department=$row['Department'];
    }
}

// ===========================
// Update
// ===========================
if(isset($_POST['update_teacher'])){

    $id=$_POST['id'];
    $name=$_POST['name'];
    $lastname=$_POST['lastname'];
    $email=$_POST['email'];
    $contact=$_POST['contact'];
    $education=$_POST['education'];
    $department=$_POST['department'];

    $conn->query("UPDATE teacher SET

    Name='$name',
    Last_Name='$lastname',
    Email='$email',
    Contact='$contact',
    Education='$education',
    Department='$department'

    WHERE ID='$id'");

    header("Location: teachers.php");
    exit();
}

// ===========================
// Search
// ===========================
$search="";

if(isset($_GET['search'])){

    $search=$_GET['search'];

    $teacher_result=$conn->query("

    SELECT teacher.*, department.Name AS department_name

    FROM teacher

    LEFT JOIN department
    ON teacher.Department=department.ID

    WHERE

    teacher.Name LIKE '%$search%'
    OR teacher.Last_Name LIKE '%$search%'
    OR teacher.Email LIKE '%$search%'
    OR teacher.Contact LIKE '%$search%'
    OR teacher.Education LIKE '%$search%'
    ");

}else{

    $teacher_result=$conn->query("

    SELECT teacher.*, department.Name AS department_name

    FROM teacher

    LEFT JOIN department
    ON teacher.Department=department.ID
    ");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Teachers</title>
<link rel="stylesheet" href="style.css">

<link rel="stylesheet"
href="../css/bootstrap.min.css">

<script src="../js/bootstrap.bundle.min.js"></script>
<!-- 
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

    font-size:18px;

    font-weight:700;

    color:#0f172a;

    margin-bottom:15px;

    text-align:center;
}

/* =========================
   Inputs
========================= */

.form-control,
.custom-select{

    height:38px;

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
}

</style> -->

</head>

<body>

<?php include('header.php'); ?>

<div class="main-wrapper">

    <!-- TABLE -->

    <div class="table-section">

        <div class="search-wrapper">

            <form method="GET"
            class="search-form">

                <input type="text"

                name="search"

                class="search-input"

                placeholder="Search teachers..."

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

                        <th>ID</th>

                        <th>Name</th>

                        <th>Last Name</th>

                        <th>Email</th>

                        <th>Contact</th>

                        <th>Education</th>

                        <th>Department</th>

                        <th width="160">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($row=$teacher_result->fetch_assoc()){ ?>

                <tr>

                    <td><?php echo $row['ID']; ?></td>

                    <td><?php echo $row['Name']; ?></td>

                    <td><?php echo $row['Last_Name']; ?></td>

                    <td><?php echo $row['Email']; ?></td>

                    <td><?php echo $row['Contact']; ?></td>

                    <td><?php echo $row['Education']; ?></td>

                    <td><?php echo $row['department_name']; ?></td>

                    <td>

                        <div class="action-icons">

                            <a href="teachers.php?edit=<?php echo $row['ID']; ?>"
                            class="edit-btn">

                                Edit

                            </a>

                            <button class="delete-btn"

                            data-bs-toggle="modal"

                            data-bs-target="#deleteModal<?php echo $row['ID']; ?>">

                                Delete

                            </button>

                        </div>

                    </td>

                </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>
<!-- FORM -->

<div class="form-section">

    <div class="form-card">

        <div class="form-title">

            <?php
            echo isset($_GET['edit'])
            ? "Edit Teacher"
            : "Add Teacher";
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

                placeholder="Enter teacher name"

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

            <!-- Education -->

            <div class="mb-3">

                <label class="form-label">

                    Education

                </label>

                <input type="text"

                name="education"

                class="form-control"

                placeholder="Enter education"

                value="<?php echo $edit_education; ?>"

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

                    $d=$conn->query("SELECT * FROM department");

                    while($dep=$d->fetch_assoc()){

                    ?>

                    <option value="<?php echo $dep['ID']; ?>"

                    <?php
                    if($edit_department==$dep['ID'])
                    echo "selected";
                    ?>>

                        <?php echo $dep['Name']; ?>

                    </option>

                    <?php } ?>

                </select>

            </div>

            <!-- Button -->

            <button class="save-btn"

            name="<?php
            echo isset($_GET['edit'])
            ? 'update_teacher'
            : 'save_teacher';
            ?>">

                <?php
                echo isset($_GET['edit'])
                ? 'Update Teacher'
                : 'Save Teacher';
                ?>

            </button>

        </form>

    </div>

</div>

</div>

</body>
</html>