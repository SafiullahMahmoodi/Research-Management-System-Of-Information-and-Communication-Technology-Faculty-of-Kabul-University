<?php

include('../auth.php');

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
    $pages         = isset($_POST['pages']) ? (int)$_POST['pages'] : 0;
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
        Pages,
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

<link rel="stylesheet" href="style.css">

<link rel="stylesheet"
href="../css/bootstrap.min.css">

<script src="../js/bootstrap.bundle.min.js"></script>

</head>

<body>

<?php include('header.php'); ?>

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
<th>Description</th>
<th>Category</th>
<th>Author</th>
<th>Department</th>
<th>Pages</th>
<th>Publish Date</th>
<th>PDF File</th>


</tr>

</thead>

<tbody>

<?php while($row = $book_result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['ID']; ?></td>

<td><?php echo $row['Title']; ?></td>

<td><?php echo $row['Description']; ?></td>

<td><?php echo $row['Category']; ?></td>

<td><?php echo $row['author_name']; ?></td>

<td><?php echo $row['department_name']; ?></td>

<td><?php echo $row['Pages']; ?></td>

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
Add Book
</div>

<form method="POST"
enctype="multipart/form-data">

<div class="mb-2">

<label class="form-label">ID</label>

<input type="text"
name="id"
class="form-control"
required
placeholder="Enter Book ID">

</div>

<div class="mb-2">

<label class="form-label">Title</label>

<input type="text"
name="title"
class="form-control"
required
placeholder="Enter Book Title">

</div>

<div class="mb-2">

<label class="form-label">Description</label>

<textarea name="description"
class="form-control"
required
placeholder="Enter Description"></textarea>

</div>

<div class="mb-2">

<label class="form-label">Category</label>

<input type="text"
name="category"
class="form-control"
required
placeholder="Enter Category">

</div>

<div class="mb-2">

<label class="form-label">Author</label>

<select name="author"
class="custom-select"
required>

<option value="">
Select Author
</option>

<?php

$teacher = $conn->query("SELECT * FROM teacher");

while($t = $teacher->fetch_assoc()){

?>

<option value="<?php echo $t['ID']; ?>">

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

<option value="">
Select Department
</option>

<?php

$dep = $conn->query("SELECT * FROM department");

while($d = $dep->fetch_assoc()){

?>

<option value="<?php echo $d['ID']; ?>">

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
required>

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
required>

</div>

<button type="submit"
class="save-btn"
name="save_book">

Save Book

</button>

</form>

</div>

</div>

</div>

</body>
</html>