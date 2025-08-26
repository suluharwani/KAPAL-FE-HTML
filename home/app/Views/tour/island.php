<!-- Gallery Section -->
<section id="gallery" class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Galeri Pulau <?= $island['island_name'] ?? 'Raja Ampat' ?></h2>
                <p class="lead">Momen indah yang diabadikan di pulau ini</p>
            </div>
        </div>
        
        <div class="row">
            <?php if (!empty($gallery) && is_array($gallery)): ?>
                <?php foreach ($gallery as $item): ?>
                <div class="col-md-4 mb-4">
                    <div class="gallery-item position-relative">
                        <img src="<?= base_url($item['image_url'] ?? 'images/gallery-placeholder.jpg') ?>" 
                             class="img-fluid rounded shadow-sm" 
                             alt="<?= $item['title'] ?? 'Gallery Image' ?>" 
                             style="height: 250px; width: 100%; object-fit: cover;">
                        <div class="gallery-overlay position-absolute top-0 start-0 end-0 bottom-0 bg-dark bg-opacity-50 d-flex align-items-end p-3 opacity-0 transition-opacity">
                            <div class="gallery-caption text-white">
                                <h6 class="mb-0"><?= $item['title'] ?? 'Image Title' ?></h6>
                                <?php if (!empty($item['description'])): ?>
                                <small class="text-white-50"><?= $item['description'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-3">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Galeri foto akan segera diupdate.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= base_url('gallery') ?>" class="btn btn-outline-primary">Lihat Galeri Lengkap</a>
        </div>
    </div>
</section>