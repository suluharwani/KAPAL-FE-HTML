<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Galeri Unggulan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Galeri Unggulan</h5>
                <a href="<?= base_url('gallery') ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Galeri
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($featuredItems)): ?>
                    <div class="alert alert-info">
                        Tidak ada item galeri unggulan yang ditemukan.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($featuredItems as $item): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="<?= esc($item['image_url']) ?>" class="card-img-top" alt="<?= esc($item['title']) ?>" style="height: 250px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($item['title']) ?></h5>
                                        <span class="badge bg-primary mb-2"><?= esc($item['category']) ?></span>
                                        <p class="card-text"><?= esc($item['description']) ?></p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($item['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>