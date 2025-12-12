<?= $this->include('templates/header', ['title' => $title ?? 'Student Dashboard']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Student Dashboard</h2>
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

    <!-- Approved Enrollments Notification -->
    <?php 
    $approvedEnrollments = array_filter($enrollmentApplications, function($app) {
        return $app['enrollment_status'] === 'Approved';
    });
    ?>
    <?php if (!empty($approvedEnrollments)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle-fill fs-2"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">🎉 Congratulations! Your enrollment<?= count($approvedEnrollments) > 1 ? 's have' : ' has' ?> been approved!</h5>
                        <p class="mb-2">
                            <?php if (count($approvedEnrollments) === 1): ?>
                                Your teacher has approved your enrollment application for <strong><?= esc($approvedEnrollments[0]['course_title']) ?></strong>.
                            <?php else: ?>
                                You have <strong><?= count($approvedEnrollments) ?></strong> approved enrollment applications ready for you to access.
                            <?php endif; ?>
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= site_url('student/enrollments') ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-eye"></i> View All Applications
                            </a>
                            <a href="<?= site_url('student/courses') ?>" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-book"></i> My Courses
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Rejected Enrollments Notification -->
    <?php 
    $rejectedEnrollments = array_filter($enrollmentApplications, function($app) {
        return $app['enrollment_status'] === 'Rejected';
    });
    ?>
    <?php if (!empty($rejectedEnrollments)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">Application Update</h5>
                        <p class="mb-2">
                            <?php if (count($rejectedEnrollments) === 1): ?>
                                Your enrollment application for <strong><?= esc($rejectedEnrollments[0]['course_title']) ?></strong> was not approved.
                            <?php else: ?>
                                You have <strong><?= count($rejectedEnrollments) ?></strong> enrollment applications that were not approved.
                            <?php endif; ?>
                            Please review the details and consider applying for other courses.
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= site_url('student/enrollments') ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                            <a href="<?= site_url('enrollment') ?>" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-plus-circle"></i> Apply for Other Courses
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <?php 
        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;
        foreach($enrollmentApplications as $app) {
            if($app['enrollment_status'] === 'Pending') $pendingCount++;
            elseif($app['enrollment_status'] === 'Approved') $approvedCount++;
            elseif($app['enrollment_status'] === 'Rejected') $rejectedCount++;
        }
        ?>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Pending Applications</h6>
                            <h2 class="card-title mb-0"><?= $pendingCount ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-clock fs-1 opacity-50"></i>
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
                            <h6 class="card-subtitle mb-2 text-white-50">Approved Applications</h6>
                            <h2 class="card-title mb-0"><?= $approvedCount ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Enrolled Courses</h6>
                            <h2 class="card-title mb-0"><?= count($enrolledCourses) ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-book fs-1 opacity-50"></i>
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
                            <h6 class="card-subtitle mb-2 text-white-50">Course Materials</h6>
                            <h2 class="card-title mb-0"><?= count($recentMaterials) ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-folder fs-1 opacity-50"></i>
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
                            <a href="<?= site_url('enrollment') ?>" class="btn btn-success w-100">
                                <i class="bi bi-person-plus"></i> Apply for Enrollment
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('student/courses') ?>" class="btn btn-primary w-100">
                                <i class="bi bi-book"></i> My Courses
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('student/enrollments') ?>" class="btn btn-info w-100">
                                <i class="bi bi-file-text"></i> My Applications
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= site_url('course/search') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-search"></i> Browse Courses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Enrollments Ready for Access -->
    <?php if (!empty($approvedEnrollments)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> Approved Enrollments - Ready to Access!</h5>
                    <span class="badge bg-light text-success"><?= count($approvedEnrollments) ?></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Teacher</th>
                                    <th>Approved Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($approvedEnrollments as $app): ?>
                                    <tr class="table-success">
                                        <td>
                                            <strong class="text-success"><?= esc($app['course_title'] ?? $app['course_name']) ?></strong>
                                            <?php if (!empty($app['course_code'])): ?>
                                                <br><small class="text-muted"><?= esc($app['course_code']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= esc($app['teacher_name'] ?? 'Not assigned') ?>
                                        </td>
                                        <td>
                                            <small><?= date('M j, Y', strtotime($app['updated_at'] ?? $app['enrollment_date'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= site_url('materials/view/' . $app['course_id']) ?>" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="bi bi-folder2-open"></i> Access Course
                                                </a>
                                                <a href="<?= site_url('student/enrollments') ?>" 
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-eye"></i> View Details
                                                </a>
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

    <!-- Recent Enrollment Applications -->
    <?php if (!empty($enrollmentApplications)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-text"></i> All My Applications</h5>
                    <a href="<?= site_url('student/enrollments') ?>" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Course</th>
                                    <th>Application Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach(array_slice($enrollmentApplications, 0, 5) as $app): ?>
                                    <tr>
                                        <td><strong><?= esc($app['student_id']) ?></strong></td>
                                        <td>
                                            <?= esc($app['course_code'] ? $app['course_code'] . ' - ' : '') ?>
                                            <?= esc($app['course_title']) ?>
                                        </td>
                                        <td>
                                            <small><?= date('M j, Y', strtotime($app['enrollment_date'])) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'Pending' => 'warning',
                                                'Approved' => 'success',
                                                'Rejected' => 'danger',
                                                'Enrolled' => 'primary'
                                            ][$app['enrollment_status']] ?? 'secondary';
                                            
                                            $statusIcon = [
                                                'Pending' => 'clock',
                                                'Approved' => 'check-circle',
                                                'Rejected' => 'x-circle',
                                                'Enrolled' => 'book'
                                            ][$app['enrollment_status']] ?? 'question-circle';
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <i class="bi bi-<?= $statusIcon ?>"></i> <?= esc($app['enrollment_status']) ?>
                                            </span>
                                            <?php if ($app['enrollment_status'] === 'Approved'): ?>
                                                <br><small class="text-success fw-bold">✨ Ready to access!</small>
                                            <?php elseif ($app['enrollment_status'] === 'Pending'): ?>
                                                <br><small class="text-muted">Awaiting teacher approval</small>
                                            <?php elseif ($app['enrollment_status'] === 'Rejected'): ?>
                                                <br><small class="text-danger">See details for reason</small>
                                            <?php endif; ?>
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

    <!-- Enrolled Courses and Recent Materials -->
    <div class="row">
        <!-- Enrolled Courses -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-book"></i> My Courses</h5>
                    <a href="<?= site_url('student/courses') ?>" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($enrolledCourses)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-book display-4 text-muted mb-3"></i>
                            <h6>No Enrolled Courses</h6>
                            <p class="text-muted mb-3">You are not enrolled in any course yet.</p>
                            <a href="<?= site_url('enrollment') ?>" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Apply for Enrollment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach(array_slice($enrolledCourses, 0, 5) as $course): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc($course['title']) ?></strong>
                                        <?php if ($course['course_code']): ?>
                                            <br><small class="text-muted"><?= esc($course['course_code']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= site_url('materials/view/' . $course['id']) ?>" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-folder2-open"></i> Materials
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Materials -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-folder"></i> Recent Materials</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentMaterials)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-folder display-4 text-muted mb-3"></i>
                            <h6>No Materials Available</h6>
                            <p class="text-muted mb-0">Materials will appear here once you're enrolled in courses.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($recentMaterials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc($material['file_name']) ?></strong>
                                        <br><small class="text-muted">From: <?= esc($material['course_title']) ?></small>
                                        <br><small class="text-muted"><?= date('M j, Y', strtotime($material['created_at'])) ?></small>
                                    </div>
                                    <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('templates/footer') ?>