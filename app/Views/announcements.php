<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Announcements</h2>
                    <div>
                        <?php if (session()->get('logged_in')): ?>
                            <span class="text-muted">Welcome, <?= esc(session()->get('name')) ?>!</span>
                            <a href="/logout" class="btn btn-outline-secondary btn-sm ms-2">Logout</a>
                        <?php else: ?>
                            <a href="/login" class="btn btn-primary btn-sm">Login</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (empty($announcements)): ?>
                    <div class="alert alert-info">
                        <h5>No announcements available</h5>
                        <p class="mb-0">Check back later for updates.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach($announcements as $announcement): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?= esc($announcement['title']) ?></h5>
                                    <small class="text-muted">
                                        <?= $announcement['created_at'] ? date('M j, Y, g:i A', strtotime($announcement['created_at'])) : '—' ?>
                                    </small>
                                </div>
                                <p class="mb-1"><?= nl2br(esc($announcement['content'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
