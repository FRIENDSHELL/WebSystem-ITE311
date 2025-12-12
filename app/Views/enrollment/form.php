<?= $this->include('templates/header', ['title' => $title ?? 'Student Enrollment Form']) ?>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0 text-center">
                        <i class="bi bi-person-plus"></i> Student Enrollment Form
                    </h3>
                </div>
                <div class="card-body p-4">
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
                            <h6>Please correct the following errors:</h6>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('enrollment/submit') ?>" method="post" id="enrollmentForm">
                        <?= csrf_field() ?>

                        <!-- Course Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="bi bi-book"></i> Course Information
                                </h5>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Select Course *</label>
                                    <select class="form-select" id="course_id" name="course_id" required onchange="showCourseInfo()">
                                        <option value="">Choose a course...</option>
                                        <?php foreach($courses as $course): ?>
                                            <option value="<?= $course['id'] ?>" 
                                                    data-teacher="<?= esc($course['teacher_name'] ?? 'Not assigned') ?>"
                                                    data-code="<?= esc($course['course_code'] ?? '') ?>"
                                                    data-title="<?= esc($course['title'] ?? $course['course_name']) ?>"
                                                    data-semester="<?= esc($course['semester_name'] ?? '') ?>"
                                                    data-term="<?= esc($course['term_name'] ?? '') ?>"
                                                    data-year="<?= esc($course['school_year'] ?? '') ?>"
                                                    data-description="<?= esc($course['description'] ?? '') ?>"
                                                    <?= old('course_id') == $course['id'] ? 'selected' : '' ?>>
                                                <?= esc($course['course_code'] ? $course['course_code'] . ' - ' : '') ?>
                                                <?= esc($course['title'] ?? $course['course_name']) ?>
                                                <?php if (!empty($course['teacher_name'])): ?>
                                                    - Teacher: <?= esc($course['teacher_name']) ?>
                                                <?php endif; ?>
                                                <?php if ($course['semester_name'] || $course['term_name'] || $course['school_year']): ?>
                                                    (<?= implode(' | ', array_filter([
                                                        $course['semester_name'],
                                                        $course['term_name'],
                                                        $course['school_year']
                                                    ])) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Course Information Display -->
                                <div id="courseInfo" class="card bg-light" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="bi bi-info-circle"></i> Course Information
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Course:</strong> <span id="courseTitle">-</span></p>
                                                <p class="mb-2"><strong>Course Code:</strong> <span id="courseCode">-</span></p>
                                                <p class="mb-2"><strong>Instructor:</strong> <span id="courseTeacher" class="text-success fw-bold">-</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Semester:</strong> <span id="courseSemester">-</span></p>
                                                <p class="mb-2"><strong>Term:</strong> <span id="courseTerm">-</span></p>
                                                <p class="mb-2"><strong>School Year:</strong> <span id="courseYear">-</span></p>
                                            </div>
                                        </div>
                                        <div id="courseDescription" class="mt-2" style="display: none;">
                                            <p class="mb-0"><strong>Description:</strong> <span id="courseDescText">-</span></p>
                                        </div>
                                        <div class="alert alert-info mt-3 mb-0">
                                            <i class="bi bi-lightbulb"></i>
                                            <strong>Note:</strong> Your enrollment application will be sent to <strong><span id="teacherNameNote">this teacher</span></strong> for approval. 
                                            You will be notified once the teacher reviews your application.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="bi bi-person"></i> Personal Information
                                </h5>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student ID *</label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" 
                                           value="<?= old('student_id') ?>" required placeholder="e.g., 2025-001234">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?= old('first_name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?= old('last_name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                           value="<?= old('middle_name') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date *</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                           value="<?= old('birth_date') ?>" required onchange="calculateAge()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="age" class="form-label">Age *</label>
                                    <input type="number" class="form-control" id="age" name="age" 
                                           value="<?= old('age') ?>" required min="16" max="99" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?= old('gender') == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= old('gender') == 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= old('gender') == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_number" class="form-label">Contact Number *</label>
                                    <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                           value="<?= old('contact_number') ?>" required placeholder="e.g., +63 912 345 6789">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_address" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email_address" name="email_address" 
                                           value="<?= old('email_address') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Complete Address *</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?= old('address') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="bi bi-people"></i> Guardian Information
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guardian_name" class="form-label">Guardian/Parent Name *</label>
                                    <input type="text" class="form-control" id="guardian_name" name="guardian_name" 
                                           value="<?= old('guardian_name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guardian_contact" class="form-label">Guardian Contact Number *</label>
                                    <input type="tel" class="form-control" id="guardian_contact" name="guardian_contact" 
                                           value="<?= old('guardian_contact') ?>" required placeholder="e.g., +63 912 345 6789">
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="bi bi-mortarboard"></i> Academic Information
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year_level" class="form-label">Year Level *</label>
                                    <select class="form-select" id="year_level" name="year_level" required>
                                        <option value="">Select Year Level</option>
                                        <option value="1st Year" <?= old('year_level') == '1st Year' ? 'selected' : '' ?>>1st Year</option>
                                        <option value="2nd Year" <?= old('year_level') == '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
                                        <option value="3rd Year" <?= old('year_level') == '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
                                        <option value="4th Year" <?= old('year_level') == '4th Year' ? 'selected' : '' ?>>4th Year</option>
                                        <option value="5th Year" <?= old('year_level') == '5th Year' ? 'selected' : '' ?>>5th Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program" class="form-label">Program/Degree *</label>
                                    <input type="text" class="form-control" id="program" name="program" 
                                           value="<?= old('program') ?>" required 
                                           placeholder="e.g., Bachelor of Science in Information Technology">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-circle"></i> Submit Enrollment Application
                                </button>
                                <a href="<?= site_url('/') ?>" class="btn btn-secondary btn-lg px-5 ms-3">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function calculateAge() {
    const birthDate = document.getElementById('birth_date').value;
    if (birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        document.getElementById('age').value = age;
    }
}

function showCourseInfo() {
    const select = document.getElementById('course_id');
    const courseInfo = document.getElementById('courseInfo');
    
    if (select.value) {
        const option = select.options[select.selectedIndex];
        
        // Update course information
        document.getElementById('courseTitle').textContent = option.dataset.title || '-';
        document.getElementById('courseCode').textContent = option.dataset.code || '-';
        document.getElementById('courseTeacher').textContent = option.dataset.teacher || 'Not assigned';
        document.getElementById('courseSemester').textContent = option.dataset.semester || '-';
        document.getElementById('courseTerm').textContent = option.dataset.term || '-';
        document.getElementById('courseYear').textContent = option.dataset.year || '-';
        document.getElementById('teacherNameNote').textContent = option.dataset.teacher || 'the assigned teacher';
        
        // Show/hide description
        const description = option.dataset.description;
        if (description && description.trim() !== '') {
            document.getElementById('courseDescText').textContent = description;
            document.getElementById('courseDescription').style.display = 'block';
        } else {
            document.getElementById('courseDescription').style.display = 'none';
        }
        
        courseInfo.style.display = 'block';
    } else {
        courseInfo.style.display = 'none';
    }
}

// Form validation
document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
    const age = parseInt(document.getElementById('age').value);
    if (age < 16 || age > 99) {
        e.preventDefault();
        alert('Age must be between 16 and 99 years old.');
        return false;
    }
    
    // Show confirmation with teacher information
    const select = document.getElementById('course_id');
    if (select.value) {
        const option = select.options[select.selectedIndex];
        const teacherName = option.dataset.teacher || 'the assigned teacher';
        const courseName = option.dataset.title || 'the selected course';
        
        const confirmed = confirm(
            `Are you sure you want to submit your enrollment application?\n\n` +
            `Course: ${courseName}\n` +
            `Teacher: ${teacherName}\n\n` +
            `Your application will be sent to ${teacherName} for approval.`
        );
        
        if (!confirmed) {
            e.preventDefault();
            return false;
        }
    }
});

// Show course info if there's a pre-selected course (for form validation errors)
document.addEventListener('DOMContentLoaded', function() {
    showCourseInfo();
});
</script>

<?= $this->include('templates/footer') ?>