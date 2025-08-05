<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Detail Rute<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Rute Perjalanan</h5>
                <div>
                    <a href="<?= base_url('routes/edit/' . $route['id']) ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="#" class="btn btn-sm btn-danger" 
                       onclick="confirmDelete(<?= $route['id'] ?>)">
                        <i class="bi bi-trash"></i> Hapus
                    </a>
                    <a href="<?= base_url('routes') ?>" class="btn btn-sm btn-secondary">
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
                        <h6>Informasi Rute</h6>
                        <p>
                            <strong>Keberangkatan:</strong> <?= esc($route['departure_island_name']) ?><br>
                            <strong>Tujuan:</strong> <?= esc($route['arrival_island_name']) ?><br>
                            <strong>Durasi:</strong> <?= esc($route['estimated_duration']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Detail Teknis</h6>
                        <p>
                            <strong>Jarak:</strong> <?= number_format($route['distance'], 2) ?> km<br>
                            <strong>Dibuat pada:</strong> <?= date('d M Y H:i', strtotime($route['created_at'])) ?><br>
                            <strong>Diperbarui pada:</strong> <?= date('d M Y H:i', strtotime($route['updated_at'])) ?>
                        </p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6>Catatan</h6>
                    <p><?= !empty($route['notes']) ? nl2br(esc($route['notes'])) : 'Tidak ada catatan' ?></p>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Jadwal Terkait</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($route['schedules'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Kapal</th>
                                            <th>Kursi Tersedia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($route['schedules'] as $schedule): ?>
                                            <tr>
                                                <td><?= date('d M Y', strtotime($schedule['departure_date'])) ?></td>
                                                <td><?= $schedule['departure_time'] ?></td>
                                                <td><?= esc($schedule['boat_name']) ?></td>
                                                <td><?= $schedule['available_seats'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Belum ada jadwal untuk rute ini</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(routeId) {
    if (confirm('Apakah Anda yakin ingin menghapus rute ini?')) {
        fetch(`<?= base_url('routes/delete/') ?>${routeId}`, {
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