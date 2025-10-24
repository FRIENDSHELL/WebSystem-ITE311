<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Welcome, Teacher!</h3>
                    </div>
                    <div class="card-body text-center">
                        <h4>Hello, <?= esc($user_name) ?>!</h4>
                        <p class="text-muted">You are logged in as: <strong><?= esc($user_role) ?></strong></p>
                        <hr>
                        <p>This is your teacher dashboard. More features will be added here.</p>
                        <a href="/announcements" class="btn btn-primary">View All Announcements</a>
                        <a href="/logout" class="btn btn-secondary">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Latest Announcements</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($announcements)): ?>
                            <div class="alert alert-info">
                                <h5>No announcements available</h5>
                                <p class="mb-0">Check back later for updates.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Content</th>
                                            <th>Date Posted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($announcements as $announcement): ?>
                                            <tr>
                                                <td><strong><?= esc($announcement['title']) ?></strong></td>
                                                <td><?= nl2br(esc($announcement['content'])) ?></td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= $announcement['created_at'] ? date('M j, Y, g:i A', strtotime($announcement['created_at'])) : '—' ?>
                                                    </small>
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
    </div>
</body>
</html>
