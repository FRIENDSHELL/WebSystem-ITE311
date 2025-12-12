<?= $this->include('templates/header', ['title' => $title ?? 'Create New Course']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Create New Course</h2>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/courses') ?>">My Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Course</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Course Information</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($validation)): ?>
                        <div class="alert alert-danger">
                            <h6>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                <?php foreach ($validation->getErrors() as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('teacher/store-course') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Course ID -->
                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label">Course ID <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('course_id') ? 'is-invalid' : '' ?>" 
                                       id="course_id" 
                                       name="course_id" 
                                       placeholder="e.g., CS101, MATH201"
                                       value="<?= old('course_id') ?>" 
                                       required>
                                <div class="form-text">Unique identifier for the course</div>
                            </div>

                            <!-- Course Name -->
                            <div class="col-md-6 mb-3">
                                <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('course_name') ? 'is-invalid' : '' ?>" 
                                       id="course_name" 
                                       name="course_name" 
                                       placeholder="e.g., Introduction to Programming"
                                       value="<?= old('course_name') ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- School Year -->
                            <div class="col-md-4 mb-3">
                                <label for="school_year" class="form-label">School Year <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($validation) && $validation->hasError('school_year') ? 'is-invalid' : '' ?>" 
                                        id="school_year" 
                                        name="school_year" 
                                        required>
                                    <option value="">Select School Year</option>
                                    <?php if (!empty($school_years)): ?>
                                        <?php foreach ($school_years as $year): ?>
                                            <option value="<?= esc($year['year']) ?>" <?= old('school_year') == $year['year'] ? 'selected' : '' ?>>
                                                <?= esc($year['year']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Fallback options if no school years in database -->
                                        <option value="2024-2025" <?= old('school_year') == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                                        <option value="2025-2026" <?= old('school_year') == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4 mb-3">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($validation) && $validation->hasError('semester') ? 'is-invalid' : '' ?>" 
                                        id="semester" 
                                        name="semester" 
                                        required>
                                    <option value="">Select Semester</option>
                                    <option value="First Semester" <?= old('semester') == 'First Semester' ? 'selected' : '' ?>>First Semester</option>
                                    <option value="Second Semester" <?= old('semester') == 'Second Semester' ? 'selected' : '' ?>>Second Semester</option>
                                    <option value="Summer" <?= old('semester') == 'Summer' ? 'selected' : '' ?>>Summer</option>
                                </select>
                            </div>

                            <!-- Term -->
                            <div class="col-md-4 mb-3">
                                <label for="term" class="form-label">Term <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($validation) && $validation->hasError('term') ? 'is-invalid' : '' ?>" 
                                        id="term" 
                                        name="term" 
                                        required>
                                    <option value="">Select Term</option>
                                    <option value="1st Term" <?= old('term') == '1st Term' ? 'selected' : '' ?>>1st Term</option>
                                    <option value="2nd Term" <?= old('term') == '2nd Term' ? 'selected' : '' ?>>2nd Term</option>
                                </select>
                            </div>
                        </div>

                        <!-- Class Schedule -->
                        <div class="mb-3">
                            <label class="form-label">Class Schedule <span class="text-danger">*</span></label>
                            <div class="form-text mb-2">Select the days when this course will be held (multiple selection allowed)</div>
                            <div class="row">
                                <?php 
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                $oldSchedule = old('class_schedule') ? old('class_schedule') : [];
                                ?>
                                <?php foreach ($days as $day): ?>
                                    <div class="col-md-3 col-sm-4 col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="class_schedule[]" 
                                                   value="<?= $day ?>" 
                                                   id="day_<?= strtolower($day) ?>"
                                                   <?= in_array($day, $oldSchedule) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="day_<?= strtolower($day) ?>">
                                                <?= $day ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Time Start -->
                            <div class="col-md-6 mb-3">
                                <label for="time_start" class="form-label">Time Start <span class="text-danger">*</span></label>
                                <input type="time" 
                                       class="form-control <?= isset($validation) && $validation->hasError('time_start') ? 'is-invalid' : '' ?>" 
                                       id="time_start" 
                                       name="time_start" 
                                       value="<?= old('time_start') ?>" 
                                       required>
                            </div>

                            <!-- Time End -->
                            <div class="col-md-6 mb-3">
                                <label for="time_end" class="form-label">Time End <span class="text-danger">*</span></label>
                                <input type="time" 
                                       class="form-control <?= isset($validation) && $validation->hasError('time_end') ? 'is-invalid' : '' ?>" 
                                       id="time_end" 
                                       name="time_end" 
                                       value="<?= old('time_end') ?>" 
                                       required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Course Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Enter a brief description of the course..."><?= old('description') ?></textarea>
                            <div class="form-text">Optional: Provide additional details about the course content and objectives</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('teacher/courses') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Courses
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Time validation
document.addEventListener('DOMContentLoaded', function() {
    const timeStart = document.getElementById('time_start');
    const timeEnd = document.getElementById('time_end');
    
    function validateTime() {
        if (timeStart.value && timeEnd.value) {
            if (timeStart.value >= timeEnd.value) {
                timeEnd.setCustomValidity('End time must be after start time');
            } else {
                timeEnd.setCustomValidity('');
            }
        }
    }
    
    timeStart.addEventListener('change', validateTime);
    timeEnd.addEventListener('change', validateTime);
});
</script>

<?= $this->include('templates/footer') ?>