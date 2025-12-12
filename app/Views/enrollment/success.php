<?= $this->include('templates/header', ['title' => $title ?? 'Enrollment Successful']) ?>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">
                        <i class="bi bi-check-circle-fill"></i> Enrollment Successful!
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="alert alert-success">
                            <h5><i class="bi bi-info-circle"></i> Your enrollment application has been submitted successfully!</h5>
                            <p class="mb-0">Please keep your enrollment details for your records. You will be notified about the status of your application.</p>
                        </div>
                    </div>

                    <!-- Enrollment Details -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-file-text"></i> Enrollment Details
                            </h5>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Enrollment ID:</strong><br>
                            <span class="badge bg-primary fs-6"><?= esc($enrollment['id']) ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Application Date:</strong><br>
                            <?= date('F j, Y, g:i A', strtotime($enrollment['enrollment_date'])) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Student ID:</strong><br>
                            <?= esc($enrollment['student_id']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-warning"><?= esc($enrollment['enrollment_status']) ?></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Student Name:</strong><br>
                            <?= esc($enrollment['first_name'] . ' ' . ($enrollment['middle_name'] ? $enrollment['middle_name'] . ' ' : '') . $enrollment['last_name']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Course:</strong><br>
                            <?= esc($enrollment['course_code'] ? $enrollment['course_code'] . ' - ' : '') ?>
                            <?= esc($enrollment['course_title']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Course Instructor:</strong><br>
                            <span class="text-success fw-bold">
                                <i class="bi bi-person-check"></i> 
                                <?= esc($enrollment['teacher_name'] ?? 'Not assigned') ?>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Program:</strong><br>
                            <?= esc($enrollment['program']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Year Level:</strong><br>
                            <?= esc($enrollment['year_level']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Age:</strong><br>
                            <?= esc($enrollment['age']) ?> years old
                        </div>
                        <div class="col-md-4">
                            <strong>Gender:</strong><br>
                            <?= esc($enrollment['gender']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Contact Number:</strong><br>
                            <?= esc($enrollment['contact_number']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong><br>
                            <?= esc($enrollment['email_address']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Address:</strong><br>
                            <?= esc($enrollment['address']) ?>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Guardian Name:</strong><br>
                            <?= esc($enrollment['guardian_name']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Guardian Contact:</strong><br>
                            <?= esc($enrollment['guardian_contact']) ?>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <?php if ($enrollment['semester_name'] || $enrollment['term_name'] || $enrollment['school_year']): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-secondary border-bottom pb-2 mb-3">Academic Period</h6>
                        </div>
                        <?php if ($enrollment['semester_name']): ?>
                        <div class="col-md-4">
                            <strong>Semester:</strong><br>
                            <?= esc($enrollment['semester_name']) ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($enrollment['term_name']): ?>
                        <div class="col-md-4">
                            <strong>Term:</strong><br>
                            <?= esc($enrollment['term_name']) ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($enrollment['school_year']): ?>
                        <div class="col-md-4">
                            <strong>School Year:</strong><br>
                            <?= esc($enrollment['school_year']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Teacher Approval Notice -->
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-clock"></i> Awaiting Teacher Approval</h6>
                        <p class="mb-2">
                            Your enrollment application has been sent to 
                            <strong><?= esc($enrollment['teacher_name'] ?? 'the course instructor') ?></strong> 
                            for review and approval.
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-info-circle"></i> 
                            You will be notified once the teacher reviews your application.
                        </p>
                    </div>

                    <!-- Next Steps -->
                    <div class="alert alert-info">
                        <h6><i class="bi bi-lightbulb"></i> Next Steps:</h6>
                        <ul class="mb-0">
                            <li>Your application is currently <strong>pending teacher approval</strong></li>
                            <li>The course instructor will review your application and make a decision</li>
                            <li>You will receive a notification about your application status</li>
                            <li>Please keep your Enrollment ID (<strong><?= esc($enrollment['id']) ?></strong>) for future reference</li>
                            <li>Check your student dashboard regularly for status updates</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="<?= site_url('student/dashboard') ?>" class="btn btn-success">
                            <i class="bi bi-speedometer2"></i> View My Dashboard
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary ms-2">
                            <i class="bi bi-printer"></i> Print Details
                        </button>
                        <a href="<?= site_url('/enrollment') ?>" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-plus-circle"></i> New Enrollment
                        </a>
                        <a href="<?= site_url('/') ?>" class="btn btn-outline-info ms-2">
                            <i class="bi bi-house"></i> Go to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
@media print {
    .btn, .alert-info {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<?= $this->include('templates/footer') ?>