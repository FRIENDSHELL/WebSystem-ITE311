<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="text-center mb-4">Register</h3>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <form action="<?= site_url('register') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required value="<?= old('name') ?>">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">

                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    

                    <button type="submit" class="btn btn-success w-100">Register</button>
                </form>

                <p class="text-center mt-3">
                    Already have an account? <a href="<?= site_url('login') ?>">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
