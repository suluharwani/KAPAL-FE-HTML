<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Daftar Jadwal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Jadwal Perjalanan</h5>
                <a href="<?= base_url('schedules/add') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Tambah Jadwal
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Rute</th>
                                <th>Kapal</th>
                                <th>Kursi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($schedules)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data jadwal</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($schedules as $index => $schedule): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= date('d M Y', strtotime($schedule['departure_date'])) ?></td>
                                        <td><?= substr($schedule['departure_time'], 0, 5) ?></td>
                                        <td>
                                            <?= esc($schedule['departure_island_name']) ?> 
                                            <i class="bi bi-arrow-right"></i> 
                                            <?= esc($schedule['arrival_island_name']) ?>
                                        </td>
                                        <td><?= esc($schedule['boat_name']) ?></td>
                                        <td><?= $schedule['available_seats'] ?></td>
                                        <td>
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
                                        </td>
                                        <td>
                                            <a href="<?= base_url('schedules/view/' . $schedule['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('schedules/edit/' . $schedule['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-danger" 
                                               title="Hapus" 
                                               onclick="confirmDelete(<?= $schedule['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pager->getPageCount() > 1): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
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