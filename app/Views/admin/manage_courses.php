<?= $this->include('templates/header', ['title' => $title ?? 'Manage Courses']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Manage Courses</h2>
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

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Course Button -->
    <div class="row mb-4">
        <div class="col-12">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="bi bi-plus-circle"></i> Add New Course
            </button>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-book"></i> All Courses</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($courses)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No courses found. Add your first course using the button above.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Title</th>
                                        <th>Credits</th>
                                        <th>Teacher</th>
                                        <th>Semester</th>
                                        <th>Term</th>
                                        <th>School Year</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($courses as $course): ?>
                                        <tr>
                                            <td><strong><?= esc($course['course_code'] ?? 'N/A') ?></strong></td>
                                            <td><?= esc($course['title']) ?></td>
                                            <td><?= esc($course['credits']) ?></td>
                                            <td><?= esc($course['teacher_name'] ?? 'Unassigned') ?></td>
                                            <td><?= esc($course['semester_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($course['term_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($course['school_year'] ?? 'N/A') ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editCourseModal<?= $course['id'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?= site_url('admin/courses/delete/' . $course['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to delete this course?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
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

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/courses/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Course Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="course_code" class="form-label">Course Code</label>
                                <input type="text" class="form-control" id="course_code" name="course_code">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="credits" class="form-label">Credits</label>
                                <input type="number" class="form-control" id="credits" name="credits" value="3" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Assign Teacher *</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select Teacher</option>
                                    <?php foreach($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>"><?= esc($teacher['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="semester_id" class="form-label">Semester</label>
                                <select class="form-select" id="semester_id" name="semester_id">
                                    <option value="">Select Semester</option>
                                    <?php foreach($semesters as $semester): ?>
                                        <option value="<?= $semester['id'] ?>"><?= esc($semester['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="term_id" class="form-label">Term</label>
                                <select class="form-select" id="term_id" name="term_id">
                                    <option value="">Select Term</option>
                                    <?php foreach($terms as $term): ?>
                                        <option value="<?= $term['id'] ?>"><?= esc($term['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="school_year_id" class="form-label">School Year</label>
                                <select class="form-select" id="school_year_id" name="school_year_id">
                                    <option value="">Select School Year</option>
                                    <?php foreach($schoolYears as $schoolYear): ?>
                                        <option value="<?= $schoolYear['id'] ?>"><?= esc($schoolYear['year']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Course Modals -->
<?php foreach($courses as $course): ?>
<div class="modal fade" id="editCourseModal<?= $course['id'] ?>" tabindex="-1" aria-labelledby="editCourseModalLabel<?= $course['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel<?= $course['id'] ?>">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/courses/update/' . $course['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title<?= $course['id'] ?>" class="form-label">Course Title *</label>
                                <input type="text" class="form-control" id="title<?= $course['id'] ?>" name="title" value="<?= esc($course['title']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="course_code<?= $course['id'] ?>" class="form-label">Course Code</label>
                                <input type="text" class="form-control" id="course_code<?= $course['id'] ?>" name="course_code" value="<?= esc($course['course_code']) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="credits<?= $course['id'] ?>" class="form-label">Credits</label>
                                <input type="number" class="form-control" id="credits<?= $course['id'] ?>" name="credits" value="<?= esc($course['credits']) ?>" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id<?= $course['id'] ?>" class="form-label">Assign Teacher *</label>
                                <select class="form-select" id="user_id<?= $course['id'] ?>" name="user_id" required>
                                    <option value="">Select Teacher</option>
                                    <?php foreach($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>" <?= $teacher['id'] == $course['user_id'] ? 'selected' : '' ?>>
                                            <?= esc($teacher['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="semester_id<?= $course['id'] ?>" class="form-label">Semester</label>
                                <select class="form-select" id="semester_id<?= $course['id'] ?>" name="semester_id">
                                    <option value="">Select Semester</option>
                                    <?php foreach($semesters as $semester): ?>
                                        <option value="<?= $semester['id'] ?>" <?= $semester['id'] == $course['semester_id'] ? 'selected' : '' ?>>
                                            <?= esc($semester['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="term_id<?= $course['id'] ?>" class="form-label">Term</label>
                                <select class="form-select" id="term_id<?= $course['id'] ?>" name="term_id">
                                    <option value="">Select Term</option>
                                    <?php foreach($terms as $term): ?>
                                        <option value="<?= $term['id'] ?>" <?= $term['id'] == $course['term_id'] ? 'selected' : '' ?>>
                                            <?= esc($term['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="school_year_id<?= $course['id'] ?>" class="form-label">School Year</label>
                                <select class="form-select" id="school_year_id<?= $course['id'] ?>" name="school_year_id">
                                    <option value="">Select School Year</option>
                                    <?php foreach($schoolYears as $schoolYear): ?>
                                        <option value="<?= $schoolYear['id'] ?>" <?= $schoolYear['id'] == $course['school_year_id'] ? 'selected' : '' ?>>
                                            <?= esc($schoolYear['year']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description<?= $course['id'] ?>" class="form-label">Description</label>
                        <textarea class="form-control" id="description<?= $course['id'] ?>" name="description" rows="3"><?= esc($course['description']) ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->include('templates/footer') ?>