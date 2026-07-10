<?php
session_start();


if (isset($_COOKIE['lang'])) {
    $_SESSION['lang'] = $_COOKIE['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';

if ($lang == 'fa') {

    $dir = "rtl";

    $title = "درباره سیستم";
    $back = "بازگشت به صفحه اصلی";

    $heroTitle = "سیستم مدیریت تحقیقات";
    $heroText = "سیستم مدیریت تحقیقات برای  پوهنځی تکنالوژی معلوماتی و مخابراتی(ICT)";

    $aboutTitle = "درباره سیستم";
    $aboutText = "این سیستم به منظور مدیریت فعالیت‌های تحقیقاتی در  پوهنځی تکنالوژی معلوماتی و  مخابراتی توسعه یافته است. این سیستم امکان مدیریت استادان، محصلان، دیپارتمنت‌ها، مقالات علمی، مونوگراف ها، کتاب‌ها و کتاب‌های ترجمه‌شده را در یک محیط متمرکز فراهم می‌کند.";

    $facultyTitle = "درباره پوهنځی";
    $facultyText = " پوهنځی تکنالوژی معلوماتی و مخابراتی(ICT) با هدف تربیه متخصصان فناوری معلومات، توسعه تحقیقات علمی و استفاده از تکنالوژی‌های نوین فعالیت می‌کند.";

    $departmentTitle = "دیپارتمنت‌ها";

    $featureTitle = "امکانات سیستم";

    $technologyTitle = "تکنالوژی‌های استفاده شده";

    $developerTitle = "توسعه‌دهنده";

    $name = "نام";
    $email = "ایمیل";
    $contact = "شماره تماس";
    $department = "دیپارتمنت";

    $developerEmail = "safiullah.mahmoodi@example.com"; // ایمیل واقعی خود را بنویسید
    $developerContact = "+93 775503386"; // شماره واقعی خود را بنویسید
    $developerDepartment = "دیپارتمنت انجنیری سیستم‌های معلوماتی، پوهنحی تکنالوژی معلوماتی و مخابراتی (ICT)، پوهنتون کابل";
    $role = "بخش کاری";
    $project = "پروژه";
    $version = "نسخه";

    $developerRole = "توسعه‌دهنده فول‌استک PHP";
    $facultyText = " پوهنځی تکنالوژی معلوماتی و مخابراتی(ICT) یکی از پوهنځی‌های تخصصی  پوهنتون کابل است که با هدف تربیه کادرهای متخصص در عرصه فناوری معلومات، مهندسی نرم‌افزار، شبکه‌های کامپیوتری، سیستم‌های معلوماتی و پایگاه‌های داده فعالیت می‌کند. این پوهنځی علاوه بر آموزش، زمینه انجام تحقیقات علمی، نوآوری و توسعه فناوری را نیز برای استادان و محصلان فراهم می‌سازد.";
    $features = [
        "مدیریت استادان",
        "مدیریت محصلان",
        "مدیریت دیپارتمنت‌ها",
        "مدیریت مقالات علمی",
        "مدیریت پایان‌نامه‌ها",
        "مدیریت کتاب‌ها",
        "مدیریت کتاب‌های ترجمه‌شده",
        "بارگذاری فایل‌های PDF",
        "سیستم جستجوی پیشرفته",
        "مدیریت کاربران"
    ];
    $departments = [
        "دیپارتمنت انجنیری سیستم‌های معلوماتی",
        "دیپارتمنت انجنیری مخابراتی",
    ];
} else {

    $dir = "ltr";

    $title = "About System";
    $back = "Back to Home";

    $heroTitle = "Research Management System";
    $heroText = "A Web-Based Research Management System for the Faculty of Information and Communication Technology (ICT).";

    $aboutTitle = "About the System";
    $aboutText = "This system has been developed to manage research activities within the Faculty of Information and Communication Technology. It enables administrators to manage teachers, students, departments, articles, theses, books and translated books through a centralized platform.";

    $facultyTitle = "Faculty";
    $facultyText = "The Faculty of Information and Communication Technology (ICT) prepares skilled professionals in software engineering, networking, databases and modern information technologies while supporting academic research.";

    $departmentTitle = "Departments";

    $featureTitle = "System Features";

    $technologyTitle = "Technologies";

    $developerTitle = "Developer";

    $name = "Name";
    $email = "Email";
    $contact = "Contact";
    $department = "Department";

    $developerEmail = "safiullah.mahmoodi@example.com"; // ایمیل واقعی خود را بنویسید
    $developerContact = "+93 775503386"; // شماره واقعی خود را بنویسید
    $developerDepartment = "Information Systems Engineering Department, Faculty of Information and Communication Technology (ICT), Kabul University";
    $role = "Role";
    $project = "Project";
    $version = "Version";

    $developerRole = "Full-Stack PHP Web Developer";
    $facultyText = "The Faculty of Information and Communication Technology (ICT) is one of the university's specialized faculties dedicated to educating skilled professionals in Information Technology, Software Engineering, Computer Networks, Information Systems, and Database Systems. In addition to academic education, the faculty promotes scientific research, innovation, and technological development for both faculty members and students.";
    $features = [
        "Teacher Management",
        "Student Management",
        "Department Management",
        "Research Articles Management",
        "Thesis Management",
        "Books Management",
        "Translated Books Management",
        "PDF Upload",
        "Advanced Search System",
        "User Management"
    ];
    $departments = [
        "Information Systems Engineering",
        "Telecommunication Engineering",
    ];
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
        }

        .hero {
            background: linear-gradient(135deg, #0d6efd, #4f8dfd);
            color: #fff;
            padding: 60px 20px;
            border-radius: 18px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .08);
            transition: .3s;
            height: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card i {
            font-size: 40px;
            color: #0d6efd;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #777;
            font-size: 15px;
        }
    </style>

</head>

<body>

    <div class="container py-4">

        <div class="hero text-center">

            <h1 class="fw-bold">Research Management System</h1>
            <p class="lead mt-3"><?= $heroText ?></p>

        </div>

        <div class="row g-4">

            <div class="col-md-6">

                <div class="card p-4">

                    <i class="bi bi-book"></i>

                    <h4 class="mt-3"><?= $aboutTitle ?></h4>

                    <p><?= $aboutText ?></p>
                </div>

            </div>

            <div class="col-md-6">

                <div class="card p-4">

                    <i class="bi bi-bank"></i>

                    <h4 class="mt-3"><?= $facultyTitle ?></h4>

                    <p><?= $facultyText ?></p>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card p-4">

                    <i class="bi bi-building"></i>


                    <h4 class="mt-3"><?= $departmentTitle ?></h4>

                    <ul>
                        <?php foreach ($departments as $department): ?>
                            <li><?= $department ?></li>
                        <?php endforeach; ?>
                    </ul>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card p-4">

                    <i class="bi bi-stars"></i>

                    <h4 class="mt-3"><?= $featureTitle ?></h4>

                    <ul>
                        <?php foreach ($features as $feature): ?>
                            <li><?= $feature ?></li>
                        <?php endforeach; ?>
                    </ul>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card p-4">

                    <i class="bi bi-code-slash"></i>

                    <h4 class="mt-3">Technologies</h4>

                    <ul>

                        <li>PHP</li>

                        <li>MySQL</li>

                        <li>HTML5</li>

                        <li>CSS3</li>

                        <li>Bootstrap 5</li>

                        <li>JavaScript</li>

                        <li>Bootstrap Icons</li>

                    </ul>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card p-4">

                    <i class="bi bi-person-circle"></i>

                    <h4 class="mt-3"><?= $developerTitle ?></h4>

                    <p>

                        <strong><?= $name ?>:</strong> Safiullah Mahmoodi

                        <br><br>

                        <strong><?= $email ?>:</strong> <?= $developerEmail ?>

                        <br><br>

                        <strong><?= $contact ?>:</strong> <?= $developerContact ?>

                        <br><br>

                        <strong><?= $department ?>:</strong> <?= $developerDepartment ?>

                        <br><br>

                        <strong><?= $role ?>:</strong> <?= $developerRole ?>

                        <br><br>

                        <strong><?= $project ?>:</strong> Research Management System

                        <br><br>

                        <strong><?= $version ?>:</strong> 1.0

                    </p>

                </div>
            </div>

        </div>

        <div class="text-center mt-5">

            <a href="index.php" class="btn btn-primary btn-lg">

                <i class="bi bi-arrow-left-circle"></i>

                <?= $back ?>

            </a>

        </div>

        <div class="footer">

            <hr>

            <p>

                © 2026 Research Management System

                <br>

                Developed by <strong>Safiullah Mahmoodi</strong>

            </p>

        </div>

    </div>

</body>

</html>