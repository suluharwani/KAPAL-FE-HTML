<?php
// Helper functions for view
function getCategoryIcon($category) {
    $icons = [
        'kapal' => 'ship',
        'wisata' => 'umbrella-beach',
        'penumpang' => 'users',
        'pulau' => 'island-tropical'
    ];
    return $icons[$category] ?? 'image';
}

function getCategoryName($category) {
    $names = [
        'kapal' => 'Kapal',
        'wisata' => 'Wisata',
        'penumpang' => 'Penumpang',
        'pulau' => 'Pulau'
    ];
    return $names[$category] ?? ucfirst($category);
}
?>

<!-- Gallery Hero Section -->
<section class="gallery-hero py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">Galeri <?= $categoryName ?></h1>
                <p class="lead">Kumpulan foto <?= strtolower($categoryName) ?> di Kepulauan Raja Ampat</p>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('gallery') ?>">Galeri</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $categoryName ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Gallery Content -->
<section class="py-5">
    <div class="container">
        <!-- Category Navigation -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Kategori Galeri</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= base_url('gallery') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-images me-2"></i>Semua
                            </a>
                            <?php 
                            $categories = ['kapal', 'wisata', 'penumpang', 'pulau'];
                            foreach ($categories as $cat): 
                            ?>
                            <a href="<?= base_url('gallery/category/' . $cat) ?>" class="btn btn-outline-primary <?= $category === $cat ? 'active' : '' ?>">
                                <i class="fas fa-<?= getCategoryIcon($cat) ?> me-2"></i>
                                <?= getCategoryName($cat) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="row">
            <?php if (!empty($gallery)): ?>
                <?php foreach ($gallery as $item): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="gallery-item position-relative">
                        <img src="<?= base_url($item['image_url']) ?>" 
                             class="img-fluid rounded shadow-sm" 
                             alt="<?= $item['title'] ?>" 
                             style="height: 250px; width: 100%; object-fit: cover;">
                        <div class="gallery-overlay position-absolute top-0 start-0 end-0 bottom-0 bg-dark bg-opacity-50 d-flex align-items-end p-3 opacity-0 transition-all">
                            <div class="gallery-caption text-white">
                                <h6 class="mb-0"><?= $item['title'] ?></h6>
                                <?php if (!empty($item['description'])): ?>
                                <small class="text-white-50"><?= $item['description'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h3>Belum Ada Foto</h3>
                    <p class="text-muted">Belum ada foto dalam kategori <?= strtolower($categoryName) ?>.</p>
                    <a href="<?= base_url('gallery') ?>" class="btn btn-primary">Kembali ke Galeri</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>




