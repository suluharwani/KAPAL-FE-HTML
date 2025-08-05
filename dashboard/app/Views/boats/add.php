<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Tambah Kapal Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Tambah Kapal</h5>
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

                <form action="<?= base_url('boats/store') ?>" method="post" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label for="boat_name" class="form-label">Nama Kapal</label>
                        <input type="text" class="form-control" id="boat_name" name="boat_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="boat_type" class="form-label">Tipe Kapal</label>
                        <select class="form-select" id="boat_type" name="boat_type" required>
                            <option value="">Pilih Tipe Kapal</option>
                            <option value="speedboat">Speedboat</option>
                            <option value="ferry">Ferry</option>
                            <option value="yacht">Yacht</option>
                            <option value="traditional">Perahu Tradisional</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Kapasitas (orang)</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price_per_trip" class="form-label">Harga per Trip (Rp)</label>
                            <input type="number" class="form-control" id="price_per_trip" name="price_per_trip" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Kapal</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('boats') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>