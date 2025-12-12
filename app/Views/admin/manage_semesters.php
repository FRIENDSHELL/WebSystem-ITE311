<?= $this->include('templates/header', ['title' => $title ?? 'Manage Semesters']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Manage Semesters</h2>
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

    <!-- Add Semester Button -->
    <div class="row mb-4">
        <div class="col-12">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSemesterModal">
                <i class="bi bi-plus-circle"></i> Add New Semester
            </button>
        </div>
    </div>

    <!-- Semesters Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> All Semesters</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($semesters)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No semesters found. Add your first semester using the button above.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Courses</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($semesters as $semester): ?>
                                        <tr>
                                            <td><strong><?= esc($semester['name']) ?></strong></td>
                                            <td><?= esc($semester['description'] ?? 'No description') ?></td>
                                            <td><span class="badge bg-primary"><?= esc($semester['course_count'] ?? 0) ?> courses</span></td>
                                            <td>
                                                <?php if ($semester['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editSemesterModal<?= $semester['id'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?= site_url('admin/semesters/delete/' . $semester['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to delete this semester?')">
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

<!-- Add Semester Modal -->
<div class="modal fade" id="addSemesterModal" tabindex="-1" aria-labelledby="addSemesterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSemesterModalLabel">Add New Semester</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/semesters/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Semester Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Fall 2025, Spring 2026">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Optional description"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Semester Modals -->
<?php foreach($semesters as $semester): ?>
<div class="modal fade" id="editSemesterModal<?= $semester['id'] ?>" tabindex="-1" aria-labelledby="editSemesterModalLabel<?= $semester['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSemesterModalLabel<?= $semester['id'] ?>">Edit Semester</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/semesters/update/' . $semester['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name<?= $semester['id'] ?>" class="form-label">Semester Name *</label>
                        <input type="text" class="form-control" id="name<?= $semester['id'] ?>" name="name" value="<?= esc($semester['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description<?= $semester['id'] ?>" class="form-label">Description</label>
                        <textarea class="form-control" id="description<?= $semester['id'] ?>" name="description" rows="3"><?= esc($semester['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active<?= $semester['id'] ?>" name="is_active" value="1" <?= $semester['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active<?= $semester['id'] ?>">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->include('templates/footer') ?>