<?= $this->include('templates/header', ['title' => $title ?? 'Pending Enrollments']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Pending Enrollment Approvals</h2>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pending Enrollments</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm baby-pink-section">
                <div class="card-header baby-pink-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark">
                        <i class="bi bi-clock"></i> 
                        Enrollment Requests Awaiting Your Approval
                        <span class="badge bg-dark ms-2"><?= count($pending_enrollments) ?></span>
                    </h5>
                </div>
                <div class="card-body baby-pink-body">
                    <?php if (empty($pending_enrollments)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h4 class="mt-3 text-muted">All caught up!</h4>
                            <p class="text-muted">No pending enrollment requests at the moment.</p>
                            <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle baby-pink-table">
                                <thead class="baby-pink-table-header">
                                    <tr>
                                        <th>Student Information</th>
                                        <th>Course</th>
                                        <th>Contact Details</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pending_enrollments as $enrollment): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong class="text-primary"><?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></strong>
                                                    <?php if (!empty($enrollment['middle_name'])): ?>
                                                        <span class="text-muted"><?= esc($enrollment['middle_name']) ?></span>
                                                    <?php endif; ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-person-badge"></i> ID: <?= esc($enrollment['student_id']) ?>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar"></i> Age: <?= esc($enrollment['age']) ?> | 
                                                        <i class="bi bi-gender-ambiguous"></i> <?= esc($enrollment['gender']) ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= esc($enrollment['course_name']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-code"></i> <?= esc($enrollment['course_code']) ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <small>
                                                        <i class="bi bi-envelope"></i> <?= esc($enrollment['email_address']) ?>
                                                        <br>
                                                        <i class="bi bi-telephone"></i> <?= esc($enrollment['contact_number']) ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M j, Y', strtotime($enrollment['enrollment_date'])) ?>
                                                    <br>
                                                    <?= date('g:i A', strtotime($enrollment['enrollment_date'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    <a href="<?= site_url('teacher/view-enrollment/' . $enrollment['id']) ?>" 
                                                       class="btn btn-sm btn-outline-info mb-1">
                                                        <i class="bi bi-eye"></i> View Details
                                                    </a>
                                                    <a href="<?= site_url('teacher/approve-enrollment/' . $enrollment['id']) ?>" 
                                                       class="btn btn-sm btn-success mb-1"
                                                       onclick="return confirm('Are you sure you want to approve this enrollment?')">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#rejectModal<?= $enrollment['id'] ?>">
                                                        <i class="bi bi-x-circle"></i> Reject
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
                                                                    <div class="alert alert-warning">
                                                                        <i class="bi bi-exclamation-triangle"></i>
                                                                        You are about to reject the enrollment for:
                                                                        <br><strong><?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></strong>
                                                                        <br>Course: <strong><?= esc($enrollment['course_name']) ?></strong>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="reason<?= $enrollment['id'] ?>" class="form-label">Reason for Rejection</label>
                                                                        <textarea class="form-control" id="reason<?= $enrollment['id'] ?>" name="reason" rows="3" 
                                                                                  placeholder="Please provide a reason for rejection (optional)..."></textarea>
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
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i>
                                        <strong>Note:</strong> Approved students will be able to access course materials and participate in class activities.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong>Important:</strong> Rejected enrollments cannot be undone. Please review carefully before rejecting.
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
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

.baby-pink-section .btn-success {
    background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
    border: none;
}

.baby-pink-section .btn-danger {
    background: linear-gradient(135deg, #f44336 0%, #ef5350 100%);
    border: none;
}
</style>

<?= $this->include('templates/footer') ?>