<?php
$searchTerm = $searchTerm ?? '';
$canEnroll = $canEnroll ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"></div>
</head>
<body>
    <div class="row mb-4">
        <div class="col-md-6">
            <form id="searchForm" class="d-flex" method="get" action="<?= base_url('course/search') ?>">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" 
                        placeholder="Search courses..." name="search_term"
                        value="<?= esc($searchTerm) ?>">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div id="coursesContainer" class="row">
        <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4">
                <div class="card course-card">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($course['title'] ?? 'Untitled') ?></h5>
                        <p class="card-text"><?= esc($course['description'] ?? 'No description available.') ?></p>
                        <?php if ($canEnroll): ?>
                            <button class="btn btn-success enroll-btn" data-course-id="<?= esc($course['id']) ?>">
                                Enroll
                            </button>
                        <?php else: ?>
                            <a href="/courses/view/<?= esc($course['id']) ?>" class="btn btn-primary">View Course</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="alertBox" class="alert mt-3 d-none" role="alert"></div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('searchInput');
        var form  = document.getElementById('searchForm');

        if (!input || !form) return;

        function applyFilter() {
            var value = input.value.toLowerCase();
            var cards = document.querySelectorAll('.course-card');

            cards.forEach(function (card) {
                var text = card.textContent.toLowerCase();
                card.parentElement.style.display = text.indexOf(value) > -1 ? '' : 'none';
            });
        }

        // Live filter while typing
        input.addEventListener('keyup', applyFilter);

        // Also filter on form submit (pressing Enter / clicking Search)
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            applyFilter();
        });
    });
    </script>
</body>
</html> 