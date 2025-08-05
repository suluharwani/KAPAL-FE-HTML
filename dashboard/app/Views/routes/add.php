<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Tambah Rute Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Tambah Rute</h5>
            </div>
            <div class="card-body">
                <form id="routeForm" action="<?= base_url('routes/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departure_island_id" class="form-label">Pulau Keberangkatan</label>
                            <select class="form-select" id="departure_island_id" name="departure_island_id" required>
                                <option value="">Pilih Pulau</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['id'] ?>"><?= esc($island['island_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="arrival_island_id" class="form-label">Pulau Tujuan</label>
                            <select class="form-select" id="arrival_island_id" name="arrival_island_id" required>
                                <option value="">Pilih Pulau</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['id'] ?>"><?= esc($island['island_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estimated_duration" class="form-label">Perkiraan Durasi</label>
                            <input type="text" class="form-control" id="estimated_duration" name="estimated_duration" 
                                   placeholder="Contoh: 2 jam 30 menit" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="distance" class="form-label">Jarak (km)</label>
                            <input type="number" class="form-control" id="distance" name="distance" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('routes') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="submit-text">Simpan</span>
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
    const form = document.getElementById('routeForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('[type="submit"]');
        const submitText = form.querySelector('.submit-text');
        const spinner = form.querySelector('.spinner-border');
        
        // Validasi manual pulau keberangkatan dan tujuan tidak sama
        const departureId = document.getElementById('departure_island_id').value;
        const arrivalId = document.getElementById('arrival_island_id').value;
        
        if (departureId === arrivalId) {
            showErrorModal('Pulau keberangkatan dan tujuan tidak boleh sama');
            return;
        }
        
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
            submitText.textContent = 'Simpan';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>