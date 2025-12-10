<?= $this->include('templates/header', ['title' => $title ?? 'Teacher Dashboard']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Teacher Dashboard</h2>
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

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">My Courses</h6>
                            <h2 class="card-title mb-0"><?= esc($my_course_count ?? 0) ?></h2>
                        </div>
                        <div><i class="bi bi-book fs-1 opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">My Students (Enrolled)</h6>
                            <h2 class="card-title mb-0"><?= esc($my_enrollment_count ?? 0) ?></h2>
                        </div>
                        <div><i class="bi bi-people fs-1 opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Announcements</h6>
                            <h2 class="card-title mb-0"><?= esc($announcement_count ?? 0) ?></h2>
                        </div>
                        <div><i class="bi bi-megaphone fs-1 opacity-50"></i></div>
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
                        <div class="col-md-4 col-sm-6">
                            <a href="<?= site_url('announcements') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-megaphone"></i> View Announcements
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-house"></i> Main Dashboard
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger w-100">
                                <i class="bi bi-box-arrow-right"></i> Logout
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
</main>

<?= $this->include('templates/footer') ?>
