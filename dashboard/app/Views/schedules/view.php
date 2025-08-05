<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Detail Jadwal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Jadwal Perjalanan</h5>
                <div>
                    <a href="<?= base_url('schedules/edit/' . $schedule['id']) ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="#" class="btn btn-sm btn-danger" 
                       onclick="confirmDelete(<?= $schedule['id'] ?>)">
                        <i class="bi bi-trash"></i> Hapus
                    </a>
                    <a href="<?= base_url('schedules') ?>" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
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
                            <strong>Tanggal:</strong> <?= date('d M Y', strtotime($schedule['departure_date'])) ?><br>
                            <strong>Waktu:</strong> <?= substr($schedule['departure_time'], 0, 5) ?><br>
                            <strong>Rute:</strong> <?= esc($schedule['departure_island_name']) ?> → <?= esc($schedule['arrival_island_name']) ?><br>
                            <strong>Durasi:</strong> <?= esc($schedule['estimated_duration']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Detail Kapal</h6>
                        <p>
                            <strong>Nama Kapal:</strong> <?= esc($schedule['boat_name']) ?><br>
                            <strong>Tipe:</strong> <?= esc($schedule['boat_type']) ?><br>
                            <strong>Kapasitas:</strong> <?= $schedule['boat_capacity'] ?> kursi<br>
                            <strong>Kursi Tersedia:</strong> <?= $schedule['available_seats'] ?>
                        </p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Status</h6>
                        <p>
                            <?php 
                            $badgeClass = [
                                'available' => 'success',
                                'full' => 'danger',
                                'cancelled' => 'secondary'
                            ][$schedule['status']] ?? 'warning';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= ucfirst($schedule['status']) ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Tambahan</h6>
                        <p>
                            <strong>Dibuat pada:</strong> <?= date('d M Y H:i', strtotime($schedule['created_at'])) ?><br>
                            <strong>Diperbarui pada:</strong> <?= date('d M Y H:i', strtotime($schedule['updated_at'])) ?>
                        </p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6>Catatan</h6>
                    <p><?= !empty($schedule['notes']) ? nl2br(esc($schedule['notes'])) : 'Tidak ada catatan' ?></p>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Pemesanan Terkait</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($schedule['bookings'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Kode Booking</th>
                                            <th>Nama Customer</th>
                                            <th>Jumlah Penumpang</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($schedule['bookings'] as $booking): ?>
                                            <tr>
                                                <td>#<?= $booking['booking_code'] ?></td>
                                                <td><?= esc($booking['customer_name']) ?></td>
                                                <td><?= $booking['passenger_count'] ?></td>
                                                <td>
                                                    <?php 
                                                    $badgeClass = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'success',
                                                        'cancelled' => 'danger'
                                                    ][$booking['status']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge bg-<?= $badgeClass ?>">
                                                        <?= ucfirst($booking['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Belum ada pemesanan untuk jadwal ini</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(scheduleId) {
    if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        fetch(`<?= base_url('schedules/delete/') ?>${scheduleId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                showErrorModal(data.message);
            }
        })
        .catch(error => {
            showErrorModal('Terjadi kesalahan: ' + error.message);
        });
    }
}
</script>
<?= $this->endSection() ?>