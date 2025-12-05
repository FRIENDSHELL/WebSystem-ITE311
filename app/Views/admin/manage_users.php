<?= $this->include('templates/header', ['title' => $title ?? 'Manage Users']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Manage Users</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle"></i> Add New User
                </button>
            </div>

            <!-- Success/Error Messages -->
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

            <!-- Users Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <div class="alert alert-info">
                            <h5>No users found</h5>
                            <p class="mb-0">Click "Add New User" to create a user.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $user): ?>
                                        <tr>
                                            <td><?= esc($user['id']) ?></td>
                                            <td><?= esc($user['name']) ?></td>
                                            <td><?= esc($user['email']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'teacher' ? 'primary' : 'success') ?>">
                                                    <?= esc(ucfirst($user['role'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $user['created_at'] ? date('M j, Y, g:i A', strtotime($user['created_at'])) : '—' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="editUser(<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>)">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <?php if ($user['id'] != session()->get('id')): ?>
                                                    <a href="<?= site_url('admin/users/delete/' . $user['id']) ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">(Current User)</span>
                                                <?php endif; ?>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/users/add') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_name" name="name" required 
                               value="<?= esc(old('name') ?? '') ?>" minlength="3" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="add_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="add_email" name="email" required 
                               value="<?= esc(old('email') ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="add_password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="add_password" name="password" required 
                               minlength="4">
                    </div>
                    <div class="mb-3">
                        <label for="add_role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="student" <?= (old('role') === 'student') ? 'selected' : '' ?>>Student</option>
                            <option value="teacher" <?= (old('role') === 'teacher') ? 'selected' : '' ?>>Teacher</option>
                            <option value="admin" <?= (old('role') === 'admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required 
                               minlength="3" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password" 
                               minlength="4" placeholder="Leave blank to keep current password">
                        <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_role').value = user.role;
    
    // Set form action
    document.getElementById('editUserForm').action = '<?= site_url('admin/users/update') ?>/' + user.id;
    
    // Show modal
    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
}
</script>

<?= $this->include('templates/footer') ?>

