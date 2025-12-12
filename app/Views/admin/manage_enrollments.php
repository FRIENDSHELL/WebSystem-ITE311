<?= $this->include('templates/header', ['title' => $title ?? 'Manage Enrollments']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Manage Enrollments</h2>
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Admin Dashboard
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

    <!-- Important Notice -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Important:</strong> Enrollment approvals are handled by course teachers, not admins. 
                This page provides administrative oversight and statistics only. 
                Teachers manage approvals through their own dashboards.
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <?php if (!empty($stats)): ?>
    <div class="row g-3 mb-4">
        <?php 
        $statusColors = [
            'Pending' => 'warning',
            'Approved' => 'success',
            'Rejected' => 'danger',
            'Enrolled' => 'primary'
        ];
        foreach($stats as $stat): 
        ?>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-<?= $statusColors[$stat['enrollment_status']] ?? 'secondary' ?> h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50"><?= esc($stat['enrollment_status']) ?></h6>
                            <h2 class="card-title mb-0"><?= esc($stat['count']) ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-person-check fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Enrollments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people"></i> All Enrollment Applications</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterEnrollments('all')">All</button>
                        <button class="btn btn-sm btn-outline-warning" onclick="filterEnrollments('Pending')">Pending</button>
                        <button class="btn btn-sm btn-outline-success" onclick="filterEnrollments('Approved')">Approved</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="filterEnrollments('Rejected')">Rejected</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterEnrollments('Enrolled')">Enrolled</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($enrollments)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No enrollment applications found.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="enrollmentsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Course</th>
                                        <th>Program</th>
                                        <th>Year Level</th>
                                        <th>Application Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($enrollments as $enrollment): ?>
                                        <tr data-status="<?= esc($enrollment['enrollment_status']) ?>">
                                            <td><strong><?= esc($enrollment['student_id']) ?></strong></td>
                                            <td>
                                                <?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?>
                                                <br><small class="text-muted"><?= esc($enrollment['email_address']) ?></small>
                                            </td>
                                            <td>
                                                <?= esc($enrollment['course_code'] ? $enrollment['course_code'] . ' - ' : '') ?>
                                                <?= esc($enrollment['course_title']) ?>
                                            </td>
                                            <td><?= esc($enrollment['program']) ?></td>
                                            <td><?= esc($enrollment['year_level']) ?></td>
                                            <td>
                                                <small><?= date('M j, Y', strtotime($enrollment['enrollment_date'])) ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'Pending' => 'warning',
                                                    'Approved' => 'success',
                                                    'Rejected' => 'danger',
                                                    'Enrolled' => 'primary'
                                                ][$enrollment['enrollment_status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= esc($enrollment['enrollment_status']) ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= site_url('admin/enrollments/view/' . $enrollment['id']) ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#statusModal<?= $enrollment['id'] ?>" 
                                                            title="View Information">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                    <a href="<?= site_url('admin/enrollments/delete/' . $enrollment['id']) ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this enrollment?')"
                                                       title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
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

<!-- Enrollment Information Modals (Read-Only) -->
<?php foreach($enrollments as $enrollment): ?>
<div class="modal fade" id="statusModal<?= $enrollment['id'] ?>" tabindex="-1" aria-labelledby="statusModalLabel<?= $enrollment['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel<?= $enrollment['id'] ?>">Enrollment Information (Read-Only)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Note:</strong> Only course teachers can approve or reject enrollments. This is for administrative viewing only.
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Student:</strong></label>
                    <p><?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?> (<?= esc($enrollment['student_id']) ?>)</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Course:</strong></label>
                    <p><?= esc($enrollment['course_title'] ?? 'N/A') ?></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Current Status:</strong></label>
                    <?php
                    $statusClass = [
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        'Enrolled' => 'primary'
                    ][$enrollment['enrollment_status']] ?? 'secondary';
                    ?>
                    <p><span class="badge bg-<?= $statusClass ?>"><?= esc($enrollment['enrollment_status']) ?></span></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Application Date:</strong></label>
                    <p><?= date('F j, Y \a\t g:i A', strtotime($enrollment['enrollment_date'])) ?></p>
                </div>
                
                <?php if (!empty($enrollment['teacher_name'])): ?>
                <div class="mb-3">
                    <label class="form-label"><strong>Course Teacher:</strong></label>
                    <p><?= esc($enrollment['teacher_name']) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($enrollment['notes'])): ?>
                <div class="mb-3">
                    <label class="form-label"><strong>Notes:</strong></label>
                    <div class="alert alert-light">
                        <?= esc($enrollment['notes']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="<?= site_url('admin/enrollments/view/' . $enrollment['id']) ?>" class="btn btn-primary">
                    <i class="bi bi-eye"></i> View Full Details
                </a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function filterEnrollments(status) {
    const table = document.getElementById('enrollmentsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const rowStatus = row.getAttribute('data-status');
        
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
    
    // Update button states
    const buttons = document.querySelectorAll('.card-header button');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}
</script>

<?= $this->include('templates/footer') ?>