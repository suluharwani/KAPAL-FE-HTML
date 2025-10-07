<?= $this->extend('templates/header') ?>
<?= $this->section('content') ?>

<!-- Packages Hero Section -->
<section class="packages-hero py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">Raja Ampat Tour Packages</h1>
                <p class="lead">Discover the best tour packages to explore the beauty of Raja Ampat Islands</p>
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
                        <h5 class="card-title mb-3">Filter Tour Packages</h5>
                        <form id="packageFilter">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Destination Island</label>
                                    <select class="form-select" id="islandFilter">
                                        <option value="">All Islands</option>
                                        <?php foreach ($islands as $island): ?>
                                            <option value="<?= $island['island_name'] ?>"><?= $island['island_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duration</label>
                                    <select class="form-select" id="durationFilter">
                                        <option value="">All Durations</option>
                                        <option value="1 Day">1 Day</option>
                                        <option value="2 Days">2 Days</option>
                                        <option value="3 Days">3 Days</option>
                                        <option value="4+ Days">4+ Days</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Budget</label>
                                    <select class="form-select" id="budgetFilter">
                                        <option value="">All Budgets</option>
                                        <option value="low">Rp 0 - 500,000</option>
                                        <option value="medium">Rp 500,000 - 1,500,000</option>
                                        <option value="high">Rp 1,500,000+</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Apply Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packages Grid -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">All Tour Packages</h2>
                <p class="lead">Choose the package that best fits your travel plans</p>
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
                                    <i class="fas fa-user-friends me-1"></i> Max 10 people
                                </small>
                                <small class="text-muted ms-3">
                                    <i class="fas fa-clock me-1"></i> <?= $package['duration'] ?>
                                </small>
                            </div>
                            
                            <h4 class="text-primary fw-bold mb-3">Rp <?= number_format($package['price'], 0, ',', '.') ?></h4>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('tour/detail/' . ($package['slug'] ?? '')) ?>" class="btn btn-primary">Package Details</a>
                                <a href="<?= base_url('boats') ?>" class="btn btn-outline-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-suitcase-rolling fa-4x text-muted mb-3"></i>
                    <h3>No Tour Packages Yet</h3>
                    <p class="text-muted">Tour packages are being prepared. Please check back later.</p>
                    <a href="<?= base_url('contact') ?>" class="btn btn-primary">Contact Us</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Consultation CTA -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card bg-light text-center">
                    <div class="card-body py-5">
                        <h3 class="fw-bold mb-3">Need Custom Package?</h3>
                        <p class="lead mb-4">We can create custom tour packages according to your needs and budget</p>
                        <a href="<?= base_url('contact') ?>" class="btn btn-primary btn-lg">Free Consultation</a>
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