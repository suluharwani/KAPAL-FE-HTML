<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Daftar Rute<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Rute Perjalanan</h5>
                <a href="<?= base_url('routes/add') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Tambah Rute
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
                                <th>Rute</th>
                                <th>Durasi</th>
                                <th>Jarak (km)</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($routes)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data rute</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($routes as $index => $route): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <?= esc($route['departure_island_name']) ?> 
                                            <i class="bi bi-arrow-right"></i> 
                                            <?= esc($route['arrival_island_name']) ?>
                                        </td>
                                        <td><?= esc($route['estimated_duration']) ?></td>
                                        <td><?= number_format($route['distance'], 2) ?></td>
                                        <td><?= !empty($route['notes']) ? esc($route['notes']) : '-' ?></td>
                                        <td>
                                            <a href="<?= base_url('routes/view/' . $route['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('routes/edit/' . $route['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-danger" 
                                               title="Hapus" 
                                               onclick="confirmDelete(<?= $route['id'] ?>)">
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