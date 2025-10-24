<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <h2 class="text-center fw-bold mb-4">Dashboard</h2>

    <!-- ✅ Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger text-center"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- ✅ Welcome Card -->
    <div class="card shadow-lg mx-auto mb-5" style="max-width: 700px; border-radius: 15px;">
        <div class="card-body text-center p-5">
            <h3 class="card-title mb-3">
                Welcome, <span class="text-primary text-capitalize"><?= esc($user_name ?? 'User') ?></span> 🎉
            </h3>
            <p class="text-muted">Role: <strong class="text-capitalize"><?= esc($user_role ?? '') ?></strong></p>
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm mt-2">Logout</a>
        </div>
    </div>

    <!-- ==================== ADMIN DASHBOARD ==================== -->
    <?php if ($user_role === 'admin'): ?>
        <div class="card mt-4">
            <div class="card-header fw-bold bg-dark text-white">System Overview</div>
            <div class="card-body">
                <?php if (!empty($users)): ?>
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Current Role</th>
                                <th>Change Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= esc($u['name']) ?></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td><strong><?= ucfirst(esc($u['role'])) ?></strong></td>
                                    <td>
                                        <?php if (session()->get('id') != $u['id']): ?>
                                            <form method="post" action="<?= base_url('auth/updateRole/' . $u['id']) ?>">
                                                <?= csrf_field() ?>
                                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="teacher" <?= $u['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                                    <option value="student" <?= $u['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                                </select>
                                            </form>
                                        <?php else: ?>
                                            <em class="text-muted">You</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted mb-0">No users found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header fw-bold bg-secondary text-white">Courses & Enrollments</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course Title</th>
                                <th>Students Enrolled</th>
                                <th>Student Names</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $db = \Config\Database::connect();
                            foreach ($courses as $index => $c): 
                                $courseId = $c['id'];
                                $students = $db->table('enrollments')
                                    ->select('users.name')
                                    ->join('users', 'users.id = enrollments.user_id')
                                    ->where('enrollments.course_id', $courseId)
                                    ->get()->getResultArray();
                                $studentNames = array_map(function($s){ return $s['name']; }, $students);
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($c['title'] ?? 'Unnamed Course') ?></td>
                                    <td><?= count($students) ?></td>
                                    <td><?= esc(implode(', ', $studentNames)) ?: '<span class="text-muted">None</span>' ?></td>
                                    <td>
                                        <a href="<?= base_url('materials/view/' . $courseId) ?>" 
                                           class="btn btn-sm btn-info me-1">
                                            <i class="bi bi-folder2-open"></i> View Materials
                                        </a>
                                        <a href="<?= base_url('admin/course/' . $courseId . '/upload') ?>" 
                                           class="btn btn-sm btn-success">
                                            <i class="bi bi-upload"></i> Upload
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted mb-0">No courses found.</p>
                <?php endif; ?>
            </div>
        </div>

    <!-- ==================== TEACHER DASHBOARD ==================== -->
    <?php elseif ($user_role === 'teacher'): ?>
        <div class="card mt-4">
            <div class="card-header fw-bold bg-primary text-white">My Courses & Enrolled Students</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course Title</th>
                                <th>Students Enrolled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $db = \Config\Database::connect();
                            foreach ($courses as $index => $c): 
                                $courseId = $c['id'];
                                $students = $db->table('enrollments')
                                    ->select('users.name')
                                    ->join('users', 'users.id = enrollments.user_id')
                                    ->where('enrollments.course_id', $courseId)
                                    ->get()->getResultArray();
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($c['title'] ?? 'Unnamed Course') ?></td>
                                    <td><?= count($students) ?></td>
                                    <td>
                                        <a href="<?= base_url('materials/view/' . $courseId) ?>" 
                                           class="btn btn-sm btn-info me-1">
                                            <i class="bi bi-folder2-open"></i> View Materials
                                        </a>
                                        <a href="<?= base_url('admin/course/' . $courseId . '/upload') ?>" 
                                           class="btn btn-sm btn-success">
                                            <i class="bi bi-upload"></i> Upload
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted mb-0">No courses found.</p>
                <?php endif; ?>
            </div>
        </div>

    <!-- ==================== STUDENT DASHBOARD ==================== -->
    <?php elseif ($user_role === 'student'): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">My Enrolled Courses</div>
            <ul class="list-group list-group-flush" id="enrolledCourses">
                <?php if (!empty($enrolledCourses)): ?>
                    <?php foreach ($enrolledCourses as $course): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= esc($course['title']) ?></span>
                            <a href="<?= base_url('materials/view/' . $course['id']) ?>" 
                               class="btn btn-sm btn-info">
                                <i class="bi bi-folder2-open"></i> View Materials
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-muted no-enrollment-msg">
                        You are not enrolled in any course yet.
                    </li>
                <?php endif; ?>
            </ul>
        </div>


        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Available Courses</div>
            <ul class="list-group list-group-flush">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="course-title"><?= esc($course['title']) ?></span>
                            <button 
                                class="btn btn-sm btn-success enroll-btn" 
                                data-course-id="<?= esc($course['id']) ?>">
                                Enroll
                            </button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-muted">No available courses.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div id="alertBox" class="alert mt-3 d-none"></div>
    <?php endif; ?>

    <!-- ==================== ANNOUNCEMENTS SECTION ==================== -->
    <div class="card mt-4">
        <div class="card-header fw-bold bg-info text-white">
            <i class="fas fa-bullhorn me-2"></i>Latest Announcements
        </div>
        <div class="card-body">
            <?php if (empty($announcements)): ?>
                <div class="alert alert-info">
                    <h5>No announcements available</h5>
                    <p class="mb-0">Check back later for updates.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                    <td><?= nl2br(esc($announcement['content'])) ?></td>
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

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- ✅ jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ Enroll AJAX -->
<script>
$(document).ready(function() {
    $('.enroll-btn').click(function() {
        const courseId = $(this).data('course-id');
        const button = $(this);
        const courseTitle = button.closest('li').find('.course-title').text().trim();

        $.ajax({
            url: "<?= base_url('dashboard') ?>",
            type: "POST",
            data: {
                course_id: courseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                const alertBox = $('#alertBox');
                if (response.status === 'success') {
                    alertBox.removeClass('d-none alert-danger')
                            .addClass('alert alert-success')
                            .text(response.message);

                    button.prop('disabled', true).text('Enrolled');
                    $('.no-enrollment-msg').remove();
                    $('#enrolledCourses').append('<li class="list-group-item">' + courseTitle + '</li>');
                } else {
                    alertBox.removeClass('d-none alert-success')
                            .addClass('alert alert-danger')
                            .text(response.message);
                }
            },
            error: function(xhr) {
                $('#alertBox').removeClass('d-none alert-success')
                              .addClass('alert alert-danger')
                              .text('An error occurred. Please try again.');
                console.error(xhr.responseText);
            }
        });
    });
});
</script>

<?= $this->include('templates/footer') ?>
