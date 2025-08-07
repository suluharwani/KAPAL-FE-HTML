<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Buat Testimoni<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Testimoni</h5>
            </div>
            <div class="card-body">
                <form id="testimonialForm" action="<?= base_url('testimonials/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Testimoni Anda</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                        <small class="text-muted">Bagikan pengalaman Anda menggunakan layanan kami (minimal 10 karakter)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <div class="rating-input">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star rating-star" data-value="<?= $i ?>"></i>
                            <?php endfor; ?>
                            <input type="hidden" id="rating" name="rating" value="5" required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('testimonials') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="submit-text">Kirim</span>
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
    // Handle star rating selection
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('rating');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            ratingInput.value = value;
            
            stars.forEach((s, i) => {
                if (i < value) {
                    s.classList.add('bi-star-fill', 'text-warning');
                    s.classList.remove('bi-star');
                } else {
                    s.classList.add('bi-star');
                    s.classList.remove('bi-star-fill', 'text-warning');
                }
            });
        });
        
        // Initialize with default rating (5)
        star.classList.add(star.getAttribute('data-value') <= 5 ? 'bi-star-fill' : 'bi-star');
        if (star.getAttribute('data-value') <= 5) {
            star.classList.add('text-warning');
        }
    });
    
    // Handle form submission
    document.getElementById('testimonialForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('[type="submit"]');
        const submitText = form.querySelector('.submit-text');
        const spinner = form.querySelector('.spinner-border');
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Mengirim...';
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
            submitText.textContent = 'Kirim';
            spinner.classList.add('d-none');
        }
    });
});
</script>

<style>
.rating-input {
    font-size: 24px;
    cursor: pointer;
}
.rating-input .bi-star {
    transition: all 0.2s ease;
}
</style>
<?= $this->endSection() ?>