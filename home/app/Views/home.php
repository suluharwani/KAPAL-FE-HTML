<!-- About Hero Section -->
<section class="about-hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Selamat Datang di Raja Ampat Boat Services</h1>
        <p class="lead">Layanan Transportasi Kapal Terbaik di Kepulauan Raja Ampat</p>
    </div>
</section>

<!-- Main Content -->
<main class="container my-5">
    <!-- Image Slider -->
    <section class="mb-5">
        <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($sliders as $key => $slider): ?>
                    <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="<?= $key ?>" <?= $key === 0 ? 'class="active"' : '' ?>></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner rounded-3">
                <?php foreach ($sliders as $key => $slider): ?>
                    <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                        <img src="<?= base_url('uploads/sliders/' . $slider['image']) ?>" class="d-block w-100" alt="<?= $slider['title'] ?>">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                            <h5><?= $slider['title'] ?></h5>
                            <p><?= $slider['description'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <!-- Quick Booking Section -->
    <section class="booking-form mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Pesan Kapal Sekarang</h3>
            </div>
            <div class="card-body">
                <form id="quickBookingForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quickFrom" class="form-label">Dari Pulau</label>
                            <select class="form-select" id="quickFrom" required>
                                <option value="" selected disabled>Pilih Pulau Asal</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quickTo" class="form-label">Ke Pulau</label>
                            <select class="form-select" id="quickTo" required>
                                <option value="" selected disabled>Pilih Pulau Tujuan</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quickDate" class="form-label">Tanggal Keberangkatan</label>
                            <input type="date" class="form-control" id="quickDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quickPassengers" class="form-label">Jumlah Penumpang</label>
                            <input type="number" class="form-control" id="quickPassengers" min="1" max="20" required>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <?php if (session()->get('isLoggedIn')): ?>
                            <button type="submit" class="btn btn-primary btn-lg">Cek Jadwal & Harga</button>
                        <?php else: ?>
                            <a href="<?= base_url('auth/login') ?>" class="btn btn-primary btn-lg">Login untuk Memesan</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section mb-5">
        <h2 class="text-center mb-4">Mengapa Memilih Kami?</h2>
        <div class="row text-center">
            <?php foreach ($features as $feature): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon mb-3">
                                <i class="<?= $feature['icon'] ?> fa-3x text-primary"></i>
                            </div>
                            <h3><?= $feature['title'] ?></h3>
                            <p><?= $feature['description'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Popular Routes Section -->
    <section class="routes-section mb-5">
        <h2 class="text-center mb-4">Rute Populer</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Rute</th>
                        <th>Jadwal</th>
                        <th>Durasi</th>
                        <th>Harga Kapal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($popularRoutes as $route): ?>
                        <tr>
                            <td><?= $route['departure_island'] ?> - <?= $route['arrival_island'] ?></td>
                            <td><?= $route['schedule'] ?></td>
                            <td><?= $route['duration'] ?></td>
                            <td>Rp <?= number_format($route['price'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section mb-5 py-4 bg-primary text-white rounded text-center">
        <h3 class="mb-3">Siap Memulai Perjalanan Anda?</h3>
        <p class="lead mb-4">Pesan kapal sekarang dan nikmati pengalaman tak terlupakan di Raja Ampat</p>
        <?php if (session()->get('isLoggedIn')): ?>
            <a href="<?= base_url('boats') ?>" class="btn btn-light btn-lg">Pesan Sekarang</a>
        <?php else: ?>
            <a href="<?= base_url('auth/register') ?>" class="btn btn-light btn-lg">Daftar Sekarang</a>
        <?php endif; ?>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section mb-5">
        <h2 class="text-center mb-4">Apa Kata Pelanggan Kami?</h2>
        <div class="row">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?= base_url('uploads/testimonials/' . $testimonial['image']) ?>" class="rounded-circle me-3" width="60" height="60" alt="<?= $testimonial['guest_name'] ?>">
                                <div>
                                    <h5 class="mb-0"><?= $testimonial['guest_name'] ?></h5>
                                    <div class="text-warning">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
    <?php if ($i < floor($testimonial['rating'])): ?>
        <i class="fas fa-star"></i> <!-- bintang penuh -->
    <?php elseif ($i < $testimonial['rating']): ?>
        <i class="fas fa-star-half-alt"></i> <!-- setengah bintang -->
    <?php else: ?>
        <i class="far fa-star"></i> <!-- bintang kosong -->
    <?php endif; ?>
<?php endfor; ?>

                                    </div>
                                </div>
                            </div>
                            <p class="card-text">"<?= $testimonial['content'] ?>"</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick booking form handling
    const quickBookingForm = document.getElementById('quickBookingForm');
    if (quickBookingForm) {
        quickBookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const fromIsland = document.getElementById('quickFrom').value;
            const toIsland = document.getElementById('quickTo').value;
            const departureDate = document.getElementById('quickDate').value;
            const passengers = document.getElementById('quickPassengers').value;
            
            if (fromIsland === toIsland) {
                alert('Pulau tujuan tidak boleh sama dengan pulau asal');
                return;
            }
            
            // Redirect to boats page with parameters if logged in
            if (<?= session()->get('isLoggedIn') ? 'true' : 'false' ?>) {
                window.location.href = `<?= base_url('boats') ?>?from=${fromIsland}&to=${toIsland}&date=${departureDate}&passengers=${passengers}`;
            }
        });
    }
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('quickDate').min = today;
    
    // Prevent selecting same island for from and to
    const quickFrom = document.getElementById('quickFrom');
    const quickTo = document.getElementById('quickTo');
    
    if (quickFrom && quickTo) {
        quickFrom.addEventListener('change', function() {
            if (this.value === quickTo.value) {
                quickTo.value = '';
            }
        });
        
        quickTo.addEventListener('change', function() {
            if (this.value === quickFrom.value) {
                alert('Pulau tujuan tidak boleh sama dengan pulau asal');
                this.value = '';
            }
        });
    }
});
</script>