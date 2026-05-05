<?php
session_start();

include('../db_connection.php');

// ===========================
// Insert Article
// ===========================

if(isset($_POST['save_article'])){

    $id          = $_POST['id'];
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $teacher_id  = $_POST['teacher_id'];
    $student_id  = $_POST['student_id'];
    $department  = $_POST['department'];
    $date        = $_POST['date'];

    $pdf_file = "";

    if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != ""){

        $pdf_file = time() . "_" . $_FILES['pdf_file']['name'];

        move_uploaded_file(
            $_FILES['pdf_file']['tmp_name'],
            "../uploads/" . $pdf_file
        );
    }

    $conn->query("INSERT INTO articles
    (ID, Title, Description, Category, Teacher_ID, Student_ID, Department, PDF_File, Date)

    VALUES

    ('$id','$title','$description','$category','$teacher_id','$student_id','$department','$pdf_file','$date')");

    header("Location: articles.php");
    exit();
}

// ===========================
// Delete
// ===========================

if(isset($_GET['delete'])){

    $id = $_GET['delete'];

    $conn->query("DELETE FROM articles
    WHERE ID='$id'");

    header("Location: articles.php");
    exit();
}

// ===========================
// Edit
// ===========================

$edit_id          = "";
$edit_title       = "";
$edit_description = "";
$edit_category    = "";
$edit_teacher     = "";
$edit_student     = "";
$edit_department  = "";
$edit_date        = "";

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $res = $conn->query("SELECT * FROM articles
    WHERE ID='$id'");

    if($res->num_rows > 0){

        $row = $res->fetch_assoc();

        $edit_id          = $row['ID'];
        $edit_title       = $row['Title'];
        $edit_description = $row['Description'];
        $edit_category    = $row['Category'];
        $edit_teacher     = $row['Teacher_ID'];
        $edit_student     = $row['Student_ID'];
        $edit_department  = $row['Department'];
        $edit_date        = $row['Date'];
    }
}

// ===========================
// Update
// ===========================

if(isset($_POST['update_article'])){

    $id          = $_POST['id'];
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $teacher_id  = $_POST['teacher_id'];
    $student_id  = $_POST['student_id'];
    $department  = $_POST['department'];
    $date        = $_POST['date'];

    $query = "UPDATE articles SET

    Title='$title',
    Description='$description',
    Category='$category',
    Teacher_ID='$teacher_id',
    Student_ID='$student_id',
    Department='$department',
    Date='$date'";

    // PDF Update

    if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != ""){

        $pdf_file = time() . "_" . $_FILES['pdf_file']['name'];

        move_uploaded_file(
            $_FILES['pdf_file']['tmp_name'],
            "../uploads/" . $pdf_file
        );

        $query .= ", PDF_File='$pdf_file'";
    }

    $query .= " WHERE ID='$id'";

    $conn->query($query);

    header("Location: articles.php");
    exit();
}

// ===========================
// Search
// ===========================

$search = "";

if(isset($_GET['search'])){

    $search = $_GET['search'];

    $article_result = $conn->query("

    SELECT articles.*,
    teacher.Name AS teacher_name,
    students.Name AS student_name,
    department.Name AS department_name

    FROM articles

    LEFT JOIN teacher
    ON articles.Teacher_ID = teacher.ID

    LEFT JOIN students
    ON articles.Student_ID = students.ID

    LEFT JOIN department
    ON articles.Department = department.ID

    WHERE

    articles.ID LIKE '%$search%'
    OR articles.Title LIKE '%$search%'
    OR articles.Category LIKE '%$search%'
    OR teacher.Name LIKE '%$search%'
    OR students.Name LIKE '%$search%'
    OR department.Name LIKE '%$search%'
    ");

}else{

    $article_result = $conn->query("

    SELECT articles.*,
    teacher.Name AS teacher_name,
    students.Name AS student_name,
    department.Name AS department_name

    FROM articles

    LEFT JOIN teacher
    ON articles.Teacher_ID = teacher.ID

    LEFT JOIN students
    ON articles.Student_ID = students.ID

    LEFT JOIN department
    ON articles.Department = department.ID
    ");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Articles</title>

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

    color:#d1fae5;

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

    text-decoration:none;
}

.delete-btn:hover{

    background:#b91c1c;

    color:white;
}

.pdf-btn{

    background:#f59e0b;

    color:white;

    padding:6px 12px;

    border-radius:7px;

    text-decoration:none;

    font-size:11px;

    font-weight:700;
}

.pdf-btn:hover{

    background:#d97706;

    color:white;
}

/* =========================
   Form Section
========================= */

.form-section{

    width:22%;

    min-width:280px;

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

    border-radius:8px;

    border:1px solid #d1d5db;

    font-size:12px;

    background:#ffffff;

    padding:10px;

    transition:0.3s;
}

.form-control:focus,
.custom-select:focus{

    border-color:#0f9d58;

    box-shadow:0 0 0 0.15rem rgba(15,157,88,0.15);

    outline:none;
}

textarea{
    resize:none;
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

</style>

</head>

<body>

<header class="main-header">

    <div class="header-menu">

        <a href="users.php">Users</a>

        <a href="departments.php">Departments</a>

        <a href="teachers.php">Teachers</a>

        <a href="students.php">Students</a>

        <a href="articles.php" class="active">Articles</a>

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

    <!-- TABLE -->

    <div class="table-section">

        <div class="search-wrapper">

            <form method="GET"
            class="search-form">

                <input type="text"

                name="search"

                class="search-input"

                placeholder="Search articles..."

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

                        <th>Title</th>

                        <th>Category</th>

                        <th>Teacher</th>

                        <th>Student</th>

                        <th>Department</th>

                        <th>Date</th>

                        <th width="220">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($row = $article_result->fetch_assoc()){ ?>

                <tr>

                    <td><?php echo $row['ID']; ?></td>

                    <td><?php echo $row['Title']; ?></td>

                    <td><?php echo $row['Category']; ?></td>

                    <td><?php echo $row['teacher_name']; ?></td>

                    <td><?php echo $row['student_name']; ?></td>

                    <td><?php echo $row['department_name']; ?></td>

                    <td><?php echo $row['Date']; ?></td>

                    <td>

                        <div class="action-icons">

                            <?php if($row['PDF_File'] != ""){ ?>

                            <a href="../uploads/<?php echo $row['PDF_File']; ?>"
                            target="_blank"
                            class="pdf-btn">

                                PDF

                            </a>

                            <?php } ?>

                            <a href="articles.php?edit=<?php echo $row['ID']; ?>"
                            class="edit-btn">

                                Edit

                            </a>

                            <a href="articles.php?delete=<?php echo $row['ID']; ?>"
                            class="delete-btn"

                            onclick="return confirm('Are you sure to delete this article?')">

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

    <!-- FORM -->

    <div class="form-section">

        <div class="form-card">

            <div class="form-title">

                <?php
                echo isset($_GET['edit'])
                ? "Edit Article"
                : "Add Article";
                ?>

            </div>

            <form method="POST"
            enctype="multipart/form-data">

                <!-- ID -->

                <div class="mb-3">

                    <label class="form-label">

                        ID

                    </label>

                    <input type="text"

                    name="id"

                    class="form-control"

                    value="<?php echo $edit_id; ?>"

                    required>

                </div>

                <!-- Title -->

                <div class="mb-3">

                    <label class="form-label">

                        Title

                    </label>

                    <input type="text"

                    name="title"

                    class="form-control"

                    value="<?php echo $edit_title; ?>"

                    required>

                </div>

                <!-- Description -->

                <div class="mb-3">

                    <label class="form-label">

                        Description

                    </label>

                    <textarea name="description"

                    class="form-control"

                    rows="4"

                    required><?php echo $edit_description; ?></textarea>

                </div>

                <!-- Category -->

                <div class="mb-3">

                    <label class="form-label">

                        Category

                    </label>

                    <input type="text"

                    name="category"

                    class="form-control"

                    value="<?php echo $edit_category; ?>"

                    required>

                </div>

                <!-- Teacher -->

                <div class="mb-3">

                    <label class="form-label">

                        Teacher

                    </label>

                    <select name="teacher_id"
                    class="custom-select">

                        <?php

                        $teacher = $conn->query("SELECT * FROM teacher");

                        while($t = $teacher->fetch_assoc()){

                        ?>

                        <option value="<?php echo $t['ID']; ?>"

                        <?php
                        if($edit_teacher == $t['ID'])
                        echo "selected";
                        ?>>

                            <?php echo $t['Name']; ?>

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- Student -->

                <div class="mb-3">

                    <label class="form-label">

                        Student

                    </label>

                    <select name="student_id"
                    class="custom-select">

                        <?php

                        $student = $conn->query("SELECT * FROM student");

                        while($s = $student->fetch_assoc()){

                        ?>

                        <option value="<?php echo $s['ID']; ?>"

                        <?php
                        if($edit_student == $s['ID'])
                        echo "selected";
                        ?>>

                            <?php echo $s['Name']; ?>

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- Department -->

                <div class="mb-3">

                    <label class="form-label">

                        Department

                    </label>

                    <select name="department"
                    class="custom-select">

                        <?php

                        $dep = $conn->query("SELECT * FROM department");

                        while($d = $dep->fetch_assoc()){

                        ?>

                        <option value="<?php echo $d['ID']; ?>"

                        <?php
                        if($edit_department == $d['ID'])
                        echo "selected";
                        ?>>

                            <?php echo $d['Name']; ?>

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- PDF -->

                <div class="mb-3">

                    <label class="form-label">

                        PDF File

                    </label>

                    <input type="file"

                    name="pdf_file"

                    class="form-control">

                </div>

                <!-- Date -->

                <div class="mb-4">

                    <label class="form-label">

                        Date

                    </label>

                    <input type="date"

                    name="date"

                    class="form-control"

                    value="<?php echo $edit_date; ?>"

                    required>

                </div>

                <!-- Save -->

                <button class="save-btn"

                name="<?php
                echo isset($_GET['edit'])
                ? 'update_article'
                : 'save_article';
                ?>">

                    <?php
                    echo isset($_GET['edit'])
                    ? 'Update Article'
                    : 'Save Article';
                    ?>

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>