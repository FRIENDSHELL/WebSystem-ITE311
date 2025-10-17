<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Welcome, Teacher!</h3>
                    </div>
                    <div class="card-body text-center">
                        <h4>Hello, <?= esc($user_name) ?>!</h4>
                        <p class="text-muted">You are logged in as: <strong><?= esc($user_role) ?></strong></p>
                        <hr>
                        <p>This is your teacher dashboard. More features will be added here.</p>
                        <a href="/announcements" class="btn btn-primary">View Announcements</a>
                        <a href="/logout" class="btn btn-secondary">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
