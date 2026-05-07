<?php
session_start();

include('../db_connection.php');

// ===========================
// CREATE PDF FOLDER
// ===========================

if(!file_exists("../PDF_File")){

    mkdir("../PDF_File",0777,true);
}

// ===========================
// INSERT BOOK
// ===========================

if(isset($_POST['save_book'])){

    $id            = mysqli_real_escape_string($conn,$_POST['id']);
    $title         = mysqli_real_escape_string($conn,$_POST['title']);
    $description   = mysqli_real_escape_string($conn,$_POST['description']);
    $category      = mysqli_real_escape_string($conn,$_POST['category']);
    $author        = mysqli_real_escape_string($conn,$_POST['author']);
    $department_id = mysqli_real_escape_string($conn,$_POST['department']);
    $pages         = mysqli_real_escape_string($conn,$_POST['pages']);
    $publish_date  = mysqli_real_escape_string($conn,$_POST['publish_date']);

    // ===========================
    // CHECK AUTHOR
    // ===========================

    $check_teacher = $conn->query("
    SELECT ID
    FROM teacher
    WHERE ID='$author'
    ");

    if($check_teacher->num_rows == 0){

        die("Selected Author does not exist.");
    }

    // ===========================
    // PDF FILE
    // ===========================

    $pdf_file = "";

    if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != ""){

        $file_name = $_FILES['pdf_file']['name'];
        $file_tmp  = $_FILES['pdf_file']['tmp_name'];
        $file_size = $_FILES['pdf_file']['size'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if($extension != "pdf"){

            die("Only PDF files are allowed.");
        }

        if($file_size > 209715200){

            die("File size must be less than 200MB.");
        }

        $pdf_file = time() . "_" . $file_name;

        move_uploaded_file(
            $file_tmp,
            "../PDF_File/" . $pdf_file
        );
    }

    // ===========================
    // INSERT
    // ===========================

    $conn->query("
    INSERT INTO books
    (
        ID,
        Title,
        Description,
        Category,
        Author,
        Department,
        pages,
        PDF_File,
        Publish_Date
    )

    VALUES
    (
        '$id',
        '$title',
        '$description',
        '$category',
        '$author',
        '$department_id',
        '$pages',
        '$pdf_file',
        '$publish_date'
    )
    ");

    header("Location: books.php");
    exit();
}

// ===========================
// DELETE BOOK
// ===========================

if(isset($_GET['delete'])){

    $id = $_GET['delete'];

    $pdf = $conn->query("
    SELECT PDF_File
    FROM books
    WHERE ID='$id'
    ");

    if($pdf->num_rows > 0){

        $p = $pdf->fetch_assoc();

        if(
            $p['PDF_File'] != ""
            &&
            file_exists("../PDF_File/".$p['PDF_File'])
        ){

            unlink("../PDF_File/".$p['PDF_File']);
        }
    }

    $conn->query("
    DELETE FROM books
    WHERE ID='$id'
    ");

    header("Location: books.php");
    exit();
}

// ===========================
// EDIT BOOK
// ===========================

$edit_id            = "";
$edit_title         = "";
$edit_description   = "";
$edit_category      = "";
$edit_author        = "";
$edit_department_id = "";
$edit_pages         = "";
$edit_publish_date  = "";

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $res = $conn->query("
    SELECT *
    FROM books
    WHERE ID='$id'
    ");

    if($res->num_rows > 0){

        $row = $res->fetch_assoc();

        $edit_id            = $row['ID'];
        $edit_title         = $row['Title'];
        $edit_description   = $row['Description'];
        $edit_category      = $row['Category'];
        $edit_author        = $row['Author'];
        $edit_department_id = $row['department'];
        $edit_pages         = $row['pages'];
        $edit_publish_date  = $row['Publish_Date'];
    }
}

// ===========================
// UPDATE BOOK
// ===========================

if(isset($_POST['update_book'])){

    $id            = mysqli_real_escape_string($conn,$_POST['id']);
    $title         = mysqli_real_escape_string($conn,$_POST['title']);
    $description   = mysqli_real_escape_string($conn,$_POST['description']);
    $category      = mysqli_real_escape_string($conn,$_POST['category']);
    $author        = mysqli_real_escape_string($conn,$_POST['author']);
    $department_id = mysqli_real_escape_string($conn,$_POST['department']);
    $pages         = mysqli_real_escape_string($conn,$_POST['pages']);
    $publish_date  = mysqli_real_escape_string($conn,$_POST['publish_date']);

    $query = "
    UPDATE books SET

    Title='$title',
    Description='$description',
    Category='$category',
    Author='$author',
    department='$department_id',
    pages='$pages',
    Publish_Date='$publish_date'
    ";

    // ===========================
    // UPDATE PDF
    // ===========================

    if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != ""){

        $file_name = $_FILES['pdf_file']['name'];
        $file_tmp  = $_FILES['pdf_file']['tmp_name'];
        $file_size = $_FILES['pdf_file']['size'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if($extension != "pdf"){

            die("Only PDF files are allowed.");
        }

        if($file_size > 209715200){

            die("File size must be less than 200MB.");
        }

        // DELETE OLD FILE

        $old = $conn->query("
        SELECT PDF_File
        FROM books
        WHERE ID='$id'
        ");

        if($old->num_rows > 0){

            $o = $old->fetch_assoc();

            if(
                $o['PDF_File'] != ""
                &&
                file_exists("../PDF_File/".$o['PDF_File'])
            ){

                unlink("../PDF_File/".$o['PDF_File']);
            }
        }

        $pdf_file = time() . "_" . $file_name;

        move_uploaded_file(
            $file_tmp,
            "../PDF_File/" . $pdf_file
        );

        $query .= ", PDF_File='$pdf_file'";
    }

    $query .= " WHERE ID='$id'";

    $conn->query($query);

    header("Location: books.php");
    exit();
}

// ===========================
// SEARCH BOOKS
// ===========================

$search = "";

if(isset($_GET['search'])){

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

    $book_result = $conn->query("

    SELECT books.*,
    teacher.Name AS author_name,
    department.Name AS department_name

    FROM books

    LEFT JOIN teacher
    ON books.Author = teacher.ID

    LEFT JOIN department
    ON books.Department = department.ID

    WHERE

    books.ID LIKE '%$search%'
    OR books.Title LIKE '%$search%'
    OR books.Category LIKE '%$search%'
    OR teacher.Name LIKE '%$search%'
    OR department.Name LIKE '%$search%'

    ORDER BY books.ID DESC
    ");

}else{

    $book_result = $conn->query("

    SELECT books.*,
    teacher.Name AS author_name,
    department.Name AS department_name

    FROM books

    LEFT JOIN teacher
    ON books.Author = teacher.ID

    LEFT JOIN department
    ON books.Department = department.ID

    ORDER BY books.ID DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Books</title>

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
}

.main-wrapper{

    display:flex;

    gap:18px;

    padding:18px;

    height:calc(100vh - 65px);
}

.table-section{

    width:78%;

    display:flex;

    flex-direction:column;

    overflow:hidden;
}

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
}

.search-btn{

    border:none;

    background:#0f9d58;

    color:white;

    padding:11px 20px;

    border-radius:0 12px 12px 0;

    font-size:13px;

    font-weight:700;
}

.table-card{

    background:white;

    border-radius:18px;

    padding:15px;

    overflow:auto;

    box-shadow:0 5px 18px rgba(0,0,0,0.08);

    flex:1;
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

.delete-btn{

    background:#dc2626;

    color:white;

    padding:6px 12px;

    border-radius:7px;

    text-decoration:none;

    font-size:11px;

    font-weight:700;
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

.form-section{

    width:22%;

    min-width:270px;

    height:calc(100vh - 100px);
}

.form-card{

    background:white;

    border-radius:16px;

    padding:14px;

    box-shadow:0 5px 18px rgba(0,0,0,0.08);

    height:100%;

    overflow-y:auto;
}

.form-title{

    font-size:18px;

    font-weight:700;

    margin-bottom:15px;

    text-align:center;
}

.form-control,
.custom-select{

    border-radius:8px;

    border:1px solid #d1d5db;

    font-size:12px;

    padding:8px;
}

textarea{

    resize:none;

    height:70px !important;
}

.save-btn{

    width:100%;

    background:#0f9d58;

    color:white;

    border:none;

    padding:10px;

    border-radius:8px;

    font-size:13px;

    font-weight:700;

    margin-top:10px;
}

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

<a href="articles.php">Articles</a>

<a href="books.php" class="active">Books</a>

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

<div class="table-section">

<div class="search-wrapper">

<form method="GET"
class="search-form">

<input type="text"

name="search"

class="search-input"

placeholder="Search books..."

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
<th>Author</th>
<th>Department</th>
<th>Pages</th>
<th>Publish Date</th>
<th>PDF File</th>
<th width="160">Action</th>

</tr>

</thead>

<tbody>

<?php while($row = $book_result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['ID']; ?></td>

<td><?php echo $row['Title']; ?></td>

<td><?php echo $row['Category']; ?></td>

<td><?php echo $row['author_name']; ?></td>

<td><?php echo $row['department_name']; ?></td>

<td><?php echo $row['page']; ?></td>

<td><?php echo $row['Publish_Date']; ?></td>

<td>

<?php if($row['PDF_File'] != ""){ ?>

<a href="../PDF_File/<?php echo $row['PDF_File']; ?>"
target="_blank"
class="pdf-btn">

View PDF

</a>

<?php }else{ ?>

No File

<?php } ?>

</td>

<td>

<div class="action-icons">

<a href="books.php?edit=<?php echo $row['ID']; ?>"
class="edit-btn">

Edit

</a>

<a href="books.php?delete=<?php echo $row['ID']; ?>"
class="delete-btn"

onclick="return confirm('Delete this book?')">

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

<div class="form-section">

<div class="form-card">

<div class="form-title">

<?php
echo isset($_GET['edit'])
? "Edit Book"
: "Add Book";
?>

</div>

<form method="POST"
enctype="multipart/form-data">

<div class="mb-2">

<label class="form-label">ID</label>

<input type="text"

name="id"

class="form-control"

required

value="<?php echo $edit_id; ?>">

</div>

<div class="mb-2">

<label class="form-label">Title</label>

<input type="text"

name="title"

class="form-control"

required

value="<?php echo $edit_title; ?>">

</div>

<div class="mb-2">

<label class="form-label">Description</label>

<textarea name="description"
class="form-control"
required><?php echo $edit_description; ?></textarea>

</div>

<div class="mb-2">

<label class="form-label">Category</label>

<input type="text"

name="category"

class="form-control"

required

value="<?php echo $edit_category; ?>">

</div>

<div class="mb-2">

<label class="form-label">Author</label>

<select name="author"
class="custom-select"
required>

<option value="">Select Author</option>

<?php

$teacher = $conn->query("SELECT * FROM teacher");

while($t = $teacher->fetch_assoc()){

?>

<option value="<?php echo $t['ID']; ?>"

<?php
if($edit_author == $t['ID'])
echo "selected";
?>>

<?php echo $t['Name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-2">

<label class="form-label">Department</label>

<select name="department"
class="custom-select"
required>

<?php

$dep = $conn->query("SELECT * FROM department");

while($d = $dep->fetch_assoc()){

?>

<option value="<?php echo $d['ID']; ?>"

<?php
if($edit_department_id == $d['ID'])
echo "selected";
?>>

<?php echo $d['Name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-2">

<label class="form-label">Pages</label>

<input type="number"

name="pages"

class="form-control"

required

value="<?php echo $edit_pages; ?>">

</div>

<div class="mb-2">

<label class="form-label">PDF File</label>

<input type="file"

name="pdf_file"

class="form-control">

</div>

<div class="mb-3">

<label class="form-label">Publish Date</label>

<input type="date"

name="publish_date"

class="form-control"

required

value="<?php echo $edit_publish_date; ?>">

</div>

<button type="submit"

class="save-btn"

name="<?php
echo isset($_GET['edit'])
? 'update_book'
: 'save_book';
?>">

<?php
echo isset($_GET['edit'])
? 'Update Book'
: 'Save Book';
?>

</button>

</form>

</div>

</div>

</div>

</body>
</html>