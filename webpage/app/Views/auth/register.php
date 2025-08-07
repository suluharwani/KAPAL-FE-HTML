<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-center">Daftar Akun Customer</h4>
            </div>
            <div class="card-body">
                <!-- Tampilkan error dari session flashdata -->
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <!-- Tampilkan error dari validation -->
                <?php if (isset($errors)) : ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('reg') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control <?= (isset($errors['username'])) ? 'is-invalid' : '' ?>" 
                                   id="username" name="username" value="<?= old('username', $old['username'] ?? '') ?>" required>
                            <?php if (isset($errors['username'])) : ?>
                                <div class="invalid-feedback">
                                    <?= $errors['username'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?= (isset($errors['email']) ? 'is-invalid' : '') ?>" 
                                   id="email" name="email" value="<?= old('email', $old['email'] ?? '') ?>" required>
                            <?php if (isset($errors['email'])) : ?>
                                <div class="invalid-feedback">
                                    <?= $errors['email'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?= (isset($errors['password'])) ? 'is-invalid' : '' ?>" 
                                   id="password" name="password" required>
                            <?php if (isset($errors['password'])) : ?>
                                <div class="invalid-feedback">
                                    <?= $errors['password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control <?= (isset($errors['confirm_password'])) ? 'is-invalid' : '' ?>" 
                                   id="confirm_password" name="confirm_password" required>
                            <?php if (isset($errors['confirm_password'])) : ?>
                                <div class="invalid-feedback">
                                    <?= $errors['confirm_password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control <?= (isset($errors['full_name'])) ? 'is-invalid' : '' ?>" 
                                   id="full_name" name="full_name" value="<?= old('full_name', $old['full_name'] ?? '') ?>" required>
                            <?php if (isset($errors['full_name'])) : ?>
                                <div class="invalid-feedback">
                                    <?= $errors['full_name'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control <?= (isset($errors['phone'])) ? 'is-invalid' : '' ?>" 
                                   id="phone" name="phone" value="<?= old('phone', $old['phone'] ?? '') ?>" required>
                            <?php if (isset($errors['phone'])) : ?>
                                <div class="invalid-feedback">
                                    <?= $errors['phone'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Daftar</button>
                    </div>
                    
                    <div class="text-center">
                        Sudah punya akun? <a href="<?= base_url('login') ?>">Login disini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>