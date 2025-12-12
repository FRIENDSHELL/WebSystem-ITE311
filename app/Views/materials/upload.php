<?= $this->include('templates/header', ['title' => $title ?? 'Upload Material']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('materials') ?>">Materials</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Upload</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-3">Upload Course Material</h2>
            
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

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Upload New Material</h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('materials/store') ?>" method="POST" enctype="multipart/form-data" id="uploadForm">
                        <?= csrf_field() ?>
                        
                        <!-- Course Selection -->
                        <div class="mb-3">
                            <label for="course_id" class="form-label">
                                <i class="bi bi-book"></i> Select Course <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">Choose a course...</option>
                                <?php if (empty($courses)): ?>
                                    <option value="" disabled>No courses available</option>
                                <?php else: ?>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= esc($course['id']) ?>" 
                                                <?= ($selected_course_id == $course['id']) ? 'selected' : '' ?>
                                                data-course-code="<?= esc($course['course_id'] ?? '') ?>"
                                                data-semester="<?= esc($course['semester_name'] ?? '') ?>"
                                                data-year="<?= esc($course['year_label'] ?? '') ?>"
                                                data-teacher="<?= esc($course['teacher_name'] ?? '') ?>">
                                            <?= esc($course['course_name']) ?>
                                            <?php if ($course['course_id']): ?>
                                                (<?= esc($course['course_id']) ?>)
                                            <?php endif; ?>
                                            <?php if ($course['semester_name'] || $course['year_label']): ?>
                                                - <?= esc($course['semester_name'] ?? '') ?> <?= esc($course['year_label'] ?? '') ?>
                                            <?php endif; ?>
                                            <?php if ($user_role === 'admin' && !empty($course['teacher_name'])): ?>
                                                - Teacher: <?= esc($course['teacher_name']) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Select the course this material belongs to.</div>
                            
                            <?php if (empty($courses)): ?>
                                <div class="alert alert-warning mt-3">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <strong>No courses available!</strong>
                                    <p class="mb-2 mt-2">You need to create a course first before uploading materials.</p>
                                    <?php if ($user_role === 'teacher'): ?>
                                        <a href="<?= site_url('teacher/create-course') ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle"></i> Create New Course
                                        </a>
                                    <?php elseif ($user_role === 'admin'): ?>
                                        <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle"></i> Manage Courses
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Course Information Display -->
                            <div id="courseInfo" class="mt-3" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2">
                                            <i class="bi bi-info-circle"></i> Course Information
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">Course Code:</small>
                                                <div id="displayCourseCode" class="fw-bold">-</div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Academic Period:</small>
                                                <div id="displayAcademicPeriod" class="fw-bold">-</div>
                                            </div>
                                            <?php if ($user_role === 'admin'): ?>
                                            <div class="col-md-6 mt-2">
                                                <small class="text-muted">Teacher:</small>
                                                <div id="displayTeacher" class="fw-bold">-</div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Material Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="bi bi-type"></i> Material Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title') ?>" required maxlength="255"
                                   placeholder="Enter a descriptive title for the material">
                            <div class="form-text">Provide a clear, descriptive title for the material.</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-text-paragraph"></i> Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      maxlength="1000" placeholder="Enter a description of the material (optional)"><?= old('description') ?></textarea>
                            <div class="form-text">Optional description to help students understand the material content.</div>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label for="material_file" class="form-label">
                                <i class="bi bi-file-earmark-arrow-up"></i> Select File <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" id="material_file" name="material_file" 
                                   required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov">
                            <div class="form-text">
                                <strong>Supported formats:</strong> PDF, Word, PowerPoint, Excel, Text, ZIP/RAR, Images (JPG, PNG, GIF), Videos (MP4, AVI, MOV)<br>
                                <strong>Maximum file size:</strong> 10 MB
                            </div>
                            
                            <!-- File Preview -->
                            <div id="filePreview" class="mt-3" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Selected File:</h6>
                                        <div id="fileInfo" class="d-flex align-items-center">
                                            <i id="fileIcon" class="bi fs-2 me-3"></i>
                                            <div>
                                                <div id="fileName" class="fw-bold"></div>
                                                <div id="fileSize" class="text-muted small"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mb-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if ($user_role === 'teacher'): ?>
                                    <a href="<?= site_url('teacher/create-course') ?>" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Create New Course
                                    </a>
                                    <a href="<?= site_url('teacher/courses') ?>" class="btn btn-outline-info">
                                        <i class="bi bi-book"></i> View My Courses
                                    </a>
                                <?php elseif ($user_role === 'admin'): ?>
                                    <a href="<?= site_url('admin/courses') ?>" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Manage Courses
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('materials') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-cloud-upload"></i> Upload Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Guidelines -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Upload Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Supported File Types:</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-file-earmark-pdf text-danger"></i> PDF Documents</li>
                                <li><i class="bi bi-file-earmark-word text-primary"></i> Word Documents (.doc, .docx)</li>
                                <li><i class="bi bi-file-earmark-ppt text-warning"></i> PowerPoint (.ppt, .pptx)</li>
                                <li><i class="bi bi-file-earmark-excel text-success"></i> Excel Spreadsheets (.xls, .xlsx)</li>
                                <li><i class="bi bi-file-earmark-zip text-secondary"></i> Compressed Files (.zip, .rar)</li>
                                <li><i class="bi bi-file-earmark-image text-info"></i> Images (.jpg, .png, .gif)</li>
                                <li><i class="bi bi-file-earmark-play text-purple"></i> Videos (.mp4, .avi, .mov)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Best Practices:</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> Use descriptive file names</li>
                                <li><i class="bi bi-check-circle text-success"></i> Keep file sizes under 10 MB</li>
                                <li><i class="bi bi-check-circle text-success"></i> Provide clear titles and descriptions</li>
                                <li><i class="bi bi-check-circle text-success"></i> Organize materials by course</li>
                                <li><i class="bi bi-check-circle text-success"></i> Use appropriate file formats</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('material_file');
    const filePreview = document.getElementById('filePreview');
    const fileIcon = document.getElementById('fileIcon');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('uploadForm');

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Show file preview
            filePreview.style.display = 'block';
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            
            // Set appropriate icon based on file type
            const extension = file.name.split('.').pop().toLowerCase();
            const iconClass = getFileIcon(extension);
            fileIcon.className = `bi ${iconClass} fs-2 me-3`;
            
            // Validate file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size exceeds 10MB limit. Please choose a smaller file.');
                fileInput.value = '';
                filePreview.style.display = 'none';
                return;
            }
        } else {
            filePreview.style.display = 'none';
        }
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';
        submitBtn.disabled = true;
    });

    // File size formatter
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Get file icon based on extension
    function getFileIcon(extension) {
        const iconMap = {
            'pdf': 'bi-file-earmark-pdf text-danger',
            'doc': 'bi-file-earmark-word text-primary',
            'docx': 'bi-file-earmark-word text-primary',
            'ppt': 'bi-file-earmark-ppt text-warning',
            'pptx': 'bi-file-earmark-ppt text-warning',
            'xls': 'bi-file-earmark-excel text-success',
            'xlsx': 'bi-file-earmark-excel text-success',
            'zip': 'bi-file-earmark-zip text-secondary',
            'rar': 'bi-file-earmark-zip text-secondary',
            'jpg': 'bi-file-earmark-image text-info',
            'jpeg': 'bi-file-earmark-image text-info',
            'png': 'bi-file-earmark-image text-info',
            'gif': 'bi-file-earmark-image text-info',
            'mp4': 'bi-file-earmark-play text-purple',
            'avi': 'bi-file-earmark-play text-purple',
            'mov': 'bi-file-earmark-play text-purple',
            'txt': 'bi-file-earmark-text text-muted'
        };
        
        return iconMap[extension] || 'bi-file-earmark text-muted';
    }
});
</script>

<?= $this->include('templates/footer') ?>