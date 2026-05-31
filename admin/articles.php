<?php

include('../auth.php');




include('../db_connection.php');

// ===========================
// CREATE PDF FOLDER
// ===========================

if (!file_exists("../PDF_File")) {

    mkdir("../PDF_File", 0777, true);
}

// ===========================
// INSERT ARTICLE
// ===========================

if (isset($_POST['save_article'])) {

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $teacher_id  = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $student_id  = mysqli_real_escape_string($conn, $_POST['student_id']);
    $department  = mysqli_real_escape_string($conn, $_POST['department']);
    $date        = mysqli_real_escape_string($conn, $_POST['date']);

    // ===========================
    // CHECK TEACHER OR STUDENT
    // ===========================

    if (empty($teacher_id) && empty($student_id)) {

        die("Please select Teacher or Student.");
    }

    // ===========================
    // CHECK TEACHER
    // ===========================

    if (!empty($teacher_id)) {

        $check_teacher = $conn->query("
        SELECT ID
        FROM teacher
        WHERE ID='$teacher_id'
        ");

        if ($check_teacher->num_rows == 0) {

            die("Selected Teacher does not exist.");
        }
    }

    // ===========================
    // CHECK STUDENT
    // ===========================

    if (!empty($student_id)) {

        $check_student = $conn->query("
        SELECT ID
        FROM students
        WHERE ID='$student_id'
        ");

        if ($check_student->num_rows == 0) {

            die("Selected Student does not exist.");
        }
    }

    // ===========================
    // NULL VALUES
    // ===========================

    $teacher_value = !empty($teacher_id)
        ? "'$teacher_id'"
        : "NULL";

    $student_value = !empty($student_id)
        ? "'$student_id'"
        : "NULL";

    $pdf_file = "";

    // ===========================
    // UPLOAD PDF
    // ===========================

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != "") {

        $file_name = $_FILES['pdf_file']['name'];
        $file_tmp  = $_FILES['pdf_file']['tmp_name'];
        $file_size = $_FILES['pdf_file']['size'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if ($extension != "pdf") {

            die("Only PDF files are allowed.");
        }

        // MAX 200MB

        if ($file_size > 209715200) {

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
    INSERT INTO articles
    (
        ID,
        Title,
        Description,
        Category,
        Teacher_ID,
        Student_ID,
        Department,
        PDF_File,
        Date
    )

    VALUES
    (
        '$id',
        '$title',
        '$description',
        '$category',
        $teacher_value,
        $student_value,
        '$department',
        '$pdf_file',
        '$date'
    )
    ");

    header("Location: articles.php");
    exit();
}

// ===========================
// DELETE ARTICLE
// ===========================

if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $pdf = $conn->query("
    SELECT PDF_File
    FROM articles
    WHERE ID='$id'
    ");

    if ($pdf->num_rows > 0) {

        $p = $pdf->fetch_assoc();

        if (
            $p['PDF_File'] != ""
            &&
            file_exists("../PDF_File/" . $p['PDF_File'])
        ) {

            unlink("../PDF_File/" . $p['PDF_File']);
        }
    }

    $conn->query("
    DELETE FROM articles
    WHERE ID='$id'
    ");

    header("Location: articles.php");
    exit();
}

// ===========================
// EDIT ARTICLE
// ===========================

$edit_id          = "";
$edit_title       = "";
$edit_description = "";
$edit_category    = "";
$edit_teacher     = "";
$edit_student     = "";
$edit_department  = "";
$edit_date        = "";

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $res = $conn->query("
    SELECT *
    FROM articles
    WHERE ID='$id'
    ");

    if ($res->num_rows > 0) {

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
// UPDATE ARTICLE
// ===========================

if (isset($_POST['update_article'])) {

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $teacher_id  = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $student_id  = mysqli_real_escape_string($conn, $_POST['student_id']);
    $department  = mysqli_real_escape_string($conn, $_POST['department']);
    $date        = mysqli_real_escape_string($conn, $_POST['date']);

    if (empty($teacher_id) && empty($student_id)) {

        die("Please select Teacher or Student.");
    }

    $teacher_value = !empty($teacher_id)
        ? "'$teacher_id'"
        : "NULL";

    $student_value = !empty($student_id)
        ? "'$student_id'"
        : "NULL";

    $query = "
    UPDATE articles SET

    Title='$title',
    Description='$description',
    Category='$category',
    Teacher_ID=$teacher_value,
    Student_ID=$student_value,
    Department='$department',
    Date='$date'
    ";

    // ===========================
    // UPDATE PDF
    // ===========================

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != "") {

        $file_name = $_FILES['pdf_file']['name'];
        $file_tmp  = $_FILES['pdf_file']['tmp_name'];
        $file_size = $_FILES['pdf_file']['size'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if ($extension != "pdf") {

            die("Only PDF files are allowed.");
        }

        if ($file_size > 209715200) {

            die("File size must be less than 200MB.");
        }

        // DELETE OLD PDF

        $old = $conn->query("
        SELECT PDF_File
        FROM articles
        WHERE ID='$id'
        ");

        if ($old->num_rows > 0) {

            $o = $old->fetch_assoc();

            if (
                $o['PDF_File'] != ""
                &&
                file_exists("../PDF_File/" . $o['PDF_File'])
            ) {

                unlink("../PDF_File/" . $o['PDF_File']);
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

    header("Location: articles.php");
    exit();
}

// ===========================
// SEARCH ARTICLES
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

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

    ORDER BY articles.ID DESC
    ");
} else {

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

    ORDER BY articles.ID DESC
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>


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
                            <th>Description</th>
                            <th>Category</th>
                            <th>Teacher</th>
                            <th>Student</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>PDF File</th>
                            <th width="160">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $article_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Title']; ?></td>
                                <td><?php echo $row['Description']; ?></td>

                                <td><?php echo $row['Category']; ?></td>

                                <td><?php echo $row['teacher_name']; ?></td>

                                <td><?php echo $row['student_name']; ?></td>

                                <td><?php echo $row['department_name']; ?></td>

                                <td><?php echo $row['Date']; ?></td>

                                <td>

                                    <?php if ($row['PDF_File'] != "") { ?>

                                        <a href="../PDF_File/<?php echo $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">

                                            View PDF

                                        </a>

                                    <?php } else { ?>

                                        No File

                                    <?php } ?>

                                </td>

                                <td>

                                    <div class="action-icons">

                                        <a href="articles.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            Edit

                                        </a>

                                        <a href="articles.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('Delete this article?')">

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

                    <div class="mb-2">

                        <label class="form-label">ID</label>

                        <input type="text"

                            name="id"

                            class="form-control"
                            placeholder="Enter ID"

                            required

                            value="<?php echo $edit_id; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">Title</label>

                        <input type="text"

                            name="title"

                            class="form-control"
                            placeholder="Enter Title"

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
                            placeholder="Enter Category"

                            required

                            value="<?php echo $edit_category; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">Teacher</label>

                        <select name="teacher_id"
                            class="custom-select">
                            <option value="">

                            </option>

                            <?php

                            $teacher = $conn->query("SELECT * FROM teacher");

                            while ($t = $teacher->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $t['ID']; ?>"

                                    <?php
                                    if ($edit_teacher == $t['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $t['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">Student</label>

                        <select name="student_id"
                            class="custom-select">
                            <option value="">

                            </option>

                            <?php

                            $student = $conn->query("SELECT * FROM students");

                            while ($s = $student->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $s['ID']; ?>"

                                    <?php
                                    if ($edit_student == $s['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $s['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">Department</label>

                        <select name="department"
                            class="custom-select">

                            <?php

                            $dep = $conn->query("SELECT * FROM department");

                            while ($d = $dep->fetch_assoc()) {

                            ?>
                                <option value="">
                                    Select Department
                                </option>

                                <option value="<?php echo $d['ID']; ?>"

                                    <?php
                                    if ($edit_department == $d['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $d['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">PDF File</label>

                        <input type="file"

                            name="pdf_file"

                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Date</label>

                        <input type="date"

                            name="date"

                            class="form-control"

                            required

                            value="<?php echo $edit_date; ?>">

                    </div>

                    <button type="submit"

                        class="save-btn"

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