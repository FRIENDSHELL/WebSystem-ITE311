<?php
// Helper function for file size formatting - must be defined before use
if (!function_exists('formatBytes')) {
    function formatBytes($size, $precision = 2) {
        if ($size == 0 || $size == null) {
            return '0 B';
        }
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}
?>
<?= $this->include('templates/header', ['title' => $title ?? 'Course Materials']) ?>

<main class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Course Materials</h2>
            
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

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Materials</h6>
                            <h2 class="card-title mb-0"><?= esc($stats['total_materials'] ?? 0) ?></h2>
                        </div>
                        <div><i class="bi bi-file-earmark fs-1 opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Downloads</h6>
                            <h2 class="card-title mb-0"><?= esc($stats['total_downloads'] ?? 0) ?></h2>
                        </div>
                        <div><i class="bi bi-download fs-1 opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0"><i class="bi bi-search"></i> Search Materials</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <?php if (in_array($user_role, ['teacher', 'admin'])): ?>
                                <a href="<?= site_url('materials/upload') ?>" class="btn btn-primary">
                                    <i class="bi bi-cloud-upload"></i> Upload Material
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= site_url('materials') ?>" class="row g-3">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" 
                                       value="<?= esc($search ?? '') ?>" 
                                       placeholder="Search by title, description, filename, or course...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">Search</button>
                        </div>
                    </form>
                    <?php if ($search): ?>
                        <div class="mt-2">
                            <small class="text-muted">
                                Showing results for: <strong>"<?= esc($search) ?>"</strong>
                                <a href="<?= site_url('materials') ?>" class="ms-2 text-decoration-none">Clear search</a>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Materials List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-folder"></i> 
                        <?= $search ? 'Search Results' : 'All Materials' ?>
                        <span class="badge bg-secondary ms-2"><?= count($materials) ?></span>
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleView('grid')">
                            <i class="bi bi-grid"></i> Grid
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="toggleView('list')">
                            <i class="bi bi-list"></i> List
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($materials)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
                            <h4 class="text-muted">No Materials Found</h4>
                            <?php if ($search): ?>
                                <p class="text-muted">No materials match your search criteria.</p>
                                <a href="<?= site_url('materials') ?>" class="btn btn-outline-primary">View All Materials</a>
                            <?php else: ?>
                                <p class="text-muted">No materials have been uploaded yet.</p>
                                <?php if (in_array($user_role, ['teacher', 'admin'])): ?>
                                    <a href="<?= site_url('materials/upload') ?>" class="btn btn-primary">
                                        <i class="bi bi-cloud-upload"></i> Upload First Material
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- List View -->
                        <div id="listView" class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Material</th>
                                        <th>Course</th>
                                        <th>Uploaded By</th>
                                        <th>Upload Date</th>
                                        <th>Downloads</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($materials as $material): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php
                                                        $fileExt = strtolower(pathinfo($material['original_name'], PATHINFO_EXTENSION));
                                                        $iconClass = match($fileExt) {
                                                            'pdf' => 'bi-file-earmark-pdf text-danger',
                                                            'doc', 'docx' => 'bi-file-earmark-word text-primary',
                                                            'ppt', 'pptx' => 'bi-file-earmark-ppt text-warning',
                                                            'xls', 'xlsx' => 'bi-file-earmark-excel text-success',
                                                            'zip', 'rar' => 'bi-file-earmark-zip text-secondary',
                                                            'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image text-info',
                                                            'mp4', 'avi', 'mov' => 'bi-file-earmark-play text-purple',
                                                            default => 'bi-file-earmark text-muted'
                                                        };
                                                        ?>
                                                        <i class="bi <?= $iconClass ?> fs-2"></i>
                                                    </div>
                                                    <div>
                                                        <strong><?= esc($material['title']) ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?= esc($material['original_name']) ?></small>
                                                        <br>
                                                        <small class="text-muted"><?= formatBytes($material['file_size']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($material['course_name']): ?>
                                                    <strong><?= esc($material['course_name']) ?></strong>
                                                    <?php if ($material['course_code']): ?>
                                                        <br><small class="text-muted"><?= esc($material['course_code']) ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">General</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($material['uploaded_by'] ?? 'Unknown') ?></td>
                                            <td>
                                                <small><?= date('M j, Y', strtotime($material['upload_date'])) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= esc($material['download_count'] ?? 0) ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= site_url('materials/view/' . $material['id']) ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                                                       class="btn btn-sm btn-outline-success" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <?php if (in_array($user_role, ['admin']) || ($user_role === 'teacher' && $material['user_id'] == session()->get('id'))): ?>
                                                        <a href="<?= site_url('materials/delete/' . $material['id']) ?>" 
                                                           class="btn btn-sm btn-outline-danger" title="Delete"
                                                           onclick="return confirm('Are you sure you want to delete this material?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Grid View (Hidden by default) -->
                        <div id="gridView" class="row g-3" style="display: none;">
                            <?php foreach($materials as $material): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start mb-3">
                                                <div class="me-3">
                                                    <?php
                                                    $fileExt = strtolower(pathinfo($material['original_name'], PATHINFO_EXTENSION));
                                                    $iconClass = match($fileExt) {
                                                        'pdf' => 'bi-file-earmark-pdf text-danger',
                                                        'doc', 'docx' => 'bi-file-earmark-word text-primary',
                                                        'ppt', 'pptx' => 'bi-file-earmark-ppt text-warning',
                                                        'xls', 'xlsx' => 'bi-file-earmark-excel text-success',
                                                        'zip', 'rar' => 'bi-file-earmark-zip text-secondary',
                                                        'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image text-info',
                                                        'mp4', 'avi', 'mov' => 'bi-file-earmark-play text-purple',
                                                        default => 'bi-file-earmark text-muted'
                                                    };
                                                    ?>
                                                    <i class="bi <?= $iconClass ?> fs-1"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title"><?= esc($material['title']) ?></h6>
                                                    <p class="card-text">
                                                        <small class="text-muted"><?= esc($material['original_name']) ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <?php if ($material['description']): ?>
                                                <p class="card-text"><?= esc(substr($material['description'], 0, 100)) ?><?= strlen($material['description']) > 100 ? '...' : '' ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="bi bi-download"></i> <?= esc($material['download_count'] ?? 0) ?>
                                                </small>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= site_url('materials/view/' . $material['id']) ?>" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function toggleView(viewType) {
    const listView = document.getElementById('listView');
    const gridView = document.getElementById('gridView');
    const buttons = document.querySelectorAll('.btn-group .btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    
    if (viewType === 'grid') {
        listView.style.display = 'none';
        gridView.style.display = 'block';
        event.target.classList.add('active');
    } else {
        listView.style.display = 'block';
        gridView.style.display = 'none';
        event.target.classList.add('active');
    }
}

// Note: formatBytes is handled by PHP function defined at top of file
</script>

<?= $this->include('templates/footer') ?>