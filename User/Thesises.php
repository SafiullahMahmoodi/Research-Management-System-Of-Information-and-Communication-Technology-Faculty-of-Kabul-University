<?php
include('../auth.php');
$lang = $_SESSION['lang'] ?? 'en';
include('../db_connection.php');

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

            die("Only PDF files are allowed.");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= ($lang == 'fa') ? 'پایان‌نامه‌ها' : 'Thesis'; ?>
    </title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/bootstrap.bundle.min.js"></script>
    <style>
        html[dir="rtl"] .form-label {
            display: block;
            width: 100%;
            text-align: right !important;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] .search-input {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .form-card {
            direction: rtl;
        }

        html[dir="rtl"] .mb-3,
        html[dir="rtl"] .mb-4 {
            text-align: right;
        }
    </Style>
</head>

<body dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- ================= SEARCH ================= -->
        <div class="table-section">

            <div class="search-wrapper">

                <form method="GET" class="search-form">

                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی پایان‌نامه...' : 'Search thesis...'; ?>"
                        value="<?= htmlspecialchars($search ?? '') ?>">

                    <button type="submit" class="search-btn">
                        <?= ($lang == 'fa') ? 'جستجو' : 'Search'; ?>
                    </button>

                </form>

            </div>

            <!-- ================= TABLE ================= -->
            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>
                            <th><?= ($lang == 'fa') ? 'آی‌دی' : 'ID'; ?></th>
                            <th><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></th>
                            <th><?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?></th>
                            <th><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></th>
                            <th><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></th>
                            <th><?= ($lang == 'fa') ? 'استاد' : 'Instructor'; ?></th>
                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>
                            <th><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></th>
                            <th><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF'; ?></th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $thesis_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?= $row['ID']; ?></td>
                                <td><?= $row['Title']; ?></td>
                                <td><?= $row['Description']; ?></td>
                                <td><?= $row['Category']; ?></td>
                                <td><?= $row['student_name']; ?></td>
                                <td><?= $row['instructor_name']; ?></td>
                                <td><?= $row['department_name']; ?></td>
                                <td><?= $row['Publish_Date']; ?></td>

                                <td>
                                    <?php if ($row['PDF_File'] != "") { ?>
                                        <a href="../PDF_File/<?= $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">
                                            PDF
                                        </a>
                                    <?php } else { ?>
                                        <?= ($lang == 'fa') ? 'ندارد' : 'No File'; ?>
                                    <?php } ?>
                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- ================= FORM ================= -->
        <div class="form-section">

            <div class="form-card">

                <div class="form-title">
                    <?= isset($_GET['edit'])
                        ? (($lang == 'fa') ? 'ویرایش پایان‌نامه' : 'Edit Thesis')
                        : (($lang == 'fa') ? 'افزودن پایان‌نامه' : 'Add Thesis');
                    ?>
                </div>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'آی‌دی' : 'ID'; ?></label>
                        <input type="text" name="id" class="form-control"
                            placeholder="<?= ($lang == 'fa') ? 'آی‌دی را وارد کنید' : 'Enter Thesis ID'; ?>" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?></label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></label>
                        <input type="text" name="category" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></label>

                        <select name="student_id" class="custom-select" required>
                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب محصل' : 'Select Student'; ?>
                            </option>

                            <?php
                            $student = $conn->query("SELECT * FROM students");
                            while ($s = $student->fetch_assoc()) {
                            ?>
                                <option value="<?= $s['ID']; ?>">
                                    <?= $s['Name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'استاد' : 'Instructor'; ?></label>

                        <select name="instructor" class="custom-select" required>
                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب استاد' : 'Select Instructor'; ?>
                            </option>

                            <?php
                            $teacher = $conn->query("SELECT * FROM teacher");
                            while ($t = $teacher->fetch_assoc()) {
                            ?>
                                <option value="<?= $t['ID']; ?>">
                                    <?= $t['Name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></label>

                        <select name="department" class="custom-select" required>
                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب دیپارتمنت' : 'Select Department'; ?>
                            </option>

                            <?php
                            $dep = $conn->query("SELECT * FROM department");
                            while ($d = $dep->fetch_assoc()) {
                            ?>
                                <option value="<?= $d['ID']; ?>">
                                    <?= $d['Name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?></label>
                        <input type="file" name="pdf_file" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></label>
                        <input type="date" name="publish_date" class="form-control" required>
                    </div>

                    <button type="submit" class="save-btn" name="save_thesis">
                        <?= ($lang == 'fa') ? 'ذخیره پایان‌نامه' : 'Save Thesis'; ?>
                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>