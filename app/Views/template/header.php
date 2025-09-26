<!-- app/Views/dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body style="background-color: #fffafc;">

    <!-- HEADER / NAVBAR -->
    <nav class="navbar navbar-expand-lg" style="background-color:#cc6699;">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="<?= site_url('dashboard') ?>">
                LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" aria-controls="navbarNav" 
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <?php if(session()->get('role') == "student"): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">My Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Notifications</a>
                        </li>

                    <?php elseif(session()->get('role') == "teacher"): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">My Classes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Teachers</a>
                        </li>

                    <?php elseif(session()->get('role') == "admin"): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Admin dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Manage Users</a>

                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="<?= site_url('logout') ?>">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
