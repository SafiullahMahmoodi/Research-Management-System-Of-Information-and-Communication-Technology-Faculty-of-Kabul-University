<?php
// dashboard.php

session_start();

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
                    FROM translate_books
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

    <!-- Bootstrap -->

    <link rel="stylesheet"
    href="../css/bootstrap.min.css">

    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
    href="../css/bootstrap-icons.min.css">

    <!-- Chart JS -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }

    body{
        font-family:Segoe UI, sans-serif;
        background:#f4f6f9;
        overflow:hidden;
    }

    /* =========================
       Header
    ========================= */

    .main-header{

        height:55px;
        background:#0f9d58;

        display:flex;
        justify-content:space-between;
        align-items:center;

        padding:0 18px;

        color:white;
    }

    .header-title{

        font-size:14px;
        font-weight:600;
    }

    .logout-btn{

        background:white;
        color:#0f9d58;

        text-decoration:none;

        padding:4px 12px;

        border-radius:6px;

        font-size:12px;
        font-weight:600;

        transition:0.3s;
    }

    .logout-btn:hover{

        background:#e5e7eb;
        color:#0c7c45;
    }

    /* =========================
       Main Layout
    ========================= */

    .main-container{

        display:flex;

        height:calc(100vh - 55px);
    }

    /* =========================
       Sidebar
    ========================= */

    .sidebar{

        width:210px;

        background:#ffffff;

        border-right:1px solid #d1d5db;

        padding:14px 10px;

        flex-shrink:0;

        overflow-y:auto;
    }

    .sidebar-menu{

        list-style:none;

        padding-left:0;

        margin-bottom:0;
    }

    .sidebar-menu li{

        margin-bottom:5px;
    }

    .sidebar-menu a{

        display:flex;

        align-items:center;

        gap:10px;

        text-decoration:none;

        color:#374151;

        padding:8px 10px;

        border-radius:8px;

        font-size:12px;

        font-weight:500;

        transition:0.3s;
    }

    .sidebar-menu a i{

        font-size:14px;

        min-width:18px;

        text-align:center;
    }

    .sidebar-menu a:hover{

        background:#0f9d58;

        color:white;
    }

    .sidebar-menu .active{

        background:#0f9d58;

        color:white;
    }

    /* =========================
       Content
    ========================= */

    .content{

        flex:1;

        padding:12px;

        overflow:hidden;
    }

    .page-title{

        font-size:20px;
        font-weight:bold;

        color:#1f2937;

        margin-bottom:12px;
    }

    /* =========================
       Graph Grid
    ========================= */

    .graph-grid{

        display:grid;

        grid-template-columns:1fr 1fr;

        gap:12px;

        height:calc(100vh - 100px);
    }

    /* =========================
       Graph Card
    ========================= */

    .graph-card{

        background:white;

        border-radius:12px;

        padding:12px;

        box-shadow:0 2px 8px rgba(0,0,0,0.06);

        display:flex;
        flex-direction:column;
    }

    .graph-top{

        display:flex;

        justify-content:space-between;

        align-items:center;

        margin-bottom:8px;
    }

    .graph-title{

        font-size:14px;

        font-weight:600;

        color:#1f2937;
    }

    .graph-count{

        background:#0f9d58;

        color:white;

        padding:3px 10px;

        border-radius:20px;

        font-size:11px;

        font-weight:600;
    }

    .graph-card canvas{

        width:100% !important;

        height:220px !important;
    }

    /* =========================
       Responsive
    ========================= */

    @media(max-width:992px){

        body{
            overflow:auto;
        }

        .main-container{

            flex-direction:column;

            height:auto;
        }

        .sidebar{

            width:100%;
        }

        .graph-grid{

            grid-template-columns:1fr;

            height:auto;
        }

        .content{

            overflow:auto;
        }
    }

</style>

</head>

<body>

<!-- Header -->

<header class="main-header">

    <div class="header-title">

        Research Management System of Information and Communication Technology Faculty

    </div>

    <a href="../index.php" class="logout-btn">

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
                <a href="users.php">
                    <i class="bi bi-people-fill"></i>
                    Users
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