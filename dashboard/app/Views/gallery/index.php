<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Galeri<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Galeri</h5>
                <div>
                    <a href="<?= base_url('gallery/featured') ?>" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-star"></i> Lihat Unggulan
                    </a>
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a href="<?= base_url('gallery/add') ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i> Tambah
                        </a>
                    <?php endif; ?>
                </div>
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
                
                <!-- Category Filter -->
                <div class="mb-4">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('gallery') ?>" class="btn btn-outline-secondary <?= !$selectedCategory ? 'active' : '' ?>">
                            Semua
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="<?= base_url('gallery?category=' . urlencode($category)) ?>" 
                               class="btn btn-outline-secondary <?= $selectedCategory === $category ? 'active' : '' ?>">
                                <?= ucfirst($category) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Gallery Items -->
                <?php if (empty($galleryItems)): ?>
                    <div class="alert alert-info">
                        Tidak ada item galeri yang ditemukan.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($galleryItems as $item): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="<?= esc($item['image_url']) ?>" class="card-img-top" alt="<?= esc($item['title']) ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($item['title']) ?></h5>
                                        <span class="badge bg-primary mb-2"><?= esc($item['category']) ?></span>
                                        <?php if ($item['is_featured']): ?>
                                            <span class="badge bg-warning mb-2">Unggulan</span>
                                        <?php endif; ?>
                                        <p class="card-text"><?= esc(substr($item['description'], 0, 100)) ?><?= strlen($item['description']) > 100 ? '...' : '' ?></p>
                                    </div>
                                    <div class="card-footer bg-transparent d-flex justify-content-between">
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($item['created_at'])) ?>
                                        </small>
                                        <?php if (session()->get('role') === 'admin'): ?>
                                            <a href="<?= base_url('gallery/delete/' . $item['id']) ?>" 
                                               class="text-danger" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Pagination -->
                <?php if ($pager): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>