<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<section class="booking-form mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Pesan Kapal Sekarang</h3>
        </div>
        <div class="card-body">
            <form action="<?= base_url('booking/create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="boat_id" class="form-label">Pilih Kapal</label>
                        <select class="form-select" id="boat_id" name="boat_id" required>
                            <option value="" selected disabled>Pilih Kapal</option>
                            <?php foreach ($boats as $boat): ?>
                                <option value="<?= $boat['id'] ?>">
                                    <?= $boat['boat_name'] ?> (<?= $boat['boat_type'] ?>, Kapasitas: <?= $boat['capacity'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="passenger_count" class="form-label">Jumlah Penumpang</label>
                        <input type="number" class="form-control" id="passenger_count" name="passenger_count" min="1" required>
                    </div>
                </div>
                
                <!-- Form lainnya -->
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Pesan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</section>

<h3 class="mb-4">Riwayat Pemesanan Anda</h3>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID Booking</th>
                <th>Kapal</th>
                <th>Jumlah Penumpang</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= $booking['id'] ?></td>
                    <td><?= $booking['boat_name'] ?? '-' ?></td>
                    <td><?= $booking['passenger_count'] ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $booking['booking_status'] === 'confirmed' ? 'success' : 
                            ($booking['booking_status'] === 'pending' ? 'warning' : 'secondary') 
                        ?>">
                            <?= ucfirst($booking['booking_status']) ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($booking['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>