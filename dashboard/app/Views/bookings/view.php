<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Detail Pemesanan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Pemesanan #<?= esc($booking['booking_code']) ?></h5>
                <div>
                    <a href="<?= base_url('bookings/invoice/' . $booking['id']) ?>" class="btn btn-sm btn-secondary">
                        <i class="bi bi-receipt"></i> Invoice
                    </a>
                    <?php if ($booking['status'] === 'pending'): ?>
                        <a href="#" class="btn btn-sm btn-danger" 
   onclick="cancelBooking(<?= $booking['id'] ?>)"
   <?= $booking['status'] !== 'pending' ? 'disabled' : '' ?>>
    <i class="bi bi-x-circle"></i> Batalkan
</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Informasi Perjalanan</h6>
                        <p>
                            <strong>Kapal:</strong> <?= esc($booking['boat_name']) ?><br>
                            <strong>Rute:</strong> <?= esc($booking['route_name']) ?><br>
                            <strong>Tanggal:</strong> <?= date('d M Y', strtotime($booking['departure_date'])) ?><br>
                            <strong>Waktu:</strong> <?= $booking['departure_time'] ?><br>
                            <strong>Durasi:</strong> <?= esc($booking['estimated_duration']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Detail Pemesanan</h6>
                        <p>
                            <strong>Status:</strong> 
                            <?php 
                            $badgeClass = [
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'cancelled' => 'danger',
                                'completed' => 'primary'
                            ][$booking['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span><br>
                            <strong>Tanggal Pemesanan:</strong> <?= date('d M Y H:i', strtotime($booking['created_at'])) ?><br>
                            <strong>Metode Pembayaran:</strong> <?= ucfirst($booking['payment_method']) ?><br>
                            <strong>Total Pembayaran:</strong> Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                        </p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6>Catatan</h6>
                    <p><?= !empty($booking['notes']) ? esc($booking['notes']) : 'Tidak ada catatan' ?></p>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Daftar Penumpang (<?= count($booking['passengers']) ?> orang)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Lengkap</th>
                                        <th>No. Identitas</th>
                                        <th>Telepon</th>
                                        <th>Usia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking['passengers'] as $index => $passenger): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= esc($passenger['full_name']) ?></td>
                                            <td><?= esc($passenger['identity_number']) ?></td>
                                            <td><?= esc($passenger['phone']) ?></td>
                                            <td><?= esc($passenger['age']) ?> tahun</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($booking['payment'])): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Pembayaran</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Status Pembayaran:</strong> 
                                        <?php 
                                        $paymentBadgeClass = [
                                            'pending' => 'warning',
                                            'verified' => 'success',
                                            'rejected' => 'danger'
                                        ][$booking['payment']['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $paymentBadgeClass ?>">
                                            <?= ucfirst($booking['payment']['status']) ?>
                                        </span><br>
                                        <strong>Metode:</strong> <?= ucfirst($booking['payment']['payment_method']) ?><br>
                                        <strong>Bank:</strong> <?= esc($booking['payment']['bank_name']) ?><br>
                                        <strong>No. Rekening:</strong> <?= esc($booking['payment']['account_number']) ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <?php if (!empty($booking['payment']['receipt_url'])): ?>
                                        <img src="<?= esc($booking['payment']['receipt_url']) ?>" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 150px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
function cancelBooking(bookingId) {
    if (!confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')) return;
    
    const btn = event.target.closest('a');
    const originalHtml = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    btn.disabled = true;
    
    fetch(`<?= base_url('bookings/cancel/') ?>${bookingId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            showErrorModal(data.message);
        }
    })
    .catch(error => {
        showErrorModal('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}
</script>
<?= $this->endSection() ?>