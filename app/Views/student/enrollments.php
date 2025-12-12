<?= $this->include('templates/header', ['title' => $title ?? 'My Enrollment Applications']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">My Enrollment Applications</h2>
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

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Need to apply for a new course?</h6>
                            <p class="text-muted mb-0">Submit a new enrollment application to join additional courses.</p>
                        </div>
                        <a href="<?= site_url('enrollment') ?>" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> New Application
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Applications -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-text"></i> All My Applications</h5>
                    <span class="badge bg-primary"><?= count($enrollmentApplications) ?> applications</span>
                </div>
                <div class="card-body">
                    <?php if (empty($enrollmentApplications)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-text display-1 text-muted mb-4"></i>
                            <h4>No Applications Found</h4>
                            <p class="text-muted mb-4">You haven't submitted any enrollment applications yet. Get started by applying for your first course!</p>
                            <a href="<?= site_url('enrollment') ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus"></i> Submit First Application
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Course</th>
                                        <th>Program</th>
                                        <th>Application Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($enrollmentApplications as $app): ?>
                                        <tr>
                                            <td><strong><?= esc($app['student_id']) ?></strong></td>
                                            <td>
                                                <div>
                                                    <strong>
                                                        <?= esc($app['course_code'] ? $app['course_code'] . ' - ' : '') ?>
                                                        <?= esc($app['course_title']) ?>
                                                    </strong>
                                                    <?php if ($app['teacher_name']): ?>
                                                        <br><small class="text-muted">Teacher: <?= esc($app['teacher_name']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?= esc($app['program']) ?>
                                                <br><small class="text-muted"><?= esc($app['year_level']) ?></small>
                                            </td>
                                            <td>
                                                <?= date('M j, Y', strtotime($app['enrollment_date'])) ?>
                                                <br><small class="text-muted"><?= date('g:i A', strtotime($app['enrollment_date'])) ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'Pending' => 'warning',
                                                    'Approved' => 'success',
                                                    'Rejected' => 'danger',
                                                    'Enrolled' => 'primary'
                                                ][$app['enrollment_status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= esc($app['enrollment_status']) ?></span>
                                                <?php if ($app['enrollment_status'] === 'Pending'): ?>
                                                    <br><small class="text-muted">Under review</small>
                                                <?php elseif ($app['enrollment_status'] === 'Approved'): ?>
                                                    <br><small class="text-success">Ready to enroll</small>
                                                <?php elseif ($app['enrollment_status'] === 'Enrolled'): ?>
                                                    <br><small class="text-primary">Active enrollment</small>
                                                <?php elseif ($app['enrollment_status'] === 'Rejected'): ?>
                                                    <br><small class="text-danger">Not approved</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewApplicationModal<?= $app['id'] ?>" 
                                                        title="View Details">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <?php if (in_array($app['enrollment_status'], ['Approved', 'Enrolled'])): ?>
                                                    <a href="<?= site_url('materials/view/' . $app['course_id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary ms-1" 
                                                       title="View Materials">
                                                        <i class="bi bi-folder2-open"></i> Materials
                                                    </a>
                                                <?php endif; ?>
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

<!-- Application Details Modals -->
<?php if (!empty($enrollmentApplications)): ?>
    <?php foreach($enrollmentApplications as $app): ?>
    <div class="modal fade" id="viewApplicationModal<?= $app['id'] ?>" tabindex="-1" aria-labelledby="viewApplicationModalLabel<?= $app['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewApplicationModalLabel<?= $app['id'] ?>">
                        Enrollment Application Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2">Personal Information</h6>
                            <p><strong>Student ID:</strong> <?= esc($app['student_id']) ?></p>
                            <p><strong>Name:</strong> <?= esc($app['first_name'] . ' ' . ($app['middle_name'] ? $app['middle_name'] . ' ' : '') . $app['last_name']) ?></p>
                            <p><strong>Age:</strong> <?= esc($app['age']) ?> years old</p>
                            <p><strong>Gender:</strong> <?= esc($app['gender']) ?></p>
                            <p><strong>Contact:</strong> <?= esc($app['contact_number']) ?></p>
                            <p><strong>Email:</strong> <?= esc($app['email_address']) ?></p>
                            <p><strong>Address:</strong> <?= esc($app['address']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2">Academic Information</h6>
                            <p><strong>Course:</strong> 
                                <?= esc($app['course_code'] ? $app['course_code'] . ' - ' : '') ?>
                                <?= esc($app['course_title']) ?>
                            </p>
                            <p><strong>Program:</strong> <?= esc($app['program']) ?></p>
                            <p><strong>Year Level:</strong> <?= esc($app['year_level']) ?></p>
                            <?php if ($app['credits']): ?>
                                <p><strong>Credits:</strong> <?= esc($app['credits']) ?></p>
                            <?php endif; ?>
                            <?php if ($app['teacher_name']): ?>
                                <p><strong>Teacher:</strong> <?= esc($app['teacher_name']) ?></p>
                            <?php endif; ?>
                            <p><strong>Application Date:</strong> <?= date('F j, Y, g:i A', strtotime($app['enrollment_date'])) ?></p>
                        </div>
                    </div>

                    <?php if ($app['semester_name'] || $app['term_name'] || $app['school_year']): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Academic Period</h6>
                            <div class="row">
                                <?php if ($app['semester_name']): ?>
                                <div class="col-md-4">
                                    <p><strong>Semester:</strong> <?= esc($app['semester_name']) ?></p>
                                </div>
                                <?php endif; ?>
                                <?php if ($app['term_name']): ?>
                                <div class="col-md-4">
                                    <p><strong>Term:</strong> <?= esc($app['term_name']) ?></p>
                                </div>
                                <?php endif; ?>
                                <?php if ($app['school_year']): ?>
                                <div class="col-md-4">
                                    <p><strong>School Year:</strong> <?= esc($app['school_year']) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2">Guardian Information</h6>
                            <p><strong>Guardian Name:</strong> <?= esc($app['guardian_name']) ?></p>
                            <p><strong>Guardian Contact:</strong> <?= esc($app['guardian_contact']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2">Application Status</h6>
                            <p><strong>Status:</strong> 
                                <?php
                                $statusClass = [
                                    'Pending' => 'warning',
                                    'Approved' => 'success',
                                    'Rejected' => 'danger',
                                    'Enrolled' => 'primary'
                                ][$app['enrollment_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= esc($app['enrollment_status']) ?></span>
                            </p>
                            <?php if ($app['updated_at']): ?>
                                <p><strong>Last Updated:</strong> <?= date('F j, Y, g:i A', strtotime($app['updated_at'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($app['notes']): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Admin Notes</h6>
                            <div class="alert alert-info">
                                <?= esc($app['notes']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <?php if ($app['enrollment_status'] === 'Pending'): ?>
                        <small class="text-muted me-3">Your application is being reviewed</small>
                    <?php elseif ($app['enrollment_status'] === 'Approved'): ?>
                        <small class="text-success me-3">Your application has been approved!</small>
                    <?php elseif ($app['enrollment_status'] === 'Enrolled'): ?>
                        <small class="text-primary me-3">You are now enrolled in this course!</small>
                        <a href="<?= site_url('materials/view/' . $app['course_id']) ?>" class="btn btn-primary">
                            <i class="bi bi-folder2-open"></i> View Materials
                        </a>
                    <?php elseif ($app['enrollment_status'] === 'Rejected'): ?>
                        <small class="text-danger me-3">Your application was not approved</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->include('templates/footer') ?>