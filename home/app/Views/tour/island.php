<div class="container my-5">
    <h1 class="mb-4">Wisata <?= $island['island_name'] ?></h1>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <img src="<?= base_url('uploads/islands/' . $island['image_url']) ?>" class="img-fluid rounded shadow" alt="<?= $island['island_name'] ?>">
        </div>
        <div class="col-md-8 mb-4">
            <p><?= $island['description'] ?></p>
        </div>
    </div>
    
    <h2 class="mb-4">Paket Wisata</h2>
    
    <div class="row">
        <?php foreach ($packages as $package): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= base_url('uploads/packages/' . $package['image']) ?>" class="card-img-top" alt="<?= $package['package_name'] ?>">
                    <div class="card-body">
                        <h3 class="h5 card-title"><?= $package['package_name'] ?></h3>
                        <p class="card-text"><?= $package['description'] ?></p>
                        <p class="text-primary fw-bold">Rp <?= number_format($package['price'], 0, ',', '.') ?></p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="<?= base_url('booking?package=' . $package['package_id']) ?>" class="btn btn-primary w-100">Pesan Sekarang</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>