<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Edit Pulau<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Pulau</h5>
            </div>
            <div class="card-body">
                <form id="islandForm" action="<?= base_url('islands/update/' . $island['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="island_name" class="form-label">Nama Pulau</label>
                        <input type="text" class="form-control" id="island_name" name="island_name" value="<?= esc($island['island_name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?= esc($island['description']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gambar Saat Ini</label>
                        <div>
                            <img src="<?= esc($island['image_url']) ?>" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Ganti Gambar (Opsional)</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('islands/view/' . $island['id']) ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="submit-text">Simpan Perubahan</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('islandForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('[type="submit"]');
        const submitText = form.querySelector('.submit-text');
        const spinner = form.querySelector('.spinner-border');
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Menyimpan...';
        spinner.classList.remove('d-none');
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                showErrorModal(result.message);
            }
        } catch (error) {
            showErrorModal('Terjadi kesalahan: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitText.textContent = 'Simpan Perubahan';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>