<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body text-center p-5" style="background: #ffd6e0;"> <!-- powder pink background -->
            <h2 class="mb-3" style="font-weight: 600; color: #440615ff;">Welcome, <span><?= esc($user_name) ?></span> 👋</h2>
            <p class="text-dark mb-4">You are logged in as <strong><?= esc($user_role) ?></strong>.</p>

            <div class="d-flex justify-content-center gap-3">
                <a href="<?= site_url('/') ?>" class="btn btn-outline-light px-4 py-2 rounded-pill shadow-sm" 
                   style="background-color: #e8bac1ff; color: #fff; border: none; transition: all 0.3s;">Home</a>
                <a href="<?= site_url('logout') ?>" class="btn btn-danger px-4 py-2 rounded-pill shadow-sm" 
                   style="background-color: #ae4c63ff; border: none; color: #fff; transition: all 0.3s;">Logout</a>
            </div>
        </div>
    </div>
</div>

<style>
    .card-body h2:hover {
        transform: translateY(-2px);
        transition: transform 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
</style>
<?= $this->endSection() ?>
