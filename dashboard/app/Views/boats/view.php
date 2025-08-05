<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Detail Kapal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Kapal</h5>
                <div>
                    <a href="<?= base_url('boats/edit/' . $boat['boat_id']) ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="<?= base_url('boats') ?>" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4 text-center">
                        <?php if (!empty($boat['image_url'])): ?>
                            <img src="<?= esc($boat['image_url']) ?>" alt="Gambar Kapal" class="img-fluid rounded" style="max-height: 200px;">
                        <?php else: ?>
                            <div class="bg-light p-5 text-center rounded">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Tidak ada gambar</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h3><?= esc($boat['boat_name']) ?></h3>
                        <p class="text-muted"><?= esc($boat['boat_type']) ?></p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <p><strong>Kapasitas:</strong> <?= esc($boat['capacity']) ?> orang</p>
                                <p><strong>Harga per Trip:</strong> Rp <?= number_format($boat['price_per_trip'], 0, ',', '.') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Dibuat pada:</strong> <?= date('d M Y', strtotime($boat['created_at'])) ?></p>
                                <p><strong>Diperbarui pada:</strong> <?= date('d M Y', strtotime($boat['updated_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h5>Deskripsi</h5>
                    <p><?= !empty($boat['description']) ? nl2br(esc($boat['description'])) : 'Tidak ada deskripsi' ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>