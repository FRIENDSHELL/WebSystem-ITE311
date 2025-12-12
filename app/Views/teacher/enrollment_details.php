<?= $this->include('templates/header', ['title' => $title ?? 'Enrollment Details']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Enrollment Details</h2>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/pending-enrollments') ?>">Pending Enrollments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Enrollment Details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Student Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Personal Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td><?= esc($enrollment['first_name'] . ' ' . ($enrollment['middle_name'] ? $enrollment['middle_name'] . ' ' : '') . $enrollment['last_name']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Student ID:</strong></td>
                                    <td><span class="badge bg-secondary"><?= esc($enrollment['student_id']) ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td><?= esc($enrollment['age']) ?> years old</td>
                                </tr>
                                <tr>
                                    <td><strong>Birth Date:</strong></td>
                                    <td><?= date('F j, Y', strtotime($enrollment['birth_date'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td><?= esc($enrollment['gender']) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Contact Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><a href="mailto:<?= esc($enrollment['email_address']) ?>"><?= esc($enrollment['email_address']) ?></a></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><a href="tel:<?= esc($enrollment['contact_number']) ?>"><?= esc($enrollment['contact_number']) ?></a></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?= esc($enrollment['address']) ?></td>
                                </tr>
                            </table>

                            <h6 class="text-muted mt-3">Guardian Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Guardian Name:</strong></td>
                                    <td><?= esc($enrollment['guardian_name']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Guardian Contact:</strong></td>
                                    <td><a href="tel:<?= esc($enrollment['guardian_contact']) ?>"><?= esc($enrollment['guardian_contact']) ?></a></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Program:</strong></td>
                                    <td><?= esc($enrollment['program']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Year Level:</strong></td>
                                    <td><?= esc($enrollment['year_level']) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td><?= esc($enrollment['course_title'] ?? $enrollment['course_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Course Code:</strong></td>
                                    <td><span class="badge bg-info"><?= esc($enrollment['course_code'] ?? 'N/A') ?></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Enrollment Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Enrollment Status</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php
                        $statusClass = '';
                        $statusIcon = '';
                        switch($enrollment['enrollment_status']) {
                            case 'Pending':
                                $statusClass = 'bg-warning text-dark';
                                $statusIcon = 'bi-clock';
                                break;
                            case 'Approved':
                                $statusClass = 'bg-success';
                                $statusIcon = 'bi-check-circle';
                                break;
                            case 'Rejected':
                                $statusClass = 'bg-danger';
                                $statusIcon = 'bi-x-circle';
                                break;
                            default:
                                $statusClass = 'bg-secondary';
                                $statusIcon = 'bi-question-circle';
                        }
                        ?>
                        <span class="badge <?= $statusClass ?> fs-6 p-3">
                            <i class="bi <?= $statusIcon ?>"></i> <?= esc($enrollment['enrollment_status']) ?>
                        </span>
                    </div>
                    
                    <p class="text-muted mb-3">
                        <strong>Applied:</strong><br>
                        <?= date('F j, Y \a\t g:i A', strtotime($enrollment['enrollment_date'])) ?>
                    </p>

                    <?php if ($enrollment['enrollment_status'] === 'Pending'): ?>
                        <div class="d-grid gap-2">
                            <a href="<?= site_url('teacher/approve-enrollment/' . $enrollment['id']) ?>" 
                               class="btn btn-success"
                               onclick="return confirm('Are you sure you want to approve this enrollment?')">
                                <i class="bi bi-check-circle"></i> Approve Enrollment
                            </a>
                            <button type="button" class="btn btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Reject Enrollment
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($enrollment['notes'])): ?>
                        <div class="mt-3">
                            <h6 class="text-muted">Notes:</h6>
                            <div class="alert alert-light">
                                <?= esc($enrollment['notes']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Course Information -->
            <?php if (!empty($enrollment['course_description'])): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-book"></i> Course Information</h5>
                </div>
                <div class="card-body">
                    <p><?= esc($enrollment['course_description']) ?></p>
                    <?php if (!empty($enrollment['credits'])): ?>
                        <p><strong>Credits:</strong> <?= esc($enrollment['credits']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($enrollment['teacher_name'])): ?>
                        <p><strong>Instructor:</strong> <?= esc($enrollment['teacher_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('teacher/pending-enrollments') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Pending Enrollments
                </a>
                
                <?php if ($enrollment['enrollment_status'] === 'Pending'): ?>
                <div>
                    <a href="<?= site_url('teacher/approve-enrollment/' . $enrollment['id']) ?>" 
                       class="btn btn-success me-2"
                       onclick="return confirm('Are you sure you want to approve this enrollment?')">
                        <i class="bi bi-check-circle"></i> Approve
                    </a>
                    <button type="button" class="btn btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Reject Modal -->
<?php if ($enrollment['enrollment_status'] === 'Pending'): ?>
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('teacher/reject-enrollment/' . $enrollment['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        You are about to reject the enrollment for:
                        <br><strong><?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></strong>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" 
                                  placeholder="Please provide a detailed reason for rejection..."></textarea>
                        <div class="form-text">This reason will be recorded and may be shared with the student.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Reject Enrollment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->include('templates/footer') ?>