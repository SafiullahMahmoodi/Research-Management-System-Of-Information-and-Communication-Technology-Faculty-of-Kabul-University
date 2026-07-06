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
// INSERT BOOK
// ===========================

if (isset($_POST['save_book'])) {

    $id            = mysqli_real_escape_string($conn, $_POST['id']);
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $author        = mysqli_real_escape_string($conn, $_POST['author']);
    $department_id = mysqli_real_escape_string($conn, $_POST['department']);
    $pages = isset($_POST['pages']) ? (int)$_POST['pages'] : 0;
    $publish_date  = mysqli_real_escape_string($conn, $_POST['publish_date']);

    // ===========================
    // CHECK AUTHOR
    // ===========================

    $check_teacher = $conn->query("
    SELECT ID
    FROM teacher
    WHERE ID='$author'
    ");

    if ($check_teacher->num_rows == 0) {

        die("Selected Author does not exist.");
    }

    // ===========================
    // PDF FILE
    // ===========================

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

    // ===========================
    // INSERT
    // ===========================

    $conn->query("
    INSERT INTO books
    (
        ID,
        title,
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

if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $pdf = $conn->query("
    SELECT PDF_File
    FROM books
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

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $res = $conn->query("
    SELECT *
    FROM books
    WHERE ID='$id'
    ");

    if ($res->num_rows > 0) {

        $row = $res->fetch_assoc();

        $edit_id            = $row['ID'];
        $edit_title         = $row['Title'];
        $edit_description   = $row['Description'];
        $edit_category      = $row['Category'];
        $edit_author        = $row['Author'];
        $edit_department_id = $row['Department'];
        $edit_pages         = $row['Pages'];
        $edit_publish_date  = $row['Publish_Date'];
    }
}

// ===========================
// UPDATE BOOK
// ===========================

if (isset($_POST['update_book'])) {

    $id            = mysqli_real_escape_string($conn, $_POST['id']);
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $author        = mysqli_real_escape_string($conn, $_POST['author']);
    $department_id = mysqli_real_escape_string($conn, $_POST['department']);
    $pages = isset($_POST['pages']) ? (int)$_POST['pages'] : 0;
    $publish_date  = mysqli_real_escape_string($conn, $_POST['publish_date']);

    $query = "
    UPDATE books SET

    title='$title',
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

        // DELETE OLD FILE

        $old = $conn->query("
        SELECT PDF_File
        FROM books
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

    header("Location: books.php");
    exit();
}

// ===========================
// SEARCH BOOKS
// ===========================

$search = "";

if (isset($_GET['search'])) {

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
    OR books.Publish_Date LIKE '%$search%'
    OR teacher.Name LIKE '%$search%'
    OR department.Name LIKE '%$search%'

    ORDER BY books.ID DESC
    ");
} else {

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
<html lang="<?= ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title><?= ($lang == 'fa') ? 'کتاب‌ها' : 'Books'; ?></title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>

    <Style>
        .form-buttons {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .form-buttons button {
            flex: 1 1 0;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 400;
        }

        .save-btn {
            background: #0f9d58;
            color: white;
        }

        .save-btn:hover {
            background: #0c7c45;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }

        html[dir="rtl"] .main-wrapper,
        html[dir="rtl"] .table-section,
        html[dir="rtl"] .form-section,
        html[dir="rtl"] .table-card,
        html[dir="rtl"] .form-card {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] textarea,
        html[dir="rtl"] .search-input {
            text-align: right;
        }

        html[dir="rtl"] .form-label,
        html[dir="rtl"] .form-title {
            text-align: right;
        }
    </Style>
</head>
<script>
    function checkPDF(input) {

        if (input.files.length > 0) {

            let file = input.files[0];

            let extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            if (extension !== "pdf") {

                alert("<?= ($lang == 'fa') ? 'فقط فایل PDF مجاز است.' : 'Only PDF files are allowed!'; ?>");

                input.value = "";
            }
        }
    }
</script>

<body>
    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <div class="table-section">

            <div class="search-wrapper"
                dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

                <form method="GET" class="search-form">

                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa')
                                            ? 'جستجوی کتاب‌ها...'
                                            : 'Search books...'; ?>"
                        value="<?= $search; ?>">

                    <button type="submit" class="search-btn">
                        <?= ($lang == 'fa') ? 'جستجو' : 'Search'; ?>
                    </button>

                </form>

            </div>

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th><?= ($lang == 'fa') ? 'آی دی' : 'ID'; ?></th>

                            <th><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></th>

                            <th><?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?></th>

                            <th><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></th>

                            <th><?= ($lang == 'fa') ? 'نویسنده' : 'Author'; ?></th>

                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تعداد صفحات' : 'Pages'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></th>

                            <th><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?></th>

                            <th><?= ($lang == 'fa') ? 'عملیات' : 'Action'; ?></th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $book_result->fetch_assoc()) { ?>

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

                                    <?php if ($row['PDF_File'] != "") { ?>

                                        <a href="../PDF_File/<?php echo $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">

                                            <?= ($lang == 'fa') ? 'مشاهده PDF' : 'View PDF'; ?>

                                        </a>

                                    <?php } else { ?>

                                        <?= ($lang == 'fa') ? 'بدون فایل' : 'No File'; ?>

                                    <?php } ?>

                                </td>

                                <td>

                                    <div class="action-icons">
                                        <a href="books.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            <?= ($lang == 'fa') ? 'تغییر دادن' : 'Edit'; ?>

                                        </a>

                                        <a href="books.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('<?= ($lang == 'fa')
                                                                            ? 'آیا از حذف این کتاب مطمئن هستید؟'
                                                                            : 'Delete this book?'; ?>')">

                                            <?= ($lang == 'fa') ? 'حذف' : 'Delete'; ?>

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

                <div class="form-title" style="text-align: center;">

                    <?php
                    echo isset($_GET['edit'])
                        ? (($lang == 'fa')
                            ? 'تفییر دادن کتاب'
                            : 'Edit Book')
                        : (($lang == 'fa')
                            ? 'اضافه نمودن  کتاب'
                            : 'Add Book');
                    ?>

                </div>

                <form method="POST"
                    enctype="multipart/form-data">

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'آی دی' : 'ID'; ?>
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
                            <?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?>
                        </label>

                        <input type="text"

                            name="category"

                            class="form-control"

                            required

                            value="<?php echo $edit_category; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'نویسنده' : 'Author'; ?>
                        </label>

                        <select name="author"
                            class="custom-select"
                            required>

                            <option value="">
                                <?= ($lang == 'fa')
                                    ? 'انتخاب نویسنده'
                                    : 'Select Author'; ?>
                            </option>

                            <?php

                            $teacher = $conn->query("SELECT * FROM teacher");

                            while ($t = $teacher->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $t['ID']; ?>"

                                    <?php
                                    if ($edit_author == $t['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $t['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?>
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
                                    <?php if ($edit_department_id == $d['ID']) echo "selected"; ?>>

                                    <?php echo $d['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? ' تعداد صفحات' : 'Pages'; ?>
                        </label>

                        <input type="number"

                            name="pages"

                            class="form-control"

                            required

                            value="<?php echo $edit_pages; ?>">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?>
                        </label>

                        <input
                            type="file"
                            name="pdf_file"
                            id="pdf_file"
                            class="form-control form-control-sm"
                            accept=".pdf"
                            onchange="checkPDF(this)">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'تاریخ انتشار' : 'Publish Date'; ?>
                        </label>

                        <input type="date"

                            name="publish_date"

                            class="form-control"

                            required

                            value="<?php echo $edit_publish_date; ?>">

                    </div>
                    <div class="form-buttons">
                        <button type="submit"
                            class="save-btn"
                            name="<?= isset($_GET['edit'])
                                        ? 'update_book'
                                        : 'save_book'; ?>">

                            <?= isset($_GET['edit'])
                                ? (($lang == 'fa')
                                    ? 'تغییر دادن کتاب'
                                    : 'Update Book')
                                : (($lang == 'fa')
                                    ? 'ذخیره کتاب'
                                    : 'Save Book'); ?>

                        </button>
                        <button
                            type="button"
                            class="cancel-btn"
                            onclick="window.location.href='books.php'">

                            <?php echo ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>

                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>

</body>

</html>