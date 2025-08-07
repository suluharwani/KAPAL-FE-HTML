<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Selamat Datang di Raja Ampat Boat Services</h1>
        <p>Nikmati perjalanan antar pulau yang nyaman dan aman di kepulauan Raja Ampat</p>
        <a href="#booking" class="btn btn-primary">Pesan Sekarang</a>
    </div>
</section>

<!-- Booking Form Section -->
<section id="booking" class="booking-form">
    <div class="container">
        <h2 class="section-title">Pesan Kapal Sekarang</h2>
        <form action="<?= base_url('/booking') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="from">Dari Pulau</label>
                    <select id="from" name="from" class="form-control" required>
                        <option value="">Pilih Pulau Asal</option>
                        <option value="waigeo">Waigeo</option>
                        <option value="misool">Misool</option>
                        <option value="salawati">Salawati</option>
                        <option value="batanta">Batanta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="to">Ke Pulau</label>
                    <select id="to" name="to" class="form-control" required>
                        <option value="">Pilih Pulau Tujuan</option>
                        <option value="waigeo">Waigeo</option>
                        <option value="misool">Misool</option>
                        <option value="salawati">Salawati</option>
                        <option value="batanta">Batanta</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Tanggal Keberangkatan</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="passengers">Jumlah Penumpang</label>
                    <input type="number" id="passengers" name="passengers" min="1" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="boat_type">Tipe Kapal</label>
                <select id="boat_type" name="boat_type" class="form-control" required>
                    <option value="">Pilih Tipe Kapal</option>
                    <option value="speedboat">Speedboat</option>
                    <option value="ferry">Ferry</option>
                    <option value="traditional">Kapal Tradisional</option>
                </select>
            </div>
            
            <div class="form-check">
                <input type="checkbox" id="round_trip" name="round_trip" class="form-check-input">
                <label for="round_trip" class="form-check-label">Pulang-Pergi</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Cari Jadwal</button>
        </form>
    </div>
</section>

<!-- Schedule Section -->
<section class="schedule">
    <div class="container">
        <h2 class="section-title">Jadwal Kapal</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rute</th>
                        <th>Jadwal</th>
                        <th>Durasi</th>
                        <th>Harga Kapal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($featured_routes as $route): ?>
                    <tr>
                        <td><?= $route['route'] ?></td>
                        <td><?= $route['schedule'] ?></td>
                        <td><?= $route['duration'] ?></td>
                        <td><?= $route['price'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="row">
            <?php foreach ($features as $feature): ?>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="<?= $feature['icon'] ?>"></i>
                </div>
                <h3><?= $feature['title'] ?></h3>
                <p><?= $feature['description'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>