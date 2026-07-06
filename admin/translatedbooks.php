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
// INSERT TRANSLATED BOOK
// ===========================

if (isset($_POST['save_book'])) {

    $id             = mysqli_real_escape_string($conn, $_POST['id']);
    $title          = mysqli_real_escape_string($conn, $_POST['title']);
    $description    = mysqli_real_escape_string($conn, $_POST['description']);
    $author         = mysqli_real_escape_string($conn, $_POST['author']);
    $translated_by  = mysqli_real_escape_string($conn, $_POST['translated_by']);
    $category       = mysqli_real_escape_string($conn, $_POST['category']);
    $department     = mysqli_real_escape_string($conn, $_POST['department']);
    $pages          = (int)$_POST['pages'];
    $publish_date   = mysqli_real_escape_string($conn, $_POST['publish_date']);

    // CHECK TRANSLATOR

    $check_teacher = $conn->query("
    SELECT ID
    FROM teacher
    WHERE ID='$translated_by'
    ");

    if ($check_teacher->num_rows == 0) {

        die("Selected Translator does not exist.");
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
    INSERT INTO translated_books
    (
        ID,
        Title,
        Description,
        Author,
        translated_by,
        Category,
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
        '$author',
        '$translated_by',
        '$category',
        '$department',
        '$pages',
        '$pdf_file',
        '$publish_date'
    )
    ");

    header("Location: translatedbooks.php");
    exit();
}

// ===========================
// DELETE BOOK
// ===========================

if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $pdf = $conn->query("
    SELECT PDF_File
    FROM translated_books
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
    DELETE FROM translated_books
    WHERE ID='$id'
    ");

    header("Location: translatedbooks.php");
    exit();
}

// ===========================
// EDIT BOOK
// ===========================

$edit_id            = "";
$edit_title         = "";
$edit_description   = "";
$edit_author        = "";
$edit_translated_by = "";
$edit_category      = "";
$edit_department    = "";
$edit_pages         = "";
$edit_publish_date  = "";

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $res = $conn->query("
    SELECT *
    FROM translated_books
    WHERE ID='$id'
    ");

    if ($res->num_rows > 0) {

        $row = $res->fetch_assoc();

        $edit_id            = $row['ID'];
        $edit_title         = $row['Title'];
        $edit_description   = $row['Description'];
        $edit_author        = $row['Author'];
        $edit_translated_by = $row['Translated_by'];
        $edit_category      = $row['Category'];
        $edit_department    = $row['Department'];
        $edit_pages         = $row['Pages'];
        $edit_publish_date  = $row['Publish_Date'];
    }
}

// ===========================
// UPDATE BOOK
// ===========================

if (isset($_POST['update_book'])) {

    $id             = mysqli_real_escape_string($conn, $_POST['id']);
    $title          = mysqli_real_escape_string($conn, $_POST['title']);
    $description    = mysqli_real_escape_string($conn, $_POST['description']);
    $author         = mysqli_real_escape_string($conn, $_POST['author']);
    $translated_by  = mysqli_real_escape_string($conn, $_POST['translated_by']);
    $category       = mysqli_real_escape_string($conn, $_POST['category']);
    $department     = mysqli_real_escape_string($conn, $_POST['department']);
    $pages          = (int)$_POST['pages'];
    $publish_date   = mysqli_real_escape_string($conn, $_POST['publish_date']);

    $query = "
    UPDATE translated_books SET

    Title='$title',
    Description='$description',
    Author='$author',
    translated_by='$translated_by',
    Category='$category',
    Department='$department',
    Pages='$pages',
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

            echo "<script>
            alert('<?= ($lang == 'fa')
                        ? 'فقط فایل PDF مجاز است!'
                        : 'Only PDF files are allowed!'; ?>');
            window.history.back();
          </script>";
            exit();
        }

        if ($file_size > 209715200) {

            die("File size must be less than 200MB.");
        }

        // DELETE OLD PDF

        $old = $conn->query("
        SELECT PDF_File
        FROM translated_books
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

    header("Location: translatedbooks.php");
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
    $book_result = $conn->query("

SELECT translated_books.*,
teacher.Name AS translator_name,
department.Name AS department_name

FROM translated_books

LEFT JOIN teacher
ON translated_books.translated_by = teacher.ID

LEFT JOIN department
ON translated_books.Department = department.ID

WHERE

translated_books.ID LIKE '%$search%'
OR translated_books.Title LIKE '%$search%'
OR translated_books.Author LIKE '%$search%'
OR translated_books.Category LIKE '%$search%'
OR teacher.Name LIKE '%$search%'
OR department.Name LIKE '%$search%'
OR translated_books.Publish_Date LIKE '%$search%'

ORDER BY translated_books.ID DESC
");
} else {

    $book_result = $conn->query("

    SELECT translated_books.*,
    teacher.Name AS translator_name,
    department.Name AS department_name

    FROM translated_books

    LEFT JOIN teacher
    ON translated_books.translated_by = teacher.ID

    LEFT JOIN department
    ON translated_books.Department = department.ID

    ORDER BY translated_books.ID DESC
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
            ? 'کتاب‌های ترجمه‌شده'
            : 'Translated Books'; ?>
    </title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>

    <Style>
        /* ==========================
   MODERN SEARCH BOX
========================== */

        .search-wrapper {
            display: flex;
            justify-content: center;
            margin: 13px 0;
        }

        .search-form {
            width: 100%;
            max-width: 450px;
            display: flex;
            align-items: center;
            background: #fff;
            border: 2px solid #3d3d3d;
            border-radius: 50px;
            overflow: hidden;
            transition: .3s;
        }

        .search-form:focus-within {
            border-color: #0f9d58;
            box-shadow: 0 0 15px rgba(15, 157, 88, .18);
        }

        /* Input */

        .search-input {
            flex: 1;

            border: none;
            outline: none;

            background: transparent;

            padding: 10px 15px;

            font-size: 13px;

            color: #222;
        }

        .search-input::placeholder {
            color: #777;
        }

        /* Button */

        .search-btn {

            width: 60px;
            height: 60px;

            border: none;
            background: transparent;

            cursor: pointer;

            font-size: 26px;

            display: flex;
            justify-content: center;
            align-items: center;

            transition: .25s;
        }

        .search-btn:hover {

            color: #0f9d58;
        }

        /* English */

        html[dir="ltr"] .search-form {
            flex-direction: row;
        }

        html[dir="ltr"] .search-input {
            text-align: left;
        }

        html[dir="ltr"] .search-btn {
            border-left: 1px solid #ddd;
        }

        /* Persian */

        html[dir="rtl"] .search-form {
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .search-input {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .search-btn {
            border-right: 1px solid #ddd;
        }

        html[dir="rtl"] {
            direction: rtl;
        }

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

            let extension =
                file.name.split('.').pop().toLowerCase();

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

                <form method="GET" class="search-form">

                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی کتاب های ترجمه شده...' : 'Search...'; ?>"
                        value="<?= htmlspecialchars($search); ?>">

                    <button type="submit" class="search-btn">
                        🔍
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
                            <th><?= ($lang == 'fa') ? 'نویسنده' : 'Author'; ?></th>
                            <th><?= ($lang == 'fa') ? 'مترجم' : 'Translator'; ?></th>
                            <th><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></th>
                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>
                            <th><?= ($lang == 'fa') ? 'تعداد صفحات' : 'Pages'; ?></th>
                            <th><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></th>
                            <th><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF'; ?></th>
                            <th><?= ($lang == 'fa') ? 'عملیات' : 'Action'; ?></th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $book_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>
                                <td><?php echo $row['Title']; ?></td>
                                <td><?php echo $row['Description']; ?></td>
                                <td><?php echo $row['Author']; ?></td>
                                <td><?php echo $row['translator_name']; ?></td>
                                <td><?php echo $row['Category']; ?></td>
                                <td><?php echo $row['department_name']; ?></td>
                                <td><?php echo $row['Pages']; ?></td>
                                <td><?php echo $row['Publish_Date']; ?></td>

                                <td>

                                    <?php if ($row['PDF_File'] != "") { ?>

                                        <a href="../PDF_File/<?php echo $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">
                                            <?= ($lang == 'fa') ? 'PDF' : 'PDF'; ?>

                                        </a>

                                    <?php } else { ?>

                                        <?= ($lang == 'fa') ? 'بدون فایل' : 'No File'; ?>

                                    <?php } ?>

                                </td>

                                <td>

                                    <div class="action-icons">

                                        <a href="translatedbooks.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            <?= ($lang == 'fa') ? 'تغییر دادن' : 'Edit'; ?>

                                        </a>

                                        <a href="translatedbooks.php?delete=<?php echo $row['ID']; ?>"
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

        <div class="form-section">

            <div class="form-card">
                <div class="form-title" style="text-align: center;">

                    <?php
                    echo isset($_GET['edit'])
                        ? (($lang == 'fa')
                            ? 'تغییر دادن کتاب ترجمه‌شده'
                            : 'Edit Translated Book')
                        : (($lang == 'fa')
                            ? 'اضافه نمودن کتاب ترجمه‌شده'
                            : 'Add Translated Book');
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
                            <?= ($lang == 'fa') ? 'نویسنده' : 'Author'; ?>
                        </label>

                        <input type="text"
                            name="author"
                            class="form-control"

                            required
                            value="<?php echo $edit_author; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'ترجمه شده توسط' : 'Translated By'; ?>
                        </label>

                        <select name="translated_by"
                            class="custom-select"
                            required>

                            <option value="">
                                <?= ($lang == 'fa')
                                    ? 'انتخاب مترجم'
                                    : 'Select Translator'; ?>
                            </option>

                            <?php

                            $teacher = $conn->query("SELECT * FROM teacher");

                            while ($t = $teacher->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $t['ID']; ?>"

                                    <?php
                                    if ($edit_translated_by == $t['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $t['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

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
                            <?= ($lang == 'fa') ? 'تعداد صفحات' : 'Pages'; ?>
                        </label>

                        <input type="number"
                            name="pages"
                            class="form-control"
                            required
                            value="<?php echo $edit_pages; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?>
                        </label>

                        <input type="file"
                            name="pdf_file"
                            class="form-control"
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
                            onclick="window.location.href='translatedbooks.php'">

                            <?php echo ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>

                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>