<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Testimoni<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Testimoni</h5>
                <div>
                    <a href="<?= base_url('testimonials/approved') ?>" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-check-circle"></i> Lihat Disetujui
                    </a>
                    <a href="<?= base_url('testimonials/create') ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Buat Testimoni
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($testimonials)): ?>
                    <div class="alert alert-info">
                        Tidak ada testimoni yang ditemukan.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pengguna</th>
                                    <th>Konten</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($testimonials as $index => $testimonial): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($testimonial['user_name']) ?></td>
                                        <td><?= esc(substr($testimonial['content'], 0, 50)) ?><?= strlen($testimonial['content']) > 50 ? '...' : '' ?></td>
                                        <td>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $testimonial['rating'] ? '-fill text-warning' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $badgeClass = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ][$testimonial['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>">
                                                <?= ucfirst($testimonial['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-testimonial" 
                                                    data-content="<?= esc($testimonial['content']) ?>"
                                                    data-rating="<?= $testimonial['rating'] ?>"
                                                    data-user="<?= esc($testimonial['user_name']) ?>"
                                                    data-status="<?= $testimonial['status'] ?>"
                                                    data-date="<?= date('d M Y', strtotime($testimonial['created_at'])) ?>">
                                                <i class="bi bi-eye"></i> Lihat
                                            </button>
                                            <?php if (session()->get('role') === 'admin' && $testimonial['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-success approve-testimonial" 
                                                        data-id="<?= $testimonial['id'] ?>">
                                                    <i class="bi bi-check"></i> Setujui
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-testimonial" 
                                                        data-id="<?= $testimonial['id'] ?>">
                                                    <i class="bi bi-x"></i> Tolak
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <?php if ($pager): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Testimonial -->
<div class="modal fade" id="viewTestimonialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Testimoni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Pengguna</label>
                    <p id="testimonial-user" class="fw-bold"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <p id="testimonial-date"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <p id="testimonial-rating"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <p id="testimonial-status"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Konten</label>
                    <p id="testimonial-content" class="border p-2 rounded"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approve/Reject Testimonial (Admin Only) -->
<?php if (session()->get('role') === 'admin'): ?>
<div class="modal fade" id="statusTestimonialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Status Testimoni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusTestimonialForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="testimonial_id" name="testimonial_id">
                    <input type="hidden" id="status_action" name="status">
                    <p>Anda yakin ingin <span id="status-text"></span> testimoni ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="submit-text">Konfirmasi</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle view testimonial button clicks
    document.querySelectorAll('.view-testimonial').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('testimonial-user').textContent = this.getAttribute('data-user');
            document.getElementById('testimonial-date').textContent = this.getAttribute('data-date');
            
            const rating = parseInt(this.getAttribute('data-rating'));
            let ratingHtml = '';
            for (let i = 1; i <= 5; i++) {
                ratingHtml += `<i class="bi bi-star${i <= rating ? '-fill text-warning' : ''}"></i>`;
            }
            document.getElementById('testimonial-rating').innerHTML = ratingHtml;
            
            const status = this.getAttribute('data-status');
            const statusClass = {
                'pending': 'warning',
                'approved': 'success',
                'rejected': 'danger'
            }[status] || 'secondary';
            document.getElementById('testimonial-status').innerHTML = 
                `<span class="badge bg-${statusClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
            
            document.getElementById('testimonial-content').textContent = this.getAttribute('data-content');
            
            const modal = new bootstrap.Modal(document.getElementById('viewTestimonialModal'));
            modal.show();
        });
    });
    
    <?php if (session()->get('role') === 'admin'): ?>
    // Handle approve testimonial button clicks
    document.querySelectorAll('.approve-testimonial').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('testimonial_id').value = this.getAttribute('data-id');
            document.getElementById('status_action').value = 'approved';
            document.getElementById('status-text').textContent = 'menyetujui';
            
            const modal = new bootstrap.Modal(document.getElementById('statusTestimonialModal'));
            modal.show();
        });
    });
    
    // Handle reject testimonial button clicks
    document.querySelectorAll('.reject-testimonial').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('testimonial_id').value = this.getAttribute('data-id');
            document.getElementById('status_action').value = 'rejected';
            document.getElementById('status-text').textContent = 'menolak';
            
            const modal = new bootstrap.Modal(document.getElementById('statusTestimonialModal'));
            modal.show();
        });
    });
    
    // Handle status change form submission
    document.getElementById('statusTestimonialForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('[type="submit"]');
        const submitText = form.querySelector('.submit-text');
        const spinner = form.querySelector('.spinner-border');
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Memproses...';
        spinner.classList.remove('d-none');
        
        try {
            const formData = new FormData(form);
            const testimonialId = formData.get('testimonial_id');
            
            const response = await fetch(`<?= base_url('testimonials/update-status/') ?>${testimonialId}`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Close modal and refresh page
                bootstrap.Modal.getInstance(document.getElementById('statusTestimonialModal')).hide();
                window.location.reload();
            } else {
                showErrorModal(result.message);
            }
        } catch (error) {
            showErrorModal('Terjadi kesalahan: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitText.textContent = 'Konfirmasi';
            spinner.classList.add('d-none');
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>