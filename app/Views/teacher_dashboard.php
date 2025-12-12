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
        <div class="col-md-3 col-sm-6">
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
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Approved Students</h6>
                            <h2 class="card-title mb-0"><?= esc($my_enrollment_count ?? 0) ?></h2>
                        </div>
                        <div><i class="bi bi-people fs-1 opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="<?= site_url('teacher/pending-enrollments') ?>" class="text-decoration-none">
                <div class="card text-white bg-warning h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2 text-white-50">Pending Approvals</h6>
                                <h2 class="card-title mb-0"><?= esc($pending_enrollment_count ?? 0) ?></h2>
                                <small class="text-white-50">Click to manage</small>
                            </div>
                            <div><i class="bi bi-clock fs-1 opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
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
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('teacher/courses') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-book"></i> My Courses
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('teacher/create-course') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-plus-circle"></i> Create Course
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('teacher/pending-enrollments') ?>" class="btn btn-outline-warning w-100">
                                <i class="bi bi-clock"></i> Pending Approvals
                                <?php if ($pending_enrollment_count > 0): ?>
                                    <span class="badge bg-danger ms-1"><?= $pending_enrollment_count ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('materials') ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-folder"></i> Course Materials
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('materials/upload') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-cloud-upload"></i> Upload Material
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('announcements') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-megaphone"></i> Announcements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Enrollments -->
    <?php if (!empty($pending_enrollments)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm baby-pink-section">
                <div class="card-header baby-pink-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark"><i class="bi bi-clock"></i> Pending Enrollment Approvals</h5>
                    <a href="<?= site_url('teacher/pending-enrollments') ?>" class="btn btn-sm btn-dark">View All</a>
                </div>
                <div class="card-body baby-pink-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle baby-pink-table">
                            <thead class="baby-pink-table-header">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Applied Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($pending_enrollments as $enrollment): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></strong>
                                            <br><small class="text-muted"><?= esc($enrollment['email_address']) ?></small>
                                        </td>
                                        <td>
                                            <strong><?= esc($enrollment['course_name']) ?></strong>
                                            <br><small class="text-muted"><?= esc($enrollment['course_code']) ?></small>
                                        </td>
                                        <td>
                                            <small><?= date('M j, Y, g:i A', strtotime($enrollment['enrollment_date'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= site_url('teacher/view-enrollment/' . $enrollment['id']) ?>" 
                                                   class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= site_url('teacher/approve-enrollment/' . $enrollment['id']) ?>" 
                                                   class="btn btn-sm btn-outline-success" title="Approve"
                                                   onclick="return confirm('Are you sure you want to approve this enrollment?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="Reject" data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal<?= $enrollment['id'] ?>">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal<?= $enrollment['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject Enrollment</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="<?= site_url('teacher/reject-enrollment/' . $enrollment['id']) ?>" method="POST">
                                                            <?= csrf_field() ?>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to reject the enrollment for <strong><?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></strong>?</p>
                                                                <div class="mb-3">
                                                                    <label for="reason<?= $enrollment['id'] ?>" class="form-label">Reason (Optional)</label>
                                                                    <textarea class="form-control" id="reason<?= $enrollment['id'] ?>" name="reason" rows="3" placeholder="Enter reason for rejection..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Reject Enrollment</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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

<style>
.clickable-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.clickable-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
}

.clickable-card:active {
    transform: translateY(0);
}

/* Baby Pink Aesthetic for Pending Enrollments */
.baby-pink-section {
    background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%);
    border: 2px solid #f48fb1;
}

.baby-pink-header {
    background: linear-gradient(135deg, #f8bbd9 0%, #f48fb1 100%);
    border-bottom: 2px solid #e91e63;
}

.baby-pink-body {
    background: rgba(252, 228, 236, 0.3);
}

.baby-pink-table {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
}

.baby-pink-table-header th {
    background: linear-gradient(135deg, #f8bbd9 0%, #f48fb1 100%);
    color: #4a148c;
    font-weight: 600;
    border: none;
    padding: 12px;
}

.baby-pink-table tbody tr {
    background: rgba(255, 255, 255, 0.9);
}

.baby-pink-table tbody tr:hover {
    background: rgba(248, 187, 217, 0.2);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.baby-pink-section .btn-outline-info {
    border-color: #e91e63;
    color: #e91e63;
}

.baby-pink-section .btn-outline-info:hover {
    background-color: #e91e63;
    border-color: #e91e63;
}

.baby-pink-section .btn-outline-success {
    border-color: #4caf50;
    color: #4caf50;
}

.baby-pink-section .btn-outline-success:hover {
    background-color: #4caf50;
    border-color: #4caf50;
}

.baby-pink-section .btn-outline-danger {
    border-color: #f44336;
    color: #f44336;
}

.baby-pink-section .btn-outline-danger:hover {
    background-color: #f44336;
    border-color: #f44336;
}
</style>

<?= $this->include('templates/footer') ?>
