<?= $this->include('templates/header') ?>

<?php
// ----------------------------
// Dashboard View (CI4)
// Controller SHOULD provide:
//   $user_role, $user_name
//   $users (for admin), $courses (admin/teacher), $enrolledCourses, $announcements
// DO NOT run DB queries inside the view — controller should prepare everything.
// ----------------------------
?>

<!-- CSRF token for AJAX (preferred) -->
<meta name="csrf-token-name" content="<?= csrf_token() ?>">
<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="container mt-4">
    <h2 class="text-center fw-bold mb-4">Dashboard</h2>

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success text-center"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger text-center"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <!-- Welcome Card -->
    <div class="card shadow-lg mx-auto mb-4" style="max-width:700px;border-radius:15px;">
        <div class="card-body text-center p-5">
            <h3 class="card-title mb-2">
                Welcome, <span class="text-primary text-capitalize"><?= esc($user_name ?? 'User') ?></span> 🎉
            </h3>
            <p class="text-muted mb-2">Role: <strong class="text-capitalize"><?= esc($user_role ?? '') ?></strong></p>
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm mt-2">Logout</a>
        </div>
    </div>

    <!-- ================= ADMIN ================= -->
    <?php if ($user_role === 'admin'): ?>
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white fw-bold">System Overview</div>
                    <div class="card-body">
                        <?php if (!empty($users)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
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
                                                <td><strong><?= esc(ucfirst($u['role'])) ?></strong></td>
                                                <td>
                                                    <?php if (session()->get('id') != $u['id']): ?>
                                                        <form method="post" action="<?= base_url('auth/updateRole/' . $u['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                                <option value="teacher" <?= isset($u['role']) && $u['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                                                <option value="student" <?= isset($u['role']) && $u['role'] === 'student' ? 'selected' : '' ?>>Student</option>
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
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No users found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Courses & Enrollments -->
            <div class="col-12">
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white fw-bold">Courses & Enrollments</div>
                    <div class="card-body">
                        <?php if (!empty($courses)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle mb-0">
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
                                        <?php foreach ($courses as $index => $c): 
                                            // Controller should provide 'students' as array of names OR 'students_count'
                                            $studentNames = isset($c['students']) && is_array($c['students']) ? $c['students'] : [];
                                        ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($c['title'] ?? 'Unnamed Course') ?></td>
                                                <td><?= isset($c['students_count']) ? (int)$c['students_count'] : count($studentNames) ?></td>
                                                <td><?= esc(!empty($studentNames) ? implode(', ', $studentNames) : '<span class="text-muted">None</span>') ?></td>
                                                <td>
                                                    <a href="<?= base_url('materials/view/' . ($c['id'] ?? '')) ?>" class="btn btn-sm btn-info me-1">
                                                        <i class="bi bi-folder2-open"></i> View Materials
                                                    </a>
                                                    <a href="<?= base_url('admin/course/' . ($c['id'] ?? '') . '/upload') ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-upload"></i> Upload
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No courses found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <!-- ================= TEACHER ================= -->
    <?php elseif ($user_role === 'teacher'): ?>
        <div class="card mt-4">
            <div class="card-header bg-primary text-white fw-bold">My Courses & Enrolled Students</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Course Title</th>
                                    <th>Students Enrolled</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $index => $c): 
                                    $students = $c['students'] ?? [];
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($c['title'] ?? 'Unnamed Course') ?></td>
                                        <td><?= isset($c['students_count']) ? (int)$c['students_count'] : count($students) ?></td>
                                        <td>
                                            <a href="<?= base_url('materials/view/' . ($c['id'] ?? '')) ?>" class="btn btn-sm btn-info me-1">
                                                <i class="bi bi-folder2-open"></i> View Materials
                                            </a>
                                            <a href="<?= base_url('admin/course/' . ($c['id'] ?? '') . '/upload') ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-upload"></i> Upload
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No courses found.</p>
                <?php endif; ?>
            </div>
        </div>

    <!-- ================= STUDENT ================= -->
    <?php else: // student or other roles ?>
        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span>My Enrolled Courses</span>
                        <span id="notificationBadge" class="badge bg-danger" style="display:none;">0</span>
                    </div>

                    <ul class="list-group list-group-flush" id="enrolledCourses">
                        <?php if (!empty($enrolledCourses)): ?>
                            <?php foreach ($enrolledCourses as $course): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= esc($course['title']) ?></span>
                                    <a href="<?= base_url('materials/view/' . $course['id']) ?>" class="btn btn-sm btn-info">
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

                    <div id="notificationList" class="p-3"></div>
                </div>
            </div>

            <!-- Available Courses -->
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Available Courses</div>
                    <ul class="list-group list-group-flush" id="availableCourses">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="course-title"><?= esc($course['title']) ?></span>
                                    <button class="btn btn-sm btn-success enroll-btn" data-course-id="<?= esc($course['id']) ?>">
                                        Enroll
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted">No available courses.</li>
                        <?php endif; ?>
                    </ul>

                    <div id="alertBox" class="alert mt-3 d-none" role="alert"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Announcements (visible for all) -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white fw-bold">
            <i class="fas fa-bullhorn me-2"></i>Latest Announcements
        </div>
        <div class="card-body">
            <?php if (empty($announcements)): ?>
                <div class="alert alert-info mb-0">
                    <h5>No announcements available</h5>
                    <p class="mb-0">Check back later for updates.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
                                            <?= !empty($announcement['created_at']) ? date('M j, Y, g:i A', strtotime($announcement['created_at'])) : '—' ?>
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

<!-- Styles / Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- jQuery (only once) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
(function() {
    // ----------------------------
    // Common config
    // ----------------------------
    const csrfName = $('meta[name="csrf-token-name"]').attr('content') || '<?= csrf_token() ?>';
    let csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    // Utility to attach CSRF to every jQuery AJAX request
    $.ajaxSetup({
        beforeSend: function (jqXHR, settings) {
            if (settings.type && settings.type.toUpperCase() === 'POST') {
                // inject CSRF token into data if not present
                if (typeof settings.data === 'string') {
                    if (settings.data.indexOf(csrfName + '=') === -1) {
                        settings.data += (settings.data ? '&' : '') + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
                    }
                } else if (typeof settings.data === 'object' || !settings.data) {
                    settings.data = settings.data || {};
                    if (!settings.data[csrfName]) {
                        settings.data[csrfName] = csrfHash;
                    }
                }
            }
        },
        dataType: 'json'
    });

    // ----------------------------
    // Notifications
    // Endpoint assumed: GET /notifications  -> { unread: n, list: [{id, message}] }
    // POST /notifications/mark_read/{id}
    // ----------------------------
    function renderNotifications(data) {
        if (!data) return;
        if (data.unread && data.unread > 0) {
            $('#notificationBadge').show().text(data.unread);
        } else {
            $('#notificationBadge').hide();
        }

        let html = '';
        (data.list || []).forEach(function(n) {
            html += `
                <div class="alert alert-info d-flex justify-content-between align-items-center mb-2">
                    <div class="me-2 small">${n.message}</div>
                    <div>
                        <button data-id="${n.id}" class="btn btn-sm btn-secondary mark-read-btn">Mark as Read</button>
                    </div>
                </div>
            `;
        });
        $('#notificationList').html(html);
    }

    function loadNotifications() {
        $.get('<?= base_url("notifications") ?>')
            .done(function(data) {
                renderNotifications(data);
            })
            .fail(function() {
                // silent fail — optional console message
                // console.warn('Failed to load notifications');
            });
    }

    // delegate mark-read clicks
    $(document).on('click', '.mark-read-btn', function() {
        const id = $(this).data('id');
        if (!id) return;
        $.post('<?= base_url("notifications/mark_read") ?>/' + id)
            .done(function() {
                loadNotifications();
            })
            .fail(function() {
                // handle error silently or notify user
            });
    });

    // ----------------------------
    // Enroll in course (student)
    // Endpoint: POST <?= base_url('dashboard/enroll') ?>
    // Expects JSON response: { status: 'success'|'error', message: '...' }
    // ----------------------------
    $(document).on('click', '.enroll-btn', function() {
        const button = $(this);
        const courseId = button.data('course-id');
        const courseTitle = button.closest('li').find('.course-title').text().trim();
        if (!courseId) return;

        button.prop('disabled', true).text('Processing...');

        $.ajax({
            url: "<?= base_url('dashboard/enroll') ?>",
            type: "POST",
            data: { course_id: courseId }, // CSRF injected automatically by ajaxSetup
        })
        .done(function(response) {
            const alertBox = $('#alertBox');
            if (response && response.status === 'success') {
                alertBox.removeClass('d-none alert-danger')
                        .addClass('alert alert-success')
                        .text(response.message || 'Enrolled successfully.');

                // disable button & update UI
                button.prop('disabled', true).text('Enrolled');
                $('.no-enrollment-msg').remove();
                $('#enrolledCourses').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${$('<div>').text(courseTitle).html()}</span>
                        <a href="<?= base_url('materials/view/') ?>${courseId}" class="btn btn-sm btn-info">
                            <i class="bi bi-folder2-open"></i> View Materials
                        </a>
                    </li>
                `);
            } else {
                alertBox.removeClass('d-none alert-success')
                        .addClass('alert alert-danger')
                        .text((response && response.message) ? response.message : 'Failed to enroll.');
                button.prop('disabled', false).text('Enroll');
            }

            // Update CSRF token if server returned a new one (CI might)
            if (response && response.csrf_hash) {
                csrfHash = response.csrf_hash;
                $('meta[name="csrf-token"]').attr('content', csrfHash);
            }
        })
        .fail(function(xhr) {
            $('#alertBox').removeClass('d-none alert-success')
                          .addClass('alert alert-danger')
                          .text('An error occurred. Please try again.');
            console.error(xhr.responseText || xhr.statusText);
            button.prop('disabled', false).text('Enroll');
        });
    });

    // ----------------------------
    // Init
    // ----------------------------
    $(document).ready(function() {
        loadNotifications();
        // Refresh notifications every 60s
        setInterval(loadNotifications, 60000);
    });
})();
</script>

<?= $this->include('templates/footer') ?>
