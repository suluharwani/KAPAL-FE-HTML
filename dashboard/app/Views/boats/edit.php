<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Edit Kapal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Data Kapal</h5>
            </div>
            <div class="card-body">
                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('boats/update/' . $boat['boat_id']) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="boat_name" class="form-label">Nama Kapal</label>
                        <input type="text" class="form-control" id="boat_name" name="boat_name" value="<?= esc($boat['boat_name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="boat_type" class="form-label">Tipe Kapal</label>
                        <select class="form-select" id="boat_type" name="boat_type" required>
                            <option value="speedboat" <?= $boat['boat_type'] === 'speedboat' ? 'selected' : '' ?>>Speedboat</option>
                            <option value="ferry" <?= $boat['boat_type'] === 'ferry' ? 'selected' : '' ?>>Ferry</option>
                            <option value="yacht" <?= $boat['boat_type'] === 'yacht' ? 'selected' : '' ?>>Yacht</option>
                            <option value="traditional" <?= $boat['boat_type'] === 'traditional' ? 'selected' : '' ?>>Perahu Tradisional</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Kapasitas (orang)</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" value="<?= esc($boat['capacity']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price_per_trip" class="form-label">Harga per Trip (Rp)</label>
                            <input type="number" class="form-control" id="price_per_trip" name="price_per_trip" min="0" value="<?= esc($boat['price_per_trip']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= esc($boat['description']) ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('boats') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>