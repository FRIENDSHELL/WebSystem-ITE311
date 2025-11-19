<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'CI App') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #fff5f8, #ffe6f1, #ffddeb);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background-color: #ffcce0 !important;
            box-shadow: 0 4px 8px rgba(255, 182, 193, 0.25);
        }

        .navbar-brand {
            font-weight: 700;
            color: #000 !important;
        }

        .nav-link {
            color: #000 !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #333 !important;
        }

        .btn-outline-primary {
            border-color: #ff9ec6;
            color: #ff80aa;
            border-radius: 12px;
        }

        .btn-outline-primary:hover {
            background-color: #ffb6d1;
            color: white;
        }

        .btn-primary {
            background-color: #ff9ec6;
            border-color: #ff9ec6;
            border-radius: 12px;
        }

        .btn-primary:hover {
            background-color: #ff80aa;
            border-color: #ff80aa;
        }

        .btn-danger {
            background-color: #ff6f91;
            border-color: #ff6f91;
            border-radius: 12px;
        }

        .btn-danger:hover {
            background-color: #ff4d6d;
            border-color: #ff4d6d;
        }

        .container {
            max-width: 960px;
        }

        .card {
            background: #fff0f6;
            border: 1px solid #ffd6eb;
            border-radius: 20px;
            box-shadow: 0 6px 16px rgba(255, 182, 193, 0.35);
            max-width: 550px;
            margin: auto;
        }

        .card-body {
            padding: 2.5rem;
        }

        .card h2, .card h3 {
            color: #a26277;
            font-weight: 600;
            text-align: center;
        }

        .form-control {
            border: 1px solid #ffd6eb;
            border-radius: 14px;
            padding: 12px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #ff9ec6;
            box-shadow: 0 0 10px rgba(255, 158, 198, 0.4);
        }

        /* Login form container */
        .login-card {
            background: #fff0f6;
            border: 1px solid #ffd6eb;
            border-radius: 20px;
            box-shadow: 0 6px 16px rgba(255, 182, 193, 0.35);
            max-width: 400px;
            width: 90%;
            margin: 60px auto;
            padding: 40px;
            text-align: center;
        }

        .login-card h2 {
            color: #a26277;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .login-card .form-control {
            margin-bottom: 20px;
        }

        .login-card .btn {
            background-color: #ff9ec6;
            border-color: #ff9ec6;
            border-radius: 12px;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        .login-card .btn:hover {
            background-color: #ff80aa;
            border-color: #ff80aa;
        }

        .login-card p {
            margin-top: 20px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url('/') ?>">CI App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- LEFT SIDE NAV LINKS -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('contact') ?>">Contact</a></li>

                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                <?php endif; ?>
            </ul>

            <!-- RIGHT SIDE NAVIGATION -->
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (!session()->get('isLoggedIn')): ?>
                    <!-- Not logged in - Show Login and Register buttons -->
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="<?= site_url('login') ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="<?= site_url('register') ?>">Register</a>
                    </li>
                <?php else: ?>
                    <!-- Logged in - Show notification and logout -->
                    <!-- 🔔 Notification Dropdown Trigger -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-bell" style="font-size: 1.5rem;"></i>
                            <!-- 🔴 Notification Badge -->
                            <span id="notificationBadge"
                                  class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"
                                  style="font-size: 0.7rem;">
                                0
                            </span>
                        </a>
                        <!-- 📩 Dropdown List -->
                        <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 300px; max-height: 250px; overflow-y: auto;">
                            <div id="notificationList">
                                <p class="text-center text-muted m-0">Loading...</p>
                            </div>
                        </ul>
                    </li>
                    <li class="nav-item d-flex align-items-center me-3">
                        <span class="fw-bold text-dark">Hello, <?= esc(session()->get('name')) ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="<?= site_url('logout') ?>">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <?= $this->renderSection('content') ?>
</div>

</body>
</html>
