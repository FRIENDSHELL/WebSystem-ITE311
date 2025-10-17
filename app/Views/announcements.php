<!-- app/Views/announcements.php -->
<div class="container mt-4">
    <h2>Announcements</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (empty($announcements)): ?>
        <p>No announcements available.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach($announcements as $a): ?>
                <li class="list-group-item mb-2">
                    <h5><?= esc($a['title']) ?></h5>
                    <p><?= nl2br(esc($a['content'])) ?></p>
                    <small class="text-muted">Posted: <?= $a['created_at'] ? date('F j, Y, g:i A', strtotime($a['created_at'])) : '—' ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
