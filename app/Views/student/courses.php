<?= $this->include('templates/header', ['title' => $title ?? 'My Courses']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">My Courses</h2>
            <a href="<?= site_url('student/dashboard') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Student Dashboard
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

    <!-- Courses -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-book"></i> All My Courses</h5>
                    <div>
                        <a href="<?= site_url('enrollment') ?>" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-circle"></i> Apply for New Course
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($enrolledCourses)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-book display-1 text-muted mb-4"></i>
                            <h4>No Courses Found</h4>
                            <p class="text-muted mb-4">You are not enrolled in any courses yet. Apply for enrollment to get started!</p>
                            <a href="<?= site_url('enrollment') ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus"></i> Apply for Enrollment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach($enrolledCourses as $course): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <?= esc($course['course_code'] ?? 'Course') ?>
                                                <span class="float-end">
                                                    <?php
                                                    $statusClass = [
                                                        'Pending' => 'warning',
                                                        'Approved' => 'success',
                                                        'Rejected' => 'danger',
                                                        'Enrolled' => 'light'
                                                    ][$course['enrollment_status']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?> text-dark">
                                                        <?= esc($course['enrollment_status']) ?>
                                                    </span>
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                            <p class="card-text text-muted">
                                                <?= esc(substr($course['description'] ?? 'No description available', 0, 100)) ?>
                                                <?= strlen($course['description'] ?? '') > 100 ? '...' : '' ?>
                                            </p>
                                            
                                            <div class="row text-sm mb-3">
                                                <?php if ($course['credits']): ?>
                                                <div class="col-6">
                                                    <strong>Credits:</strong> <?= esc($course['credits']) ?>
                                                </div>
                                                <?php endif; ?>
                                                <?php if ($course['teacher_name']): ?>
                                                <div class="col-6">
                                                    <strong>Teacher:</strong> <?= esc($course['teacher_name']) ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($course['semester_name'] || $course['term_name'] || $course['school_year']): ?>
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <?= implode(' | ', array_filter([
                                                        $course['semester_name'],
                                                        $course['term_name'],
                                                        $course['school_year']
                                                    ])) ?>
                                                </small>
                                            </div>
                                            <?php endif; ?>

                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar"></i> 
                                                    Enrolled: <?= date('M j, Y', strtotime($course['enrollment_date'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <div class="d-flex justify-content-between">
                                                <?php if (in_array($course['enrollment_status'], ['Approved', 'Enrolled'])): ?>
                                                    <a href="<?= site_url('materials/view/' . $course['id']) ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-folder2-open"></i> Materials
                                                    </a>
                                                    <span class="badge bg-success">Active</span>
                                                <?php elseif ($course['enrollment_status'] === 'Pending'): ?>
                                                    <span class="text-muted">
                                                        <i class="bi bi-clock"></i> Awaiting approval
                                                    </span>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php elseif ($course['enrollment_status'] === 'Rejected'): ?>
                                                    <span class="text-danger">
                                                        <i class="bi bi-x-circle"></i> Application rejected
                                                    </span>
                                                    <span class="badge bg-danger">Rejected</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
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