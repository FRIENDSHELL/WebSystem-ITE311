<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Back Button -->
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>

            <!-- Upload Form Card -->
            <div class="card shadow-lg" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-cloud-upload"></i> Upload Course Material
                    </h4>
                </div>
                <div class="card-body p-4">
                    <!-- Course Info -->
                    <div class="alert alert-info">
                        <strong>Course:</strong> <?= esc($course_title) ?>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Upload Form -->
                    <form action="<?= base_url('admin/course/' . $course_id . '/upload') ?>" 
                          method="post" 
                          enctype="multipart/form-data">
                        
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="file" class="form-label fw-bold">
                                <i class="bi bi-file-earmark-arrow-up"></i> Select File
                            </label>
                            <input type="file" 
                                   class="form-control form-control-lg" 
                                   id="file" 
                                   name="file" 
                                   required
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar">
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> 
                                Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, RAR (Max: 10MB)
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-upload"></i> Upload Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Upload Guidelines -->
            <div class="card mt-4" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle-fill text-info"></i> Upload Guidelines
                    </h5>
                    <ul>
                        <li>Ensure your file is in one of the supported formats</li>
                        <li>File size should not exceed 10MB</li>
                        <li>Use descriptive file names for easy identification</li>
                        <li>Check file content before uploading to avoid errors</li>
                        <li>Uploaded materials will be available to all enrolled students</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?= $this->include('templates/footer') ?>
