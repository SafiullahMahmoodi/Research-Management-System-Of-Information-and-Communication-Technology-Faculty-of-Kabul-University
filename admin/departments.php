<?php
// departments.php

include('../auth.php');




include('../db_connection.php');

// ===========================
// Insert Department
// ===========================

if (isset($_POST['save_department'])) {

    $id         = $_POST['id'];
    $department = $_POST['department'];

    $insert_query = "INSERT INTO department
    (ID, Name)

    VALUES

    ('$id','$department')";

    $conn->query($insert_query);

    header("Location: departments.php");
    exit();
}

// ===========================
// Delete Department
// ===========================

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    // Delete articles

    $conn->query("DELETE FROM articles
    WHERE Department='$delete_id'");

    // Delete books

    $conn->query("DELETE FROM books
    WHERE Department='$delete_id'");

    // Delete translated books

    $conn->query("DELETE FROM translated_books
    WHERE Department='$delete_id'");

    // Delete thesis

    $conn->query("DELETE FROM thesis
    WHERE Department='$delete_id'");

    // Delete students

    $conn->query("DELETE FROM students
    WHERE Department='$delete_id'");

    // Delete teachers

    $conn->query("DELETE FROM teacher
    WHERE Department='$delete_id'");

    // Delete department

    $conn->query("DELETE FROM department
    WHERE ID='$delete_id'");

    header("Location: departments.php");
    exit();
}
// ===========================
// Edit Department
// ===========================

$edit_id         = "";
$edit_department = "";

if (isset($_GET['edit'])) {

    $edit_id = $_GET['edit'];

    $edit_query = "SELECT * FROM department
    WHERE ID='$edit_id'";

    $edit_result = $conn->query($edit_query);

    if ($edit_result->num_rows > 0) {

        $edit_row = $edit_result->fetch_assoc();

        $edit_department = $edit_row['Name'];
    }
}

// ===========================
// Update Department
// ===========================

if (isset($_POST['update_department'])) {

    $id         = $_POST['id'];
    $department = $_POST['department'];

    $update_query = "UPDATE department SET

    Name='$department'

    WHERE ID='$id'";

    $conn->query($update_query);

    header("Location: departments.php");
    exit();
}

// ===========================
// Search
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = $_GET['search'];

    $department_query = "SELECT * FROM department

    WHERE

    ID LIKE '%$search%'
    OR Name LIKE '%$search%'";
} else {

    $department_query = "SELECT * FROM department";
}

$department_result = $conn->query($department_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Departments</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>


</head>

<body>
    <?php include('header.php'); ?>
    <div class="main-wrapper">

        <!-- TABLE SECTION -->

        <div class="table-section">

            <div class="search-wrapper">

                <form method="GET"
                    class="search-form">

                    <input type="text"

                        name="search"

                        class="search-input"

                        placeholder="Search departments..."

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

                            <th width="100">ID</th>

                            <th>Department Name</th>

                            <th width="180">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $department_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Name']; ?></td>

                                <td>

                                    <div class="action-icons">

                                        <a href="departments.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            Edit

                                        </a>

                                        <a href="departments.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('Are you sure to delete this department? It will delete all data that related to this department in other tables.')">

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

        <!-- FORM SECTION -->

        <div class="form-section">

            <div class="form-card">

                <div class="form-title">

                    <?php
                    echo isset($_GET['edit'])
                        ? "Edit Department"
                        : "Add Department";
                    ?>

                </div>

                <form method="POST">

                    <!-- ID -->

                    <div class="mb-3">

                        <label class="form-label">

                            ID

                        </label>

                        <input type="text"

                            name="id"

                            class="form-control"



                            required

                            value="<?php echo $edit_id; ?>"

                            <?php if (isset($_GET['edit'])) echo "readonly"; ?>>

                    </div>

                    <!-- Department Name -->

                    <div class="mb-4">

                        <label class="form-label">

                            Department Name

                        </label>

                        <input type="text"

                            name="department"

                            class="form-control"


                            required

                            value="<?php echo $edit_department; ?>">

                    </div>

                    <!-- Save Button -->

                    <button type="submit"

                        class="save-btn"

                        name="<?php
                                echo isset($_GET['edit'])
                                    ? 'update_department'
                                    : 'save_department';
                                ?>">

                        <?php
                        echo isset($_GET['edit'])
                            ? 'Update Department'
                            : 'Save Department';
                        ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>