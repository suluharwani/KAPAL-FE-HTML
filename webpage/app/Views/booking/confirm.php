<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="booking-confirm">
    <div class="container">
        <h1 class="section-title">Konfirmasi Pemesanan</h1>
        
        <div class="booking-summary">
            <h2>Ringkasan Pemesanan</h2>
            
            <div class="summary-item">
                <span>Dari:</span>
                <span><?= ucfirst($booking['from']) ?></span>
            </div>
            
            <div class="summary-item">
                <span>Ke:</span>
                <span><?= ucfirst($booking['to']) ?></span>
            </div>
            
            <div class="summary-item">
                <span>Tanggal:</span>
                <span><?= date('d F Y', strtotime($booking['date'])) ?></span>
            </div>
            
            <div class="summary-item">
                <span>Jumlah Penumpang:</span>
                <span><?= $booking['passengers'] ?></span>
            </div>
            
            <div class="summary-item">
                <span>Tipe Kapal:</span>
                <span><?= ucfirst($booking['boat_type']) ?></span>
            </div>
            
            <div class="summary-item">
                <span>Tipe Perjalanan:</span>
                <span><?= isset($booking['round_trip']) ? 'Pulang-Pergi' : 'Sekali Jalan' ?></span>
            </div>
            
            <div class="summary-total">
                <span>Total Biaya:</span>
                <span>Rp 3.500.000</span>
            </div>
            
            <div class="booking-actions">
                <a href="<?= base_url('/booking') ?>" class="btn btn-outline">Kembali</a>
                <button class="btn btn-primary">Konfirmasi & Bayar</button>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>