<?php
include('../auth.php');



include('../db_connection.php');
$lang = $_SESSION['lang'] ?? 'en';
// ===========================
// CREATE PDF FOLDER
// ===========================

if (!file_exists("../PDF_File")) {

    mkdir("../PDF_File", 0777, true);
}

// ===========================
// INSERT THESIS
// ===========================

if (isset($_POST['save_thesis'])) {

    $id            = mysqli_real_escape_string($conn, $_POST['id']);
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $student_id    = mysqli_real_escape_string($conn, $_POST['student_id']);
    $instructor    = mysqli_real_escape_string($conn, $_POST['instructor']);
    $department    = mysqli_real_escape_string($conn, $_POST['department']);
    $publish_date  = mysqli_real_escape_string($conn, $_POST['publish_date']);

    // CHECK STUDENT

    $check_student = $conn->query("
    SELECT ID
    FROM students
    WHERE ID='$student_id'
    ");

    if ($check_student->num_rows == 0) {

        die("Selected Student does not exist.");
    }

    // CHECK INSTRUCTOR

    $check_teacher = $conn->query("
    SELECT ID
    FROM teacher
    WHERE ID='$instructor'
    ");

    if ($check_teacher->num_rows == 0) {

        die("Selected Instructor does not exist.");
    }

    // PDF FILE

    $pdf_file = "";

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != "") {

        $file_name = $_FILES['pdf_file']['name'];
        $file_tmp  = $_FILES['pdf_file']['tmp_name'];
        $file_size = $_FILES['pdf_file']['size'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if ($extension != "pdf") {

            echo "<script>
            alert('Only PDF files are allowed!');
            window.history.back();
          </script>";
            exit();
        }
        if ($file_size > 209715200) {

            die("File size must be less than 200MB.");
        }

        $pdf_file = time() . "_" . $file_name;

        move_uploaded_file(
            $file_tmp,
            "../PDF_File/" . $pdf_file
        );
    }

    // INSERT

    $conn->query("
    INSERT INTO thesis
    (
        ID,
        Title,
        Description,
        Category,
        Student_ID,
        Instructor,
        Department,
        PDF_File,
        Publish_Date
    )

    VALUES
    (
        '$id',
        '$title',
        '$description',
        '$category',
        '$student_id',
        '$instructor',
        '$department',
        '$pdf_file',
        '$publish_date'
    )
    ");

    header("Location: thesis.php");
    exit();
}

// ===========================
// DELETE THESIS
// ===========================

if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $pdf = $conn->query("
    SELECT PDF_File
    FROM thesis
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
    DELETE FROM thesis
    WHERE ID='$id'
    ");

    header("Location: thesis.php");
    exit();
}

// ===========================
// EDIT THESIS
// ===========================

$edit_id           = "";
$edit_title        = "";
$edit_description  = "";
$edit_category     = "";
$edit_student      = "";
$edit_instructor   = "";
$edit_department   = "";
$edit_publish_date = "";

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $res = $conn->query("
    SELECT *
    FROM thesis
    WHERE ID='$id'
    ");

    if ($res->num_rows > 0) {

        $row = $res->fetch_assoc();

        $edit_id           = $row['ID'];
        $edit_title        = $row['Title'];
        $edit_description  = $row['Description'];
        $edit_category     = $row['Category'];
        $edit_student      = $row['Student_ID'];
        $edit_instructor   = $row['Instructor'];
        $edit_department   = $row['Department'];
        $edit_publish_date = $row['Publish_Date'];
    }
}

// ===========================
// UPDATE THESIS
// ===========================

if (isset($_POST['update_thesis'])) {

    $id            = mysqli_real_escape_string($conn, $_POST['id']);
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $student_id    = mysqli_real_escape_string($conn, $_POST['student_id']);
    $instructor    = mysqli_real_escape_string($conn, $_POST['instructor']);
    $department    = mysqli_real_escape_string($conn, $_POST['department']);
    $publish_date  = mysqli_real_escape_string($conn, $_POST['publish_date']);

    $query = "
    UPDATE thesis SET

    Title='$title',
    Description='$description',
    Category='$category',
    Student_ID='$student_id',
    Instructor='$instructor',
    Department='$department',
    Publish_Date='$publish_date'
    ";

    // UPDATE PDF

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
        FROM thesis
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

    header("Location: thesis.php");
    exit();
}

// ===========================
// SEARCH
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

    $thesis_result = $conn->query("

    SELECT thesis.*,
    students.Name AS student_name,
    teacher.Name AS instructor_name,
    department.Name AS department_name

    FROM thesis

    LEFT JOIN students
    ON thesis.Student_ID = students.ID

    LEFT JOIN teacher
    ON thesis.Instructor = teacher.ID

    LEFT JOIN department
    ON thesis.Department = department.ID

 WHERE

thesis.ID LIKE '%$search%'
OR thesis.Title LIKE '%$search%'
OR thesis.Description LIKE '%$search%'
OR thesis.Category LIKE '%$search%'
OR students.Name LIKE '%$search%'
OR teacher.Name LIKE '%$search%'
OR department.Name LIKE '%$search%'
OR thesis.Publish_Date LIKE '%$search%'

    ORDER BY thesis.ID DESC
    ");
} else {

    $thesis_result = $conn->query("

    SELECT thesis.*,
    students.Name AS student_name,
    teacher.Name AS instructor_name,
    department.Name AS department_name

    FROM thesis

    LEFT JOIN students
    ON thesis.Student_ID = students.ID

    LEFT JOIN teacher
    ON thesis.Instructor = teacher.ID

    LEFT JOIN department
    ON thesis.Department = department.ID

    ORDER BY thesis.ID DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="<?= ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>
        <?= ($lang == 'fa')
            ? 'پایان‌نامه‌ها'
            : 'Thesis'; ?>
    </title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>

    <Style>
        html[dir="rtl"] {
            direction: rtl;
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
<script>
    function checkPDF(input) {

        if (input.files.length > 0) {

            let file = input.files[0];

            let extension = file.name.split('.').pop().toLowerCase();

            if (extension !== "pdf") {

                alert("<?= ($lang == 'fa')
                            ? 'فقط فایل PDF مجاز است!'
                            : 'Only PDF files are allowed!'; ?>");

                input.value = "";
            }
        }
    }
</script>

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
                        placeholder="<?= ($lang == 'fa')
                                            ? 'جستجوی پایان‌نامه‌ها...'
                                            : 'Search thesis...'; ?>"
                        value="<?php echo $search; ?>">
                    <button type="submit" class="search-btn">
                        <?= ($lang == 'fa') ? 'جستجو' : 'Search'; ?>
                    </button>

                </form>

            </div>

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th><?= ($lang == 'fa') ? 'شناسه' : 'ID'; ?></th>
                            <th><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></th>
                            <th><?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?></th>
                            <th><?= ($lang == 'fa') ? 'دسته‌بندی' : 'Category'; ?></th>
                            <th><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></th>
                            <th><?= ($lang == 'fa') ? 'استاد راهنما' : 'Instructor'; ?></th>
                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>
                            <th><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></th>
                            <th><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF'; ?></th>
                            <th><?= ($lang == 'fa') ? 'عملیات' : 'Action'; ?></th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $thesis_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Title']; ?></td>

                                <td><?php echo $row['Description']; ?></td>

                                <td><?php echo $row['Category']; ?></td>

                                <td><?php echo $row['student_name']; ?></td>

                                <td><?php echo $row['instructor_name']; ?></td>

                                <td><?php echo $row['department_name']; ?></td>

                                <td><?php echo $row['Publish_Date']; ?></td>

                                <td>

                                    <?php if ($row['PDF_File'] != "") { ?>

                                        <a href="../PDF_File/<?php echo $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">
                                            <?= ($lang == 'fa') ? 'PDF' : 'PDF'; ?>

                                        </a>

                                    <?php } else { ?>

                                        <?= ($lang == 'fa')
                                            ? 'بدون فایل'
                                            : 'No File'; ?>

                                    <?php } ?>

                                </td>

                                <td>

                                    <div class="action-icons">

                                        <a href="thesis.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">
                                            <?= ($lang == 'fa')
                                                ? 'ویرایش'
                                                : 'Edit'; ?>
                                        </a>

                                        <a href="thesis.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"
                                            onclick="return confirm('<?= ($lang == 'fa')
                                                                            ? 'آیا از حذف این پایان‌نامه مطمئن هستید؟'
                                                                            : 'Delete this thesis?'; ?>')">
                                            <?= ($lang == 'fa')
                                                ? 'حذف'
                                                : 'Delete'; ?>

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
                        ? (($lang == 'fa')
                            ? 'ویرایش پایان‌نامه'
                            : 'Edit Thesis')
                        : (($lang == 'fa')
                            ? 'افزودن پایان‌نامه'
                            : 'Add Thesis');
                    ?>

                </div>

                <form method="POST"
                    enctype="multipart/form-data">

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'شناسه' : 'ID'; ?>
                        </label>

                        <input type="text"
                            name="id"
                            class="form-control"

                            required
                            value="<?php echo $edit_id; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?>
                        </label>

                        <input type="text"
                            name="title"
                            class="form-control"

                            required
                            value="<?php echo $edit_title; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?>
                        </label>

                        <textarea name="description"
                            class="form-control"
                            required><?php echo $edit_description; ?></textarea>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'دسته‌بندی' : 'Category'; ?>
                        </label>

                        <input type="text"
                            name="category"
                            class="form-control"

                            required
                            value="<?php echo $edit_category; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'محصل' : 'Student'; ?>
                        </label>

                        <select name="student_id"
                            class="custom-select"
                            required>

                            <option value="">
                                <?= ($lang == 'fa')
                                    ? 'انتخاب محصل'
                                    : 'Select Student'; ?>
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

                        <label class="form-label">
                            <?= ($lang == 'fa')
                                ? 'استاد راهنما'
                                : 'Instructor'; ?>
                        </label>

                        <select name="instructor"
                            class="custom-select"
                            required>

                            <option value="">
                                <?= ($lang == 'fa')
                                    ? 'انتخاب استاد راهنما'
                                    : 'Select Instructor'; ?>
                            </option>

                            <?php

                            $teacher = $conn->query("SELECT * FROM teacher");

                            while ($t = $teacher->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $t['ID']; ?>"

                                    <?php
                                    if ($edit_instructor == $t['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $t['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa')
                                ? 'دیپارتمنت'
                                : 'Department'; ?>
                        </label>

                        <select name="department"
                            class="custom-select"
                            required>

                            <option value="">
                                <?= ($lang == 'fa')
                                    ? 'انتخاب دیپارتمنت'
                                    : 'Select Department'; ?>
                            </option>

                            <?php

                            $dep = $conn->query("SELECT * FROM department");

                            while ($d = $dep->fetch_assoc()) {

                            ?>

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
                        <label class="form-label">
                            <?= ($lang == 'fa')
                                ? 'فایل PDF'
                                : 'PDF File'; ?>
                        </label>
                        <input type="file"
                            name="pdf_file"
                            class="form-control"
                            accept=".pdf"
                            onchange="checkPDF(this)">
                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa')
                                ? 'تاریخ نشر'
                                : 'Publish Date'; ?>
                        </label>

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
                                    ? 'update_thesis'
                                    : 'save_thesis';
                                ?>">

                        <?php
                        echo isset($_GET['edit'])
                            ? (($lang == 'fa')
                                ? 'بروزرسانی پایان‌نامه'
                                : 'Update Thesis')
                            : (($lang == 'fa')
                                ? 'ذخیره پایان‌نامه'
                                : 'Save Thesis');
                        ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>