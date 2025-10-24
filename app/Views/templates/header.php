<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?></title>

    <!-- ✅ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- ✅ Custom Style -->
    <style>
        body {
            background-color: #fffafc;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(90deg, #cc6699, #e07fae);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .nav-link:hover {
            color: #ffe6f0 !important;
            transform: translateY(-1px);
        }

        main.container {
            flex: 1;
            padding-top: 30px;
        }

        footer {
            background-color: #cc6699;
            color: white;
            text-align: center;
            padding: 15px 0;
            width: 100%;
            margin-top: auto;
        }

        footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* For better visibility on small screens */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            .nav-link {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="<?= site_url('dashboard') ?>">LMS Portal</a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (session()->get('role') === 'student'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('student/courses') ?>">My Courses</a></li>

                <?php elseif (session()->get('role') === 'teacher'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('teacher/courses') ?>">My Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('teacher/students') ?>">Students</a></li>

                <?php elseif (session()->get('role') === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/dashboard') ?>">Admin Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/users') ?>">Manage Users</a></li>
                <?php endif; ?>

                <li class="nav-item"><a class="nav-link fw-bold" href="<?= site_url('logout') ?>">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ✅ MAIN CONTENT -->
<main class="container">
    <?= $this->renderSection('content') ?>
</main>
