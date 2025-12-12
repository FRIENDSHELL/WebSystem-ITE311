<?= $this->include('templates/header', ['title' => $title ?? 'Manage School Years']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Manage School Years</h2>
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

    <!-- Add School Year Button -->
    <div class="row mb-4">
        <div class="col-12">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSchoolYearModal">
                <i class="bi bi-plus-circle"></i> Add New School Year
            </button>
        </div>
    </div>

    <!-- School Years Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-calendar-range"></i> All School Years</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($schoolYears)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No school years found. Add your first school year using the button above.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Year</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Courses</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($schoolYears as $schoolYear): ?>
                                        <tr>
                                            <td><strong><?= esc($schoolYear['year']) ?></strong></td>
                                            <td><?= $schoolYear['start_date'] ? date('M j, Y', strtotime($schoolYear['start_date'])) : 'Not set' ?></td>
                                            <td><?= $schoolYear['end_date'] ? date('M j, Y', strtotime($schoolYear['end_date'])) : 'Not set' ?></td>
                                            <td><span class="badge bg-primary"><?= esc($schoolYear['course_count'] ?? 0) ?> courses</span></td>
                                            <td>
                                                <?php if ($schoolYear['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editSchoolYearModal<?= $schoolYear['id'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?= site_url('admin/school-years/delete/' . $schoolYear['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to delete this school year?')">
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

<!-- Add School Year Modal -->
<div class="modal fade" id="addSchoolYearModal" tabindex="-1" aria-labelledby="addSchoolYearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSchoolYearModalLabel">Add New School Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/school-years/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="year" class="form-label">School Year *</label>
                        <input type="text" class="form-control" id="year" name="year" required placeholder="e.g., 2025-2026, AY 2025-2026">
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
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
                    <button type="submit" class="btn btn-success">Add School Year</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit School Year Modals -->
<?php foreach($schoolYears as $schoolYear): ?>
<div class="modal fade" id="editSchoolYearModal<?= $schoolYear['id'] ?>" tabindex="-1" aria-labelledby="editSchoolYearModalLabel<?= $schoolYear['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSchoolYearModalLabel<?= $schoolYear['id'] ?>">Edit School Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/school-years/update/' . $schoolYear['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="year<?= $schoolYear['id'] ?>" class="form-label">School Year *</label>
                        <input type="text" class="form-control" id="year<?= $schoolYear['id'] ?>" name="year" value="<?= esc($schoolYear['year']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_date<?= $schoolYear['id'] ?>" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date<?= $schoolYear['id'] ?>" name="start_date" value="<?= esc($schoolYear['start_date']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="end_date<?= $schoolYear['id'] ?>" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date<?= $schoolYear['id'] ?>" name="end_date" value="<?= esc($schoolYear['end_date']) ?>">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active<?= $schoolYear['id'] ?>" name="is_active" value="1" <?= $schoolYear['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active<?= $schoolYear['id'] ?>">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update School Year</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->include('templates/footer') ?>