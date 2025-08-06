<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Tambah Kapal Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-ship me-2"></i>Form Tambah Kapal</h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form id="boatForm" action="<?= getenv('API_BASE_URL') ?>/api/boats" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= $token ?>">
                    
                    <div class="mb-3">
                        <label for="boat_name" class="form-label">Nama Kapal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="boat_name" name="boat_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="boat_type" class="form-label">Tipe Kapal <span class="text-danger">*</span></label>
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
                            <label for="capacity" class="form-label">Kapasitas (orang) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price_per_trip" class="form-label">Harga per Trip (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="price_per_trip" name="price_per_trip" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="image" class="form-label">Gambar Kapal <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png" required>
                        <small class="text-muted">Format: JPG, PNG (maksimal 2MB)</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
                        <a href="<?= base_url('boats') ?>" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('boatForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + document.querySelector('input[name="token"]').value
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok) {
            window.location.href = '<?= base_url('boats') ?>?success=Kapal berhasil ditambahkan';
        } else {
            alert(result.message || 'Gagal menambahkan kapal');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim data');
    }
});
</script>

<?= $this->endSection() ?>