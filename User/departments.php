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


                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $department_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Name']; ?></td>


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

                    Add Department

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

                            placeholder="Enter department ID"

                            required>

                    </div>

                    <!-- Department Name -->

                    <div class="mb-4">

                        <label class="form-label">

                            Department Name

                        </label>

                        <input type="text"

                            name="department"

                            class="form-control"

                            placeholder="Enter department name"

                            required>

                    </div>

                    <!-- Save Button -->

                    <button type="submit"

                        class="save-btn"

                        name="save_department">

                        Save Department

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>