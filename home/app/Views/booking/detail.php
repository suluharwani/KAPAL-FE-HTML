<?php
// app/Views/booking/detail.php

// Cek jika booking tidak ditemukan
if (empty($booking)) {
    echo '<div class="container my-5">
        <div class="alert alert-danger text-center">
            <h4>Pemesanan Tidak Ditemukan</h4>
            <p>Pemesanan yang Anda cari tidak ditemukan atau tidak dapat diakses.</p>
            <a href="' . base_url('booking/my-bookings') . '" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Pemesanan Saya
            </a>
        </div>
    </div>';
    return;
}
?>

<div class="container my-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('booking/my-bookings') ?>">Pemesanan Saya</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Pemesanan</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Detail Pemesanan</h1>
            <small class="text-muted">Kode: <?= $booking['booking_code'] ?></small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('booking/print/' . $booking['booking_code']) ?>" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-print me-2"></i>Cetak Tiket
            </a>
            <a href="<?= base_url('booking/my-bookings') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Informasi Utama -->
        <div class="col-lg-8">
            <!-- Card Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-<?= 
                                $booking['booking_status'] === 'confirmed' ? 'success' : 
                                ($booking['booking_status'] === 'pending' ? 'warning' : 
                                ($booking['booking_status'] === 'paid' ? 'primary' : 
                                ($booking['booking_status'] === 'completed' ? 'info' : 'danger')))
                            ?> fs-6">
                                <?= strtoupper($booking['booking_status']) ?>
                            </span>
                            <span class="badge bg-<?= 
                                $booking['payment_status'] === 'paid' ? 'success' : 
                                ($booking['payment_status'] === 'partial' ? 'info' : 'warning')
                            ?> ms-2 fs-6">
                                Pembayaran: <?= strtoupper($booking['payment_status']) ?>
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Dibuat: <?= date('d M Y H:i', strtotime($booking['created_at'])) ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Perjalanan -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Informasi Perjalanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kapal</label>
                                <p class="mb-0"><?= esc($booking['boat_name']) ?> (<?= $booking['boat_type'] ?>)</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rute</label>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <?= esc($booking['departure_island']) ?> 
                                    <i class="fas fa-arrow-right mx-2 text-primary"></i>
                                    <i class="fas fa-map-marker-alt text-success me-2"></i>
                                    <?= esc($booking['arrival_island']) ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal & Waktu</label>
                                <p class="mb-0">
                                    <i class="fas fa-calendar me-2 text-primary"></i>
                                    <?= date('d M Y', strtotime($booking['departure_date'])) ?>
                                    <i class="fas fa-clock mx-2 text-primary"></i>
                                    <?= date('H:i', strtotime($booking['departure_time'])) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah Penumpang</label>
                                <p class="mb-0">
                                    <i class="fas fa-users me-2 text-primary"></i>
                                    <?= $booking['passenger_count'] ?> orang
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipe Pemesanan</label>
                                <p class="mb-0">
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    <?= $booking['is_open_trip'] ? 'Open Trip' : 'Private Trip' ?>
                                    <?php if ($booking['is_open_trip'] && !empty($booking['open_trip_type'])): ?>
                                        <span class="badge bg-info ms-2"><?= ucfirst($booking['open_trip_type']) ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Durasi Perkiraan</label>
                                <p class="mb-0">
                                    <i class="fas fa-hourglass-half me-2 text-primary"></i>
                                    <?= $booking['estimated_duration'] ?> jam
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Pembayaran -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Total Harga</label>
                                <p class="mb-0 fs-5 text-success fw-bold">
                                    Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Metode Pembayaran</label>
                                <p class="mb-0">
                                    <i class="fas fa-wallet me-2 text-primary"></i>
                                    <?= $booking['payment_method'] === 'transfer' ? 'Transfer Bank' : 'Tunai' ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Pembayaran</label>
                                <p class="mb-0">
                                    <span class="badge bg-<?= 
                                        $booking['payment_status'] === 'paid' ? 'success' : 
                                        ($booking['payment_status'] === 'partial' ? 'info' : 'warning')
                                    ?>">
                                        <?= strtoupper($booking['payment_status']) ?>
                                    </span>
                                </p>
                            </div>
                            <?php if ($booking['payment_method'] === 'transfer' && $booking['payment_status'] !== 'paid'): ?>
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Instruksi Pembayaran</h6>
                                    <p class="mb-1">Silakan transfer ke rekening berikut:</p>
                                    <p class="mb-1 fw-bold">BANK BCA - 1234567890</p>
                                    <p class="mb-1">a.n. RAJA AMPAT BOAT SERVICES</p>
                                    <p class="mb-0">Jumlah: Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Daftar Penumpang -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Daftar Penumpang</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($passengers)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>No. Telepon</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($passengers as $passenger): ?>
                                        <tr>
                                            <td><?= esc($passenger['full_name']) ?></td>
                                            <td><?= esc($passenger['phone'] ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $passenger['status'] === 'confirmed' ? 'success' : 'warning'
                                                ?>">
                                                    <?= strtoupper($passenger['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($passenger['status'] !== 'confirmed' && in_array($booking['booking_status'], ['pending', 'confirmed'])): ?>
                                                    <button class="btn btn-sm btn-outline-success confirm-passenger" data-passenger-id="<?= $passenger['passenger_id'] ?>">
                                                        <i class="fas fa-check me-1"></i>Konfirmasi
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Tidak ada data penumpang.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Catatan -->
            <?php if (!empty($booking['notes'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">Catatan</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($booking['notes'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar - Aksi -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- Cetak Tiket -->
                        <a href="<?= base_url('booking/print/' . $booking['booking_code']) ?>" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-print me-2"></i>Cetak Tiket
                        </a>

                        <!-- Bagikan -->
                        <button class="btn btn-outline-info share-booking" data-booking-code="<?= $booking['booking_code'] ?>">
                            <i class="fas fa-share-alt me-2"></i>Bagikan
                        </button>

                        <!-- Batalkan -->
                        <?php if (in_array($booking['booking_status'], ['pending', 'confirmed'])): ?>
                            <button class="btn btn-outline-danger cancel-booking" data-booking-id="<?= $booking['booking_id'] ?>">
                                <i class="fas fa-times me-2"></i>Batalkan Pemesanan
                            </button>
                        <?php endif; ?>

                        <!-- Hubungi Admin -->
                        <a href="https://wa.me/6281327341834?text=Halo,%20saya%20membutuhkan%20bantuan%20untuk%20pemesanan%20<?= $booking['booking_code'] ?>" 
                           target="_blank" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>Hubungi Admin
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informasi Kontak -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Kontak Darurat</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="fas fa-phone-alt me-2 text-primary"></i>
                        <strong>Customer Service:</strong> +62 813-2734-1834
                    </p>
                    <p class="mb-2">
                        <i class="fab fa-whatsapp me-2 text-success"></i>
                        <strong>WhatsApp:</strong> +62 813-2734-1834
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <strong>Email:</strong> muramuma67@gmail.com
                    </p>
                </div>
            </div>

            <!-- Status Check-in -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">Status Check-in</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="fas fa-user-check me-2 text-primary"></i>
                        <strong>Terkonfirmasi:</strong> <?= $booking['checkin_count'] ?> / <?= $booking['passenger_count'] ?> penumpang
                    </p>
                    <?php if ($booking['checkin_time']): ?>
                        <p class="mb-0">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            <strong>Waktu Check-in:</strong> <?= date('d M Y H:i', strtotime($booking['checkin_time'])) ?>
                        </p>
                    <?php else: ?>
                        <p class="mb-0 text-muted">Belum melakukan check-in</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">Batalkan Pemesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelBookingForm" action="<?= base_url('booking/cancel') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" id="cancel_booking_id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pemesanan ini?</p>
                    <div class="form-group">
                        <label for="cancel_reason" class="form-label">Alasan Pembatalan (opsional)</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" placeholder="Masukkan alasan pembatalan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan Pemesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Passenger Modal -->
<div class="modal fade" id="confirmPassengerModal" tabindex="-1" aria-labelledby="confirmPassengerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPassengerModalLabel">Konfirmasi Penumpang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="confirmPassengerForm" action="<?= base_url('booking/confirmPassenger') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="passenger_id" id="confirm_passenger_id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin mengkonfirmasi penumpang ini?</p>
                    <p class="text-muted">Penumpang yang sudah dikonfirmasi tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card-header {
    border-bottom: none;
}
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 0.5rem;
}
.share-booking:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cancel booking modal
    const cancelButtons = document.querySelectorAll('.cancel-booking');
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
    const cancelBookingId = document.getElementById('cancel_booking_id');
    
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');
            cancelBookingId.value = bookingId;
            cancelModal.show();
        });
    });
    
    // Confirm passenger modal
    const confirmButtons = document.querySelectorAll('.confirm-passenger');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmPassengerModal'));
    const confirmPassengerId = document.getElementById('confirm_passenger_id');
    
    confirmButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passengerId = this.getAttribute('data-passenger-id');
            confirmPassengerId.value = passengerId;
            confirmModal.show();
        });
    });
    
    // Share booking functionality
    const shareButtons = document.querySelectorAll('.share-booking');
    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingCode = this.getAttribute('data-booking-code');
            const shareUrl = `${window.location.origin}${window.location.pathname}?ref=${bookingCode}`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Detail Pemesanan Kapal Raja Ampat',
                    text: `Lihat detail pemesanan kapal saya dengan kode: ${bookingCode}`,
                    url: shareUrl
                })
                .catch(console.error);
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(shareUrl)
                    .then(() => {
                        alert('Link berhasil disalin ke clipboard!');
                    })
                    .catch(err => {
                        console.error('Error copying text: ', err);
                    });
            }
        });
    });
});
</script>