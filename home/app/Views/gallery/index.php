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
<section class="gallery-hero py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">Galeri Raja Ampat</h1>
                <p class="lead">Kumpulan momen indah dan kenangan tak terlupakan di Kepulauan Raja Ampat</p>
            </div>
        </div>
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
                            <a href="<?= base_url('gallery') ?>" class="btn btn-outline-primary <?= !service('uri')->getSegment(2) ? 'active' : '' ?>">
                                <i class="fas fa-images me-2"></i>Semua
                            </a>
                            <?php foreach ($categories as $category): ?>
                            <a href="<?= base_url('gallery/category/' . $category) ?>" class="btn btn-outline-primary <?= service('uri')->getSegment(2) === $category ? 'active' : '' ?>">
                                <i class="fas fa-<?= getCategoryIcon($category) ?> me-2"></i>
                                <?= getCategoryName($category) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Gallery -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">Foto Pilihan</h2>
                <p class="lead">Momen-momen terbaik yang diabadikan</p>
            </div>
            
            <?php if (!empty($featuredGallery)): ?>
                <?php foreach ($featuredGallery as $item): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="gallery-item position-relative">
                        <img src="<?= base_url($item['image_url']) ?>" 
                             class="img-fluid rounded shadow-sm" 
                             alt="<?= $item['title'] ?>" 
                             style="height: 200px; width: 100%; object-fit: cover;">
                        <div class="gallery-overlay position-absolute top-0 start-0 end-0 bottom-0 bg-dark bg-opacity-50 d-flex align-items-end p-3 opacity-0 transition-all">
                            <div class="gallery-caption text-white">
                                <h6 class="mb-0"><?= $item['title'] ?></h6>
                                <?php if (!empty($item['description'])): ?>
                                <small class="text-white-50"><?= substr($item['description'], 0, 50) ?>...</small>
                                <?php endif; ?>
                                <small class="d-block mt-1">
                                    <span class="badge bg-primary"><?= getCategoryName($item['category']) ?></span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-3">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada foto yang ditampilkan.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Gallery by Category -->
        <?php foreach ($galleryByCategory as $category => $items): ?>
            <?php if (!empty($items)): ?>
            <div class="row mb-5">
                <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold"><?= getCategoryName($category) ?></h3>
                    <a href="<?= base_url('gallery/category/' . $category) ?>" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <?php foreach (array_slice($items, 0, 4) as $item): ?>
                <div class="col-md-3 mb-4">
                    <div class="gallery-item position-relative">
                        <img src="<?= $adminUrl . '/' . $item['image_url'] ?>" 
                             class="img-fluid rounded shadow-sm" 
                             alt="<?= $item['title'] ?>" 
                             style="height: 180px; width: 100%; object-fit: cover;">
                        <div class="gallery-overlay position-absolute top-0 start-0 end-0 bottom-0 bg-dark bg-opacity-50 d-flex align-items-end p-3 opacity-0 transition-all">
                            <div class="gallery-caption text-white">
                                <h6 class="mb-0"><?= $item['title'] ?></h6>
                                <?php if (!empty($item['description'])): ?>
                                <small class="text-white-50"><?= substr($item['description'], 0, 50) ?>...</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>




