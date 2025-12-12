<?= $this->include('templates/header', ['title' => $title ?? 'My Courses']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold mb-0">My Courses</h2>
                <a href="<?= site_url('teacher/create-course') ?>" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Create New Course
                </a>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Courses</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="<?= site_url('teacher/courses') ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label for="search" class="form-label">Search Courses</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Search by course name, course ID, or description..."
                                       value="<?= esc($search ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <?php if (!empty($search)): ?>
                                    <a href="<?= site_url('teacher/courses') ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-book"></i> 
                        <?php if (!empty($search)): ?>
                            Search Results for "<?= esc($search) ?>"
                        <?php else: ?>
                            All My Courses
                        <?php endif; ?>
                        <span class="badge bg-primary ms-2"><?= count($courses) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($courses)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-book display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">
                                <?php if (!empty($search)): ?>
                                    No courses found matching your search.
                                <?php else: ?>
                                    No courses created yet.
                                <?php endif; ?>
                            </h4>
                            <p class="text-muted">
                                <?php if (!empty($search)): ?>
                                    Try adjusting your search terms or <a href="<?= site_url('teacher/courses') ?>">view all courses</a>.
                                <?php else: ?>
                                    Get started by creating your first course.
                                <?php endif; ?>
                            </p>
                            <?php if (empty($search)): ?>
                                <a href="<?= site_url('teacher/create-course') ?>" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Create Your First Course
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course ID</th>
                                        <th>Course Name</th>
                                        <th>School Year</th>
                                        <th>Semester</th>
                                        <th>Term</th>
                                        <th>Schedule</th>
                                        <th>Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($courses as $course): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary"><?= esc($course['course_id']) ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= esc($course['course_name']) ?></strong>
                                                    <?php if (!empty($course['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($course['description'], 0, 50)) ?><?= strlen($course['description']) > 50 ? '...' : '' ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?= esc($course['school_year']) ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= esc($course['semester']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= esc($course['term']) ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($course['class_schedule'])): ?>
                                                    <small><?= esc(str_replace(',', ', ', $course['class_schedule'])) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Not set</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($course['time_start']) && !empty($course['time_end'])): ?>
                                                    <small>
                                                        <?= date('g:i A', strtotime($course['time_start'])) ?> - 
                                                        <?= date('g:i A', strtotime($course['time_end'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">Not set</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= site_url('teacher/edit-course/' . $course['id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit Course">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="<?= site_url('teacher/delete-course/' . $course['id']) ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       title="Delete Course"
                                                       onclick="return confirm('Are you sure you want to delete this course?')">
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

<?= $this->include('templates/footer') ?>