<!-- app/Views/auth/login.php -->
<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-center">Login Customer</h4>
            </div>
            <div class="card-body">
                <?php if (session('message') !== null) : ?>
                    <div class="alert alert-success">
                        <?= session('message') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger">
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('login') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?= (session('errors.username') ? 'is-invalid' : '') ?>" 
                               id="username" name="username" value="<?= old('username') ?>" required>
                        <?php if (session('errors.username')) : ?>
                            <div class="invalid-feedback">
                                <?= session('errors.username') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control <?= (session('errors.password') ? 'is-invalid' : '') ?>" 
                               id="password" name="password" required>
                        <?php if (session('errors.password')) : ?>
                            <div class="invalid-feedback">
                                <?= session('errors.password') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    
                    <div class="text-center">
                        <a href="<?= base_url('forgot-password') ?>">Lupa Password?</a>
                        <span class="mx-2">|</span>
                        <a href="<?= base_url('register') ?>">Buat Akun Baru</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>