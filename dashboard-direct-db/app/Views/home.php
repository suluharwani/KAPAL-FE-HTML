<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">
    <!-- Hero Section -->
    <div class="hero-section position-relative">
        <div class="hero-image" style="background-image: url('<?= base_url('assets/img/hero-boat.jpg') ?>');"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content text-center text-white">
            <h1 class="display-4 fw-bold mb-4">Sewa Kapal di Raja Ampat</h1>
            <p class="lead mb-5">Nikmati perjalanan tak terlupakan dengan kapal terbaik kami</p>
            
            <!-- Booking Form -->
            <div class="booking-form-container">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <h3 class="card-title text-dark mb-4">Cari Kapal</h3>
                        <form action="<?= base_url('schedules') ?>" method="get">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="departure" class="form-label">Asal</label>
                                    <select class="form-select" id="departure" name="departure" required>
                                        <option value="" selected disabled>Pilih Pulau Asal</option>
                                        <?php foreach ($islands as $island): ?>
                                            <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="arrival" class="form-label">Tujuan</label>
                                    <select class="form-select" id="arrival" name="arrival" required>
                                        <option value="" selected disabled>Pilih Pulau Tujuan</option>
                                        <?php foreach ($islands as $island): ?>
                                            <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Tanggal Berangkat</label>
                                    <input type="date" class="form-control" id="date" name="date" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="passengers" class="form-label">Jumlah Penumpang</label>
                                    <input type="number" class="form-control" id="passengers" name="passengers" min="1" value="1" required>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>CARI KAPAL
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Boats Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Kapal Unggulan Kami</h2>
                <p class="section-subtitle">Pilih kapal terbaik untuk perjalanan Anda</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($featuredBoats as $boat): ?>
                <div class="col-md-4">
                    <div class="card boat-card h-100">
                        <div class="boat-badge"><?= strtoupper($boat['boat_type']) ?></div>
                        <img src="<?= base_url($boat['image_url'] ?? 'assets/img/boat-default.jpg') ?>" class="card-img-top" alt="<?= $boat['boat_name'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $boat['boat_name'] ?></h5>
                            <div class="boat-features mb-3">
                                <span><i class="fas fa-users me-1"></i> <?= $boat['capacity'] ?> Penumpang</span>
                                <span><i class="fas fa-tag me-1"></i> Rp <?= number_format($boat['price_per_trip'], 0, ',', '.') ?>/trip</span>
                            </div>
                            <p class="card-text"><?= $boat['description'] ?? 'Kapal nyaman dengan fasilitas lengkap.' ?></p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="<?= base_url('boats/'.$boat['boat_id']) ?>" class="btn btn-outline-primary w-100">
                                <i class="fas fa-info-circle me-2"></i>Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= base_url('boats') ?>" class="btn btn-primary px-4">
                    Lihat Semua Kapal <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Cara Memesan</h2>
                <p class="section-subtitle">Pesan kapal hanya dalam 4 langkah mudah</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">1</span>
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="step-title mt-3">Cari Kapal</h5>
                        <p class="step-text">Temukan kapal yang sesuai dengan kebutuhan Anda</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">2</span>
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h5 class="step-title mt-3">Pilih Jadwal</h5>
                        <p class="step-text">Pilih tanggal dan waktu keberangkatan</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">3</span>
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h5 class="step-title mt-3">Isi Data</h5>
                        <p class="step-text">Lengkapi data penumpang dan pemesanan</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="step-card text-center">
                        <div class="step-icon">
                            <span class="step-number">4</span>
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h5 class="step-title mt-3">Pembayaran</h5>
                        <p class="step-text">Lakukan pembayaran dan dapatkan konfirmasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Apa Kata Pelanggan?</h2>
                <p class="section-subtitle">Testimonial dari pelanggan yang menggunakan layanan kami</p>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="testimonial-slider">
                        <?php foreach ($testimonials as $testimonial): ?>
                        <div class="testimonial-item">
                            <div class="testimonial-content">
                                <div class="testimonial-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= $testimonial['rating'] ? 'text-warning' : 'text-secondary' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="testimonial-text">"<?= $testimonial['content'] ?>"</p>
                                <div class="testimonial-author">
                                    <strong><?= $testimonial['user_id'] ? $testimonial['user_name'] : $testimonial['guest_name'] ?></strong>
                                    <?php if ($testimonial['guest_email']): ?>
                                        <span><?= $testimonial['guest_email'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Open Trip Section -->
    <section class="py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Open Trip Available</h2>
                <p class="section-subtitle">Bergabunglah dengan open trip kami yang akan datang</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($openTrips as $trip): ?>
                <div class="col-md-4">
                    <div class="card trip-card h-100">
                        <div class="trip-badge">OPEN TRIP</div>
                        <img src="<?= base_url($trip['boat_image'] ?? 'assets/img/trip-default.jpg') ?>" class="card-img-top" alt="<?= $trip['boat_name'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $trip['departure_island'] ?> ke <?= $trip['arrival_island'] ?></h5>
                            <div class="trip-meta mb-2">
                                <span><i class="fas fa-ship me-1"></i> <?= $trip['boat_name'] ?></span>
                                <span><i class="fas fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($trip['departure_date'])) ?></span>
                            </div>
                            <div class="trip-price mb-3">
                                <span class="price">Rp <?= number_format($trip['price_per_trip'], 0, ',', '.') ?></span>
                                <span class="text-muted">/orang</span>
                            </div>
                            <div class="trip-seats">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= (($trip['max_passengers'] - $trip['available_seats']) / $trip['max_passengers']) * 100 ?>%" 
                                         aria-valuenow="<?= $trip['max_passengers'] - $trip['available_seats'] ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="<?= $trip['max_passengers'] ?>">
                                    </div>
                                </div>
                                <small><?= $trip['max_passengers'] - $trip['available_seats'] ?> dari <?= $trip['max_passengers'] ?> kursi terisi</small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="<?= base_url('open-trip/'.$trip['open_trip_id']) ?>" class="btn btn-primary w-100">
                                <i class="fas fa-ticket-alt me-2"></i>Bergabung
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= base_url('open-trip') ?>" class="btn btn-outline-primary px-4">
                    Lihat Semua Open Trip <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Hero Section */
    .hero-section {
        height: 80vh;
        min-height: 600px;
        margin-bottom: 50px;
    }
    .hero-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        z-index: 1;
    }
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 2;
    }
    .hero-content {
        position: relative;
        z-index: 3;
        padding-top: 120px;
    }
    .booking-form-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    /* Boat Cards */
    .boat-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
    }
    .boat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .boat-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        z-index: 2;
    }
    .boat-card img {
        height: 200px;
        object-fit: cover;
    }
    .boat-features {
        display: flex;
        justify-content: space-between;
    }
    
    /* How It Works */
    .step-card {
        padding: 20px;
        height: 100%;
    }
    .step-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        color: var(--bs-primary);
        font-size: 24px;
    }
    .step-number {
        position: absolute;
        top: -10px;
        right: -10px;
        background: var(--bs-primary);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }
    .step-title {
        font-weight: 600;
    }
    
    /* Testimonials */
    .testimonial-slider {
        max-width: 1000px;
        margin: 0 auto;
    }
    .testimonial-item {
        padding: 0 15px;
    }
    .testimonial-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .testimonial-rating {
        margin-bottom: 15px;
    }
    .testimonial-text {
        font-style: italic;
        margin-bottom: 20px;
    }
    .testimonial-author {
        font-weight: 600;
    }
    .testimonial-author span {
        display: block;
        font-weight: normal;
        font-size: 14px;
        color: #6c757d;
    }
    
    /* Open Trip Cards */
    .trip-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
    }
    .trip-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .trip-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--bs-primary);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        z-index: 2;
    }
    .trip-card img {
        height: 180px;
        object-fit: cover;
    }
    .trip-meta {
        font-size: 14px;
        color: #6c757d;
    }
    .trip-meta span {
        display: block;
        margin-bottom: 5px;
    }
    .trip-price .price {
        font-size: 20px;
        font-weight: bold;
        color: var(--bs-primary);
    }
    .trip-seats {
        margin-top: 15px;
    }
    .trip-seats .progress {
        height: 6px;
        margin-bottom: 5px;
    }
    
    /* Section Styling */
    .section-header {
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }
    .section-title {
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }
    .section-title:after {
        content: '';
        position: absolute;
        width: 50px;
        height: 3px;
        background: var(--bs-primary);
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
    }
    .section-subtitle {
        color: #6c757d;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .hero-section {
            height: auto;
            min-height: 500px;
        }
        .hero-content {
            padding-top: 80px;
            padding-bottom: 80px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize testimonial slider
    $('.testimonial-slider').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true,
        autoplay: true,
        autoplaySpeed: 5000
    });
    
    // Set minimum date for date picker to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').min = today;
    
    // Handle destination select based on departure
    $('#departure').change(function() {
        const departureId = $(this).val();
        $('#arrival').html('<option value="" selected disabled>Pilih Pulau Tujuan</option>');
        
        if (departureId) {
            // In a real app, you would fetch this via AJAX
            const destinations = <?= json_encode($routes) ?>;
            const filtered = destinations.filter(r => r.departure_island_id == departureId);
            
            filtered.forEach(route => {
                const island = <?= json_encode($islands) ?>.find(i => i.island_id == route.arrival_island_id);
                if (island) {
                    $('#arrival').append(`<option value="${island.island_id}">${island.island_name}</option>`);
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>