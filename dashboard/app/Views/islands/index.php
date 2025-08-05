<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Daftar Pulau<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pulau</h5>
                <?php if (session()->get('role') === 'admin'): ?>
                    <a href="<?= base_url('islands/add') ?>" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Tambah Pulau
                    </a>
                <?php endif; ?>
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
                                <th>Nama Pulau</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($islands)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data pulau</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($islands as $index => $island): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($island['island_name']) ?></td>
                                        <td><?= esc(substr($island['description'], 0, 100)) ?><?= strlen($island['description']) > 100 ? '...' : '' ?></td>
                                        <td>
                                            <a href="<?= base_url('islands/view/' . $island['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if (session()->get('role') === 'admin'): ?>
                                                <a href="<?= base_url('islands/edit/' . $island['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= base_url('islands/delete/' . $island['id']) ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pulau ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pager): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>