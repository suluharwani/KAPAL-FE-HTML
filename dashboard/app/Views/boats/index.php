<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Daftar Kapal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Kapal</h5>
                <a href="<?= base_url('boats/add') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Tambah Kapal
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kapal</th>
                                <th>Tipe</th>
                                <th>Kapasitas</th>
                                <th>Harga/Trip</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($boats)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data kapal</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($boats as $index => $boat): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($boat['boat_name']) ?></td>
                                        <td><?= esc($boat['boat_type']) ?></td>
                                        <td><?= esc($boat['capacity']) ?> orang</td>
                                        <td>Rp <?= number_format($boat['price_per_trip'], 0, ',', '.') ?></td>
                                        <td>
                                            <a href="<?= base_url('boats/view/' . $boat['boat_id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('boats/edit/' . $boat['boat_id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('boats/delete/' . $boat['boat_id']) ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kapal ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

=
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>