<?php
// var_dump($user_id);
// die();
?>
<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <!-- Sidebar Profil -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4><?= esc($user['full_name']) ?></h4>
                    <p class="text-muted"><?= esc($user['role'] === 'admin' ? 'Administrator' : 'Customer') ?></p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-2"></i>Edit Profil
                        </button>
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-lock me-2"></i>Ubah Password
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Menu Profil -->
            <div class="list-group">
                <a href="<?= base_url('booking/my-bookings') ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-ticket-alt me-2"></i>Pemesanan Saya
                </a>
                <a href="<?= base_url('boats/my-open-trip-requests') ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-ship me-2"></i>Request Open Trip
                </a>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Informasi Profil -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Nama Lengkap</div>
                        <div class="col-sm-9"><?= esc($user['full_name']) ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Email</div>
                        <div class="col-sm-9"><?= esc($user['email']) ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Telepon</div>
                        <div class="col-sm-9"><?= esc($user['phone']) ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Alamat</div>
                        <div class="col-sm-9"><?= esc($user['address'] ?? 'Belum diisi') ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Status Akun</div>
                        <div class="col-sm-9">
                            <span class="badge bg-<?= $user['email_verified'] ? 'success' : 'warning' ?>">
                                <?= $user['email_verified'] ? 'Terverifikasi' : 'Belum Terverifikasi' ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-3 fw-bold">Bergabung Pada</div>
                        <div class="col-sm-9"><?= date('d F Y', strtotime($user['created_at'])) ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Statistik Pengguna -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Total Pemesanan</h5>
                            <p class="card-text display-6">0</p>
                            <a href="<?= base_url('booking/my-bookings') ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Open Trip</h5>
                            <p class="card-text display-6">0</p>
                            <a href="<?= base_url('boats/my-open-trip-requests') ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('profile/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <p class="mb-0"><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', $user['full_name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', $user['phone']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= old('address', $user['address'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ubah Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Ubah Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('profile/changePassword') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <p class="mb-0"><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #dee2e6;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>