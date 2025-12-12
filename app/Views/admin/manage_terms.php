<?= $this->include('templates/header', ['title' => $title ?? 'Manage Terms']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Manage Terms</h2>
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

    <!-- Add Term Button -->
    <div class="row mb-4">
        <div class="col-12">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTermModal">
                <i class="bi bi-plus-circle"></i> Add New Term
            </button>
        </div>
    </div>

    <!-- Terms Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-calendar-week"></i> All Terms</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($terms)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No terms found. Add your first term using the button above.
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
                                    <?php foreach($terms as $term): ?>
                                        <tr>
                                            <td><strong><?= esc($term['name']) ?></strong></td>
                                            <td><?= esc($term['description'] ?? 'No description') ?></td>
                                            <td><span class="badge bg-primary"><?= esc($term['course_count'] ?? 0) ?> courses</span></td>
                                            <td>
                                                <?php if ($term['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editTermModal<?= $term['id'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?= site_url('admin/terms/delete/' . $term['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to delete this term?')">
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

<!-- Add Term Modal -->
<div class="modal fade" id="addTermModal" tabindex="-1" aria-labelledby="addTermModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTermModalLabel">Add New Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/terms/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Term Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Midterm, Finals, Quarter 1">
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
                    <button type="submit" class="btn btn-success">Add Term</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Term Modals -->
<?php foreach($terms as $term): ?>
<div class="modal fade" id="editTermModal<?= $term['id'] ?>" tabindex="-1" aria-labelledby="editTermModalLabel<?= $term['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTermModalLabel<?= $term['id'] ?>">Edit Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/terms/update/' . $term['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name<?= $term['id'] ?>" class="form-label">Term Name *</label>
                        <input type="text" class="form-control" id="name<?= $term['id'] ?>" name="name" value="<?= esc($term['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description<?= $term['id'] ?>" class="form-label">Description</label>
                        <textarea class="form-control" id="description<?= $term['id'] ?>" name="description" rows="3"><?= esc($term['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active<?= $term['id'] ?>" name="is_active" value="1" <?= $term['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active<?= $term['id'] ?>">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Term</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->include('templates/footer') ?>