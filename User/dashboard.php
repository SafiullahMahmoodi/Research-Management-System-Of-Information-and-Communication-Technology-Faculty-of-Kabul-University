<?php
// dashboard.php

include('../auth.php');



include('../db_connection.php');

// ===============================
// Articles Graph
// ===============================

$article_labels = [];
$article_data   = [];
$total_articles = 0;

$article_query = "SELECT category, COUNT(*) as total
                  FROM articles
                  GROUP BY category";

$article_result = $conn->query($article_query);

while($row = $article_result->fetch_assoc()){

    $article_labels[] = $row['category'];
    $article_data[]   = $row['total'];

    $total_articles += $row['total'];
}

// ===============================
// Books Graph
// ===============================

$book_labels = [];
$book_data   = [];
$total_books = 0;

$book_query = "SELECT category, COUNT(*) as total
               FROM books
               GROUP BY category";

$book_result = $conn->query($book_query);

while($row = $book_result->fetch_assoc()){

    $book_labels[] = $row['category'];
    $book_data[]   = $row['total'];

    $total_books += $row['total'];
}

// ===============================
// Translate Books Graph
// ===============================

$translate_labels = [];
$translate_data   = [];
$total_translate  = 0;

$translate_query = "SELECT category, COUNT(*) as total
                    FROM translated_books
                    GROUP BY category";

$translate_result = $conn->query($translate_query);

while($row = $translate_result->fetch_assoc()){

    $translate_labels[] = $row['category'];
    $translate_data[]   = $row['total'];

    $total_translate += $row['total'];
}

// ===============================
// Thesis Graph
// ===============================

$thesis_labels = [];
$thesis_data   = [];
$total_thesis  = 0;

$thesis_query = "SELECT category, COUNT(*) as total
                 FROM thesis
                 GROUP BY category";

$thesis_result = $conn->query($thesis_query);

while($row = $thesis_result->fetch_assoc()){

    $thesis_labels[] = $row['category'];
    $thesis_data[]   = $row['total'];

    $total_thesis += $row['total'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>
<link rel="stylesheet" href="style.css">
    <!-- Bootstrap -->

    <link rel="stylesheet"
    href="../css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">

    <!-- Chart JS -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>

<body>

<!-- Header -->

<header class="main-header">

    <div class="header-title">

        Research Management System of Information and Communication Technology Faculty

    </div>

    <a href="../logout.php" class="logout-btn">

        Logout

    </a>

</header>

<!-- Main Layout -->

<div class="main-container">

    <!-- Sidebar -->

    <aside class="sidebar">

        <ul class="sidebar-menu">

            <li>
                <a href="dashboard.php" class="active">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>


            <li>
                <a href="departments.php">
                    <i class="bi bi-building"></i>
                    Departments
                </a>
            </li>

            <li>
                <a href="teachers.php">
                    <i class="bi bi-person-workspace"></i>
                    Teachers
                </a>
            </li>

            <li>
                <a href="students.php">
                    <i class="bi bi-mortarboard-fill"></i>
                    Students
                </a>
            </li>

            <li>
                <a href="articles.php">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    Articles
                </a>
            </li>

            <li>
                <a href="thesises.php">
                    <i class="bi bi-journal-richtext"></i>
                    Thesises
                </a>
            </li>

            <li>
                <a href="books.php">
                    <i class="bi bi-book-fill"></i>
                    Books
                </a>
            </li>

            <li>
                <a href="translatedbooks.php">
                    <i class="bi bi-translate"></i>
                    Translated Books
                </a>
            </li>

        </ul>

    </aside>

    <!-- Content -->

    <main class="content">

        <h2 class="page-title">

            Dashboard

        </h2>

        <div class="graph-grid">

            <!-- Articles -->

            <div class="graph-card">

                <div class="graph-top">

                    <div class="graph-title">

                        Articles By Category

                    </div>

                    <div class="graph-count">

                        Total: <?php echo $total_articles; ?>

                    </div>

                </div>

                <canvas id="articlesChart"></canvas>

            </div>

            <!-- Books -->

            <div class="graph-card">

                <div class="graph-top">

                    <div class="graph-title">

                        Books By Category

                    </div>

                    <div class="graph-count">

                        Total: <?php echo $total_books; ?>

                    </div>

                </div>

                <canvas id="booksChart"></canvas>

            </div>

            <!-- Translate Books -->

            <div class="graph-card">

                <div class="graph-top">

                    <div class="graph-title">

                        Translated Books

                    </div>

                    <div class="graph-count">

                        Total: <?php echo $total_translate; ?>

                    </div>

                </div>

                <canvas id="translateChart"></canvas>

            </div>

            <!-- Thesis -->

            <div class="graph-card">

                <div class="graph-top">

                    <div class="graph-title">

                        Thesis By Category

                    </div>

                    <div class="graph-count">

                        Total: <?php echo $total_thesis; ?>

                    </div>

                </div>

                <canvas id="thesisChart"></canvas>

            </div>

        </div>

    </main>

</div>

<script>

    // ==========================
    // Articles Chart
    // Line Chart
    // ==========================

    new Chart(document.getElementById('articlesChart'), {

        type:'line',

        data:{

            labels: <?php echo json_encode($article_labels); ?>,

            datasets:[{

                label:'Articles',

                data: <?php echo json_encode($article_data); ?>,

                borderColor:'#0f9d58',

                backgroundColor:'rgba(15,157,88,0.2)',

                borderWidth:3,

                fill:true,

                tension:0.4,

                pointBackgroundColor:'#0f9d58',

                pointRadius:4
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }
    });

    // ==========================
    // Books Chart
    // Doughnut Chart
    // ==========================

    new Chart(document.getElementById('booksChart'), {

        type:'doughnut',

        data:{

            labels: <?php echo json_encode($book_labels); ?>,

            datasets:[{

                data: <?php echo json_encode($book_data); ?>,

                backgroundColor:[
                    '#2563eb',
                    '#16a34a',
                    '#f59e0b',
                    '#dc2626',
                    '#9333ea'
                ]
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }
    });

    // ==========================
    // Translate Books Chart
    // Pie Chart
    // ==========================

    new Chart(document.getElementById('translateChart'), {

        type:'pie',

        data:{

            labels: <?php echo json_encode($translate_labels); ?>,

            datasets:[{

                data: <?php echo json_encode($translate_data); ?>,

                backgroundColor:[
                    '#0ea5e9',
                    '#14b8a6',
                    '#f97316',
                    '#8b5cf6',
                    '#ef4444'
                ]
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }
    });

    // ==========================
    // Thesis Chart
    // Bar Chart
    // ==========================

    new Chart(document.getElementById('thesisChart'), {

        type:'bar',

        data:{

            labels: <?php echo json_encode($thesis_labels); ?>,

            datasets:[{

                label:'Thesis',

                data: <?php echo json_encode($thesis_data); ?>,

                backgroundColor:[
                    '#0f9d58',
                    '#2563eb',
                    '#f59e0b',
                    '#dc2626',
                    '#9333ea'
                ],

                borderRadius:8
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }
    });

</script>

</body>
</html>