<?= $this->include('templates/header', ['title' => $title ?? 'Edit Course']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Edit Course</h2>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('teacher/courses') ?>">My Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Course</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Course Information</h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('teacher/update-course/' . $course['id']) ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Course ID -->
                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label">Course ID <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="course_id" 
                                       name="course_id" 
                                       placeholder="e.g., CS101, MATH201"
                                       value="<?= esc($course['course_id']) ?>" 
                                       required>
                                <div class="form-text">Unique identifier for the course</div>
                            </div>

                            <!-- Course Name -->
                            <div class="col-md-6 mb-3">
                                <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="course_name" 
                                       name="course_name" 
                                       placeholder="e.g., Introduction to Programming"
                                       value="<?= esc($course['course_name']) ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- School Year -->
                            <div class="col-md-4 mb-3">
                                <label for="school_year" class="form-label">School Year <span class="text-danger">*</span></label>
                                <select class="form-select" id="school_year" name="school_year" required>
                                    <option value="">Select School Year</option>
                                    <?php if (!empty($school_years)): ?>
                                        <?php foreach ($school_years as $year): ?>
                                            <option value="<?= esc($year['year']) ?>" <?= $course['school_year'] == $year['year'] ? 'selected' : '' ?>>
                                                <?= esc($year['year']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Fallback options if no school years in database -->
                                        <option value="2024-2025" <?= $course['school_year'] == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                                        <option value="2025-2026" <?= $course['school_year'] == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4 mb-3">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select" id="semester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    <option value="First Semester" <?= $course['semester'] == 'First Semester' ? 'selected' : '' ?>>First Semester</option>
                                    <option value="Second Semester" <?= $course['semester'] == 'Second Semester' ? 'selected' : '' ?>>Second Semester</option>
                                    <option value="Summer" <?= $course['semester'] == 'Summer' ? 'selected' : '' ?>>Summer</option>
                                </select>
                            </div>

                            <!-- Term -->
                            <div class="col-md-4 mb-3">
                                <label for="term" class="form-label">Term <span class="text-danger">*</span></label>
                                <select class="form-select" id="term" name="term" required>
                                    <option value="">Select Term</option>
                                    <option value="1st Term" <?= $course['term'] == '1st Term' ? 'selected' : '' ?>>1st Term</option>
                                    <option value="2nd Term" <?= $course['term'] == '2nd Term' ? 'selected' : '' ?>>2nd Term</option>
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
                                $currentSchedule = !empty($course['class_schedule']) ? explode(',', $course['class_schedule']) : [];
                                ?>
                                <?php foreach ($days as $day): ?>
                                    <div class="col-md-3 col-sm-4 col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="class_schedule[]" 
                                                   value="<?= $day ?>" 
                                                   id="day_<?= strtolower($day) ?>"
                                                   <?= in_array($day, $currentSchedule) ? 'checked' : '' ?>>
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
                                       class="form-control" 
                                       id="time_start" 
                                       name="time_start" 
                                       value="<?= esc($course['time_start']) ?>" 
                                       required>
                            </div>

                            <!-- Time End -->
                            <div class="col-md-6 mb-3">
                                <label for="time_end" class="form-label">Time End <span class="text-danger">*</span></label>
                                <input type="time" 
                                       class="form-control" 
                                       id="time_end" 
                                       name="time_end" 
                                       value="<?= esc($course['time_end']) ?>" 
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
                                      placeholder="Enter a brief description of the course..."><?= esc($course['description']) ?></textarea>
                            <div class="form-text">Optional: Provide additional details about the course content and objectives</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('teacher/courses') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Courses
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle"></i> Update Course
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