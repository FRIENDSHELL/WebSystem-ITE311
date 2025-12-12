<?= $this->include('templates/header', ['title' => $title ?? 'Admin Dashboard']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Admin Dashboard</h2>
            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Main Dashboard
            </a>
        </div>
        <div class="col-12 mt-3">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    <h3 class="card-title mb-2">
                        Welcome, <span class="text-primary"><?= esc($user_name) ?></span>! 👋
                    </h3>
                    <p class="text-muted mb-0">You are logged in as: <strong class="text-capitalize"><?= esc($user_role) ?></strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Users</h6>
                            <h2 class="card-title mb-0"><?= esc($totalUsers ?? 0) ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Students</h6>
                            <h2 class="card-title mb-0"><?= esc($totalStudents ?? 0) ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-person-check fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Teachers</h6>
                            <h2 class="card-title mb-0"><?= esc($totalTeachers ?? 0) ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-person-badge fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Courses</h6>
                            <h2 class="card-title mb-0"><?= esc($totalCourses ?? 0) ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-book fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('admin/courses') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-book"></i> Manage Courses
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('admin/semesters') ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-calendar3"></i> Manage Semesters
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('admin/terms') ?>" class="btn btn-outline-warning w-100">
                                <i class="bi bi-calendar-week"></i> Manage Terms
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('admin/school-years') ?>" class="btn btn-outline-danger w-100">
                                <i class="bi bi-calendar-range"></i> School Years
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('admin/enrollments') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-person-check"></i> Manage Enrollments
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('announcements') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-megaphone"></i> Announcements
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('materials') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-folder"></i> Course Materials
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('materials/upload') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-cloud-upload"></i> Upload Material
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('course/search') ?>" class="btn btn-outline-dark w-100">
                                <i class="bi bi-search"></i> Search Courses
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('admin/enrollments') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-person-plus"></i> Manage Enrollments
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-house"></i> Main Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Announcements -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bullhorn"></i> Latest Announcements</h5>
                    <a href="<?= site_url('announcements') ?>" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($announcements)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No announcements available yet.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Date Posted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($announcements as $announcement): ?>
                                        <tr>
                                            <td><strong><?= esc($announcement['title']) ?></strong></td>
                                            <td>
                                                <?php 
                                                $content = esc($announcement['content']);
                                                echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                                ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $announcement['created_at'] ? date('M j, Y, g:i A', strtotime($announcement['created_at'])) : '—' ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-graph-up"></i> System Statistics</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>Total Announcements:</strong> <?= esc($totalAnnouncements ?? 0) ?>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>Total Enrollments:</strong> <?= esc($totalEnrollments ?? 0) ?>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>Total Admins:</strong> <?= esc($totalAdmins ?? 0) ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> System Information</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Current User:</strong> <?= esc($user_name) ?></p>
                    <p class="mb-2"><strong>Role:</strong> <span class="badge bg-primary"><?= esc(ucfirst($user_role)) ?></span></p>
                    <p class="mb-0"><strong>Last Login:</strong> <span class="text-muted">Just now</span></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('templates/footer') ?>
