<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Back Button -->
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>

            <!-- Course Header -->
            <div class="card shadow-lg mb-4" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-book"></i> <?= esc($course['title']) ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($course['description'])): ?>
                        <p class="text-muted"><?= esc($course['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Upload Button (Admin/Instructor Only) -->
            <?php if ($user_role === 'admin' || $user_role === 'instructor' || $user_role === 'teacher'): ?>
                <div class="mb-3">
                    <a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>" 
                       class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Upload New Material
                    </a>
                </div>
            <?php endif; ?>

            <!-- Materials List -->
            <div class="card shadow-lg" style="border-radius: 15px;">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-folder2-open"></i> Course Materials
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($materials)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 40%;">File Name</th>
                                        <th style="width: 20%;">Uploaded Date</th>
                                        <th style="width: 20%;" class="text-center">Actions</th>
                                        <?php if ($user_role === 'admin' || $user_role === 'instructor' || $user_role === 'teacher'): ?>
                                            <th style="width: 15%;" class="text-center">Manage</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materials as $index => $material): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                                <?= esc($material['file_name']) ?>
                                            </td>
                                            <td>
                                                <?php
                                                    if (!empty($material['created_at'])) {
                                                        try {
                                                            $date = new DateTime($material['created_at']);
                                                            echo '<small class="text-muted">';
                                                            echo '<i class="bi bi-calendar3"></i> ' . $date->format('M d, Y') . '<br>';
                                                            echo '<i class="bi bi-clock"></i> ' . $date->format('h:i A');
                                                            echo '</small>';
                                                        } catch (Exception $e) {
                                                            echo '<small class="text-muted">Date not available</small>';
                                                        }
                                                    } else {
                                                        echo '<small class="text-muted">Date not available</small>';
                                                    }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('materials/download/' . $material['id']) ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            </td>
                                            <?php if ($user_role === 'admin' || $user_role === 'instructor' || $user_role === 'teacher'): ?>
                                                <td class="text-center">
                                                    <a href="<?= base_url('materials/delete/' . $material['id']) ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Are you sure you want to delete this material?');">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle"></i>
                            No materials available for this course yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-4" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle-fill text-info"></i> About Course Materials
                    </h5>
                    <ul class="mb-0">
                        <li>Click the download button to save materials to your device</li>
                        <li>Materials are provided by your instructor for learning purposes</li>
                        <li>Make sure you have appropriate software to open the files</li>
                        <li>Contact your instructor if you have issues accessing materials</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?= $this->include('templates/footer') ?>
