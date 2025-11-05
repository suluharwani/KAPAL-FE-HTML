<?php
// app/Views/booking/my_bookings.php
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Pemesanan Saya</h1>
                <a href="<?= base_url('boats/schedule') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Pesan Kapal Baru
                </a>
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

            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4" id="bookingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                        Mendatang
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                        Selesai
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="canceled-tab" data-bs-toggle="tab" data-bs-target="#canceled" type="button" role="tab" aria-controls="canceled" aria-selected="false">
                        Dibatalkan
                    </button>
                </li>
            </ul>

            <!-- Booking Content -->
            <div class="tab-content" id="bookingTabsContent">
                <!-- Upcoming Bookings -->
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                    <?php if (!empty($upcoming_bookings)): ?>
                        <div class="row">
                            <?php foreach ($upcoming_bookings as $booking): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 booking-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?= 
                                                $booking['booking_status'] === 'confirmed' ? 'success' : 
                                                ($booking['booking_status'] === 'pending' ? 'warning' : 'secondary')
                                            ?>">
                                                <?= strtoupper($booking['booking_status']) ?>
                                            </span>
                                            <small class="text-muted"><?= $booking['booking_code'] ?></small>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($booking['boat_name']) ?></h5>
                                            <p class="card-text">
                                                <i class="fas fa-route me-2 text-primary"></i>
                                                <?= esc($booking['departure_island']) ?> → <?= esc($booking['arrival_island']) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-calendar me-2 text-primary"></i>
                                                <?= date('d M Y', strtotime($booking['departure_date'])) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-clock me-2 text-primary"></i>
                                                <?= date('H:i', strtotime($booking['departure_time'])) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-users me-2 text-primary"></i>
                                                <?= $booking['passenger_count'] ?> Penumpang
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                                                Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-grid gap-2">
                                                <a href="<?= base_url('booking/detail/' . $booking['booking_code']) ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                                <?php if ($booking['booking_status'] === 'pending' || $booking['booking_status'] === 'confirmed'): ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm cancel-booking" data-booking-id="<?= $booking['booking_id'] ?>">
                                                        <i class="fas fa-times me-1"></i> Batalkan
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada pemesanan mendatang</h5>
                            <p class="text-muted">Anda belum memiliki pemesanan yang akan datang.</p>
                            <a href="<?= base_url('boats/schedule') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>Pesan Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Completed Bookings -->
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <?php if (!empty($completed_bookings)): ?>
                        <div class="row">
                            <?php foreach ($completed_bookings as $booking): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 booking-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success">SELESAI</span>
                                            <small class="text-muted"><?= $booking['booking_code'] ?></small>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($booking['boat_name']) ?></h5>
                                            <p class="card-text">
                                                <i class="fas fa-route me-2 text-primary"></i>
                                                <?= esc($booking['departure_island']) ?> → <?= esc($booking['arrival_island']) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-calendar me-2 text-primary"></i>
                                                <?= date('d M Y', strtotime($booking['departure_date'])) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-users me-2 text-primary"></i>
                                                <?= $booking['passenger_count'] ?> Penumpang
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                                                Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-grid gap-2">
                                                <a href="<?= base_url('booking/detail/' . $booking['booking_code']) ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                                <a href="<?= base_url('booking/print/' . $booking['booking_code']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-print me-1"></i> Cetak Tiket
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada pemesanan selesai</h5>
                            <p class="text-muted">Anda belum memiliki pemesanan yang telah selesai.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Canceled Bookings -->
                <div class="tab-pane fade" id="canceled" role="tabpanel" aria-labelledby="canceled-tab">
                    <?php if (!empty($canceled_bookings)): ?>
                        <div class="row">
                            <?php foreach ($canceled_bookings as $booking): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 booking-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span class="badge bg-danger">DIBATALKAN</span>
                                            <small class="text-muted"><?= $booking['booking_code'] ?></small>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($booking['boat_name']) ?></h5>
                                            <p class="card-text">
                                                <i class="fas fa-route me-2 text-primary"></i>
                                                <?= esc($booking['departure_island']) ?> → <?= esc($booking['arrival_island']) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-calendar me-2 text-primary"></i>
                                                <?= date('d M Y', strtotime($booking['departure_date'])) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-users me-2 text-primary"></i>
                                                <?= $booking['passenger_count'] ?> Penumpang
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                                                Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-grid">
                                                <a href="<?= base_url('booking/detail/' . $booking['booking_code']) ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-ban fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada pemesanan yang dibatalkan</h5>
                            <p class="text-muted">Anda belum membatalkan pemesanan apapun.</p>
                        </div>
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

<style>
.booking-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e9ecef;
}

.booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 3px solid #0d6efd;
    border-top: none;
    border-left: none;
    border-right: none;
    background: transparent;
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
    
    // Tab persistence
    const bookingTabs = document.getElementById('bookingTabs');
    const storedTab = localStorage.getItem('bookingActiveTab');
    
    if (storedTab) {
        const tabTrigger = new bootstrap.Tab(document.querySelector(storedTab));
        tabTrigger.show();
    }
    
    bookingTabs.addEventListener('click', function(e) {
        if (e.target.classList.contains('nav-link')) {
            const tabId = e.target.getAttribute('data-bs-target');
            localStorage.setItem('bookingActiveTab', tabId);
        }
    });
});
</script>