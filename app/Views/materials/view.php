<?= $this->include('templates/header', ['title' => $title ?? 'Material Details']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('materials') ?>">Materials</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-3">Material Details</h2>
            
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

    <div class="row">
        <div class="col-lg-8">
            <!-- Material Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <?php
                            $fileExt = strtolower(pathinfo($material['original_name'], PATHINFO_EXTENSION));
                            $iconClass = match($fileExt) {
                                'pdf' => 'bi-file-earmark-pdf',
                                'doc', 'docx' => 'bi-file-earmark-word',
                                'ppt', 'pptx' => 'bi-file-earmark-ppt',
                                'xls', 'xlsx' => 'bi-file-earmark-excel',
                                'zip', 'rar' => 'bi-file-earmark-zip',
                                'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image',
                                'mp4', 'avi', 'mov' => 'bi-file-earmark-play',
                                default => 'bi-file-earmark'
                            };
                            ?>
                            <i class="bi <?= $iconClass ?>"></i> <?= esc($material['title']) ?>
                        </h5>
                        <div class="btn-group" role="group">
                            <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                               class="btn btn-light btn-sm">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <?php if (in_array($user_role, ['admin']) || ($user_role === 'teacher' && $material['user_id'] == session()->get('id'))): ?>
                                <a href="<?= site_url('materials/delete/' . $material['id']) ?>" 
                                   class="btn btn-outline-light btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this material?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-muted mb-2">Description</h6>
                            <?php if ($material['description']): ?>
                                <p class="mb-3"><?= nl2br(esc($material['description'])) ?></p>
                            <?php else: ?>
                                <p class="text-muted mb-3"><em>No description provided.</em></p>
                            <?php endif; ?>
                            
                            <h6 class="text-muted mb-2">File Information</h6>
                            <ul class="list-unstyled">
                                <li><strong>Original Filename:</strong> <?= esc($material['original_name']) ?></li>
                                <li><strong>File Size:</strong> <?= formatBytes($material['file_size']) ?></li>
                                <li><strong>File Type:</strong> <?= esc($material['file_type']) ?></li>
                                <li><strong>Upload Date:</strong> <?= date('F j, Y, g:i A', strtotime($material['upload_date'])) ?></li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-4 bg-light rounded">
                                <?php
                                $colorClass = match($fileExt) {
                                    'pdf' => 'text-danger',
                                    'doc', 'docx' => 'text-primary',
                                    'ppt', 'pptx' => 'text-warning',
                                    'xls', 'xlsx' => 'text-success',
                                    'zip', 'rar' => 'text-secondary',
                                    'jpg', 'jpeg', 'png', 'gif' => 'text-info',
                                    'mp4', 'avi', 'mov' => 'text-purple',
                                    default => 'text-muted'
                                };
                                ?>
                                <i class="bi <?= $iconClass ?> <?= $colorClass ?>" style="font-size: 4rem;"></i>
                                <div class="mt-2">
                                    <small class="text-muted text-uppercase fw-bold"><?= strtoupper($fileExt) ?> File</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Information -->
            <?php if ($material['course_name']): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-book"></i> Course Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Course Name</h6>
                            <p class="mb-2"><strong><?= esc($material['course_name']) ?></strong></p>
                        </div>
                        <?php if ($material['course_code']): ?>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Course Code</h6>
                            <p class="mb-2"><strong><?= esc($material['course_code']) ?></strong></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Upload Information -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-person"></i> Upload Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Uploaded By</h6>
                            <p class="mb-2"><strong><?= esc($material['uploaded_by'] ?? 'Unknown') ?></strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Upload Date</h6>
                            <p class="mb-2"><strong><?= date('F j, Y, g:i A', strtotime($material['upload_date'])) ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Download Statistics -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-graph-up"></i> Statistics</h6>
                </div>
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-2">
                        <i class="bi bi-download"></i>
                    </div>
                    <h3 class="text-primary mb-1"><?= esc($material['download_count'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Total Downloads</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-download"></i> Download File
                        </a>
                        <a href="<?= site_url('materials') ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Materials
                        </a>
                        <?php if (in_array($user_role, ['teacher', 'admin'])): ?>
                            <a href="<?= site_url('materials/upload') ?>" 
                               class="btn btn-outline-success">
                                <i class="bi bi-cloud-upload"></i> Upload New Material
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- File Preview (for images) -->
            <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-eye"></i> Preview</h6>
                </div>
                <div class="card-body text-center">
                    <img src="<?= site_url('materials/download/' . $material['id']) ?>" 
                         alt="<?= esc($material['title']) ?>" 
                         class="img-fluid rounded"
                         style="max-height: 300px;">
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// Load materials helper for formatBytes function
helper('materials');
?>

<?= $this->include('templates/footer') ?>