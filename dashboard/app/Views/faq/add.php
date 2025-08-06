<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Tambah FAQ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Tambah FAQ</h5>
            </div>
            <div class="card-body">
                <form id="faqForm" action="<?= base_url('faq/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="question" class="form-label">Pertanyaan</label>
                        <input type="text" class="form-control" id="question" name="question" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="answer" class="form-label">Jawaban</label>
                        <textarea class="form-control" id="answer" name="answer" rows="5" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category ?>"><?= ucfirst($category) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                            <label class="form-check-label" for="is_featured">
                                Jadikan Unggulan
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_order" class="form-label">Urutan Tampil</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" min="0">
                        <small class="text-muted">Nomor kecil akan ditampilkan lebih awal (opsional)</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('faq') ?>" class="btn btn-secondary">Batal</a>
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
    // Handle form submission
    document.getElementById('faqForm').addEventListener('submit', async function(e) {
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
            const data = Object.fromEntries(formData.entries());
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
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