<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
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

        /* Notification dropdown */
        .notification-dropdown {
            position: absolute;
            top: calc(100% + 0.75rem);
            right: 0;
            width: 320px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 0.75rem;
            z-index: 1050;
            display: none;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-item {
            font-size: 0.9rem;
            border: 1px solid #f3d6e4;
            border-radius: 10px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: #fff6fb;
        }

        .notification-item:last-child {
            margin-bottom: 0;
        }

        .notification-item .btn {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<!-- ✅ NAVBAR -->
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

            <!-- Left side navigation -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('contact') ?>">Contact</a></li>
            </ul>

            <!-- Right side navigation -->
            <ul class="navbar-nav ms-auto align-items-center">

                <?php if (session()->get('logged_in')): ?>

                    <!-- 🔔 Notification Bell -->
                    <li class="nav-item me-3">
                        <div class="position-relative" id="notificationBellWrapper" style="cursor:pointer;">
                            <i class="bi bi-bell-fill fs-4 text-white"></i>

                            <span id="headerNotificationBadge"
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                                  style="font-size: 0.7rem;">
                                0
                            </span>

                            <div id="headerNotificationDropdown" class="notification-dropdown">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 text-secondary">Notifications</h6>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="refreshNotificationsBtn">
                                        Refresh
                                    </button>
                                </div>
                                <div id="headerNotificationList">
                                    <p class="text-center text-muted small mb-0">No notifications yet.</p>
                                </div>
                            </div>
                        </div>
                    </li>

                    <?php if (session()->get('role') === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('student/courses') ?>">My Courses</a></li>

                    <?php elseif (session()->get('role') === 'teacher'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('teacher/courses') ?>">My Classes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('teacher/students') ?>">Students</a></li>

                    <?php elseif (session()->get('role') === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/dashboard') ?>">Admin Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/users') ?>">Manage Users</a></li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="<?= site_url('logout') ?>">Logout</a>
                    </li>

                <?php else: ?>

                    <!-- Not logged in -->
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('login') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('register') ?>">Register</a></li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
