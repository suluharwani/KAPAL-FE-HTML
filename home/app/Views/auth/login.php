<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0 text-center">Login</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?php 
                            $error = session()->getFlashdata('error');
                            // Jika error mengandung link HTML, gunakan html_entity_decode
                            if (strpos($error, '<a href') !== false) {
                                echo $error;
                            } else {
                                echo htmlspecialchars($error);
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= htmlspecialchars(session()->getFlashdata('message')) ?></div>
                    <?php endif; ?>

                    <!-- Google Login Button -->
                    <div class="d-grid mb-4">
                        <a href="<?= base_url('auth/google') ?>" class="btn btn-danger">
                            <i class="fab fa-google me-2"></i>Login dengan Google
                        </a>
                    </div>

                    <div class="text-center mb-4">
                        <hr>
                        <span class="bg-white px-3">ATAU</span>
                    </div>
                    
                    <form action="<?= base_url('auth/attemptLogin') ?>" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        Don't have account? <a href="<?= base_url('auth/register') ?>">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>