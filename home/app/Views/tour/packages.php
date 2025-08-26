<?= $this->extend('templates/header') ?>
<?= $this->section('content') ?>

<!-- Packages Hero Section -->
<section class="packages-hero py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">Paket Wisata Raja Ampat</h1>
                <p class="lead">Temukan paket wisata terbaik untuk menjelajahi keindahan Kepulauan Raja Ampat</p>
            </div>
        </div>
    </div>
</section>

<!-- Packages Content -->
<section class="py-5">
    <div class="container">
        <!-- Filter Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filter Paket Wisata</h5>
                        <form id="packageFilter">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Pulau Tujuan</label>
                                    <select class="form-select" id="islandFilter">
                                        <option value="">Semua Pulau</option>
                                        <?php foreach ($islands as $island): ?>
                                            <option value="<?= $island['island_name'] ?>"><?= $island['island_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Durasi</label>
                                    <select class="form-select" id="durationFilter">
                                        <option value="">Semua Durasi</option>
                                        <option value="1 Hari">1 Hari</option>
                                        <option value="2 Hari">2 Hari</option>
                                        <option value="3 Hari">3 Hari</option>
                                        <option value="4+ Hari">4+ Hari</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Budget</label>
                                    <select class="form-select" id="budgetFilter">
                                        <option value="">Semua Budget</option>
                                        <option value="low">Rp 0 - 500.000</option>
                                        <option value="medium">Rp 500.000 - 1.500.000</option>
                                        <option value="high">Rp 1.500.000+</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packages Grid -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">Semua Paket Wisata</h2>
                <p class="lead">Pilih paket yang paling sesuai dengan rencana perjalanan Anda</p>
            </div>
            
            <?php if (!empty($packages)): ?>
                <?php foreach ($packages as $package): ?>
                <div class="col-md-6 col-lg-4 mb-4 package-item" data-island="<?= $package['island_slug'] ?>" data-duration="<?= $package['duration'] ?>" data-price="<?= $package['price'] ?>">
                    <div class="card h-100 shadow-sm package-card">
                        <?php if (!empty($package['image_url'])): ?>
                            <img src="<?= base_url($package['image_url']) ?>" class="card-img-top" alt="<?= $package['package_name'] ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <img src="<?= base_url('images/package-placeholder.jpg') ?>" class="card-img-top" alt="Package placeholder" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary"><?= $package['duration'] ?></span>
                                <span class="badge bg-success"><?= ucfirst($package['island_slug']) ?></span>
                            </div>
                            
                            <h5 class="card-title fw-bold"><?= $package['package_name'] ?></h5>
                            <p class="card-text"><?= substr(strip_tags($package['description']), 0, 100) ?>...</p>
                            
                            <div class="package-features mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-user-friends me-1"></i> Max 10 orang
                                </small>
                                <small class="text-muted ms-3">
                                    <i class="fas fa-clock me-1"></i> <?= $package['duration'] ?>
                                </small>
                            </div>
                            
                            <h4 class="text-primary fw-bold mb-3">Rp <?= number_format($package['price'], 0, ',', '.') ?></h4>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('tour/detail/' . ($package['slug'] ?? '')) ?>" class="btn btn-primary">Detail Paket</a>
                                <a href="<?= base_url('boats') ?>" class="btn btn-outline-primary">Pesan Sekarang</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-suitcase-rolling fa-4x text-muted mb-3"></i>
                    <h3>Belum Ada Paket Wisata</h3>
                    <p class="text-muted">Paket wisata sedang dalam persiapan. Silakan kembali lagi nanti.</p>
                    <a href="<?= base_url('contact') ?>" class="btn btn-primary">Hubungi Kami</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Consultation CTA -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card bg-light text-center">
                    <div class="card-body py-5">
                        <h3 class="fw-bold mb-3">Butuh Paket Kustom?</h3>
                        <p class="lead mb-4">Kami dapat membuat paket wisata khusus sesuai kebutuhan dan budget Anda</p>
                        <a href="<?= base_url('contact') ?>" class="btn btn-primary btn-lg">Konsultasi Gratis</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->include('templates/footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('packageFilter');
    const packageItems = document.querySelectorAll('.package-item');
    
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const islandFilter = document.getElementById('islandFilter').value;
        const durationFilter = document.getElementById('durationFilter').value;
        const budgetFilter = document.getElementById('budgetFilter').value;
        
        packageItems.forEach(item => {
            let show = true;
            const island = item.getAttribute('data-island');
            const duration = item.getAttribute('data-duration');
            const price = parseFloat(item.getAttribute('data-price'));
            
            // Island filter
            if (islandFilter && island !== islandFilter.toLowerCase()) {
                show = false;
            }
            
            // Duration filter
            if (durationFilter && !duration.includes(durationFilter)) {
                show = false;
            }
            
            // Budget filter
            if (budgetFilter) {
                if (budgetFilter === 'low' && price > 500000) {
                    show = false;
                } else if (budgetFilter === 'medium' && (price <= 500000 || price > 1500000)) {
                    show = false;
                } else if (budgetFilter === 'high' && price <= 1500000) {
                    show = false;
                }
            }
            
            if (show) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>