<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Detail Pulau<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Pulau: <?= esc($island['island_name']) ?></h5>
                <div>
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a href="<?= base_url('islands/edit/' . $island['id']) ?>" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('islands') ?>" class="btn btn-sm btn-secondary">
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
                
                <div class="text-center mb-4">
                    <img src="<?= esc($island['image_url']) ?>" alt="<?= esc($island['island_name']) ?>" class="img-fluid rounded" style="max-height: 400px;">
                </div>
                
                <div class="mb-4">
                    <h6>Deskripsi</h6>
                    <p><?= nl2br(esc($island['description'])) ?></p>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Rute yang Tersedia</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($island['routes'])): ?>
                            <p class="text-muted">Belum ada rute yang tersedia untuk pulau ini.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($island['routes'] as $route): ?>
                                    <a href="<?= base_url('routes/view/' . $route['id']) ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?= esc($route['route_name']) ?></h6>
                                            <small><?= esc($route['estimated_duration']) ?></small>
                                        </div>
                                        <p class="mb-1"><?= esc($route['notes']) ?></p>
                                        <small>Jarak: <?= esc($route['distance']) ?> km</small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>