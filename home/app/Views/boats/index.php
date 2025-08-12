<!-- Main Content -->
<main class="container my-5">
    <!-- Image Slider -->
    <section class="mb-5">
        <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner rounded-3">
                <div class="carousel-item active">
                    <img src="<?= base_url('images/slider1.jpg') ?>" class="d-block w-100" alt="Raja Ampat 1">
                </div>
                <div class="carousel-item">
                    <img src="<?= base_url('images/slider2.jpg') ?>" class="d-block w-100" alt="Raja Ampat 2">
                </div>
                <div class="carousel-item">
                    <img src="<?= base_url('images/slider3.jpg') ?>" class="d-block w-100" alt="Raja Ampat 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <!-- Booking Form -->
    <section class="booking-form mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Pesan Kapal Sekarang</h3>
            </div>
            <div class="card-body">
                <form id="boatBookingForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fromIsland" class="form-label">Dari Pulau</label>
                            <select class="form-select" id="fromIsland" required>
                                <option value="" selected disabled>Pilih Pulau Asal</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="toIsland" class="form-label">Ke Pulau</label>
                            <select class="form-select" id="toIsland" required>
                                <option value="" selected disabled>Pilih Pulau Tujuan</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departureDate" class="form-label">Tanggal Keberangkatan</label>
                            <input type="date" class="form-control" id="departureDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="passengers" class="form-label">Jumlah Penumpang</label>
                            <input type="number" class="form-control" id="passengers" min="1" max="20" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="boatType" class="form-label">Tipe Kapal</label>
                        <select class="form-select" id="boatType">
                            <option value="" selected disabled>Pilih Tipe Kapal</option>
                            <option value="speedboat">Speedboat</option>
                            <option value="traditional">Kapal Tradisional</option>
                            <option value="luxury">Kapal Luxury</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="roundTrip">
                            <label class="form-check-label" for="roundTrip">
                                Pulang-Pergi
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Cek Jadwal & Harga</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section mb-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-ship fa-3x text-primary"></i>
                </div>
                <h3>Kapal Nyaman</h3>
                <p>Kapal kami dilengkapi dengan perlengkapan keselamatan dan kenyamanan penumpang.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-clock fa-3x text-primary"></i>
                </div>
                <h3>Tepat Waktu</h3>
                <p>Jadwal keberangkatan yang teratur dan tepat waktu untuk kenyamanan perjalanan Anda.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <h3>Aman Terpercaya</h3>
                <p>Dilayani oleh awak kapal profesional dengan pengalaman bertahun-tahun.</p>
            </div>
        </div>
    </section>
</main>