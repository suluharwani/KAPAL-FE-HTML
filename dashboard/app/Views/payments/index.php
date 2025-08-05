<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Daftar Pembayaran<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Booking</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data pembayaran</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payments as $index => $payment): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>#<?= esc($payment['booking_code']) ?></td>
                                        <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                        <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                                        <td><?= ucfirst($payment['payment_method']) ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = [
                                                'pending' => 'warning',
                                                'verified' => 'success',
                                                'rejected' => 'danger'
                                            ][$payment['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>">
                                                <?= ucfirst($payment['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('payments/view/' . $payment['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($payment['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-warning upload-proof-btn" 
                                                        data-payment-id="<?= $payment['id'] ?>" 
                                                        title="Upload Bukti Tambahan">
                                                    <i class="bi bi-upload"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Ganti bagian pager dengan: -->

            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Bukti Tambahan -->
<div class="modal fade" id="uploadProofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran Tambahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadProofForm" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="additional_proof" class="form-label">File Bukti Pembayaran</label>
                        <input class="form-control" type="file" id="additional_proof" name="additional_proof" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                    </div>
                    <input type="hidden" id="payment_id" name="payment_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="submit-text">Upload</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle upload proof button clicks
    document.querySelectorAll('.upload-proof-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            document.getElementById('payment_id').value = paymentId;
            
            const modal = new bootstrap.Modal(document.getElementById('uploadProofModal'));
            modal.show();
        });
    });
    
    // Handle proof upload form submission
    document.getElementById('uploadProofForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('[type="submit"]');
        const submitText = form.querySelector('.submit-text');
        const spinner = form.querySelector('.spinner-border');
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Mengupload...';
        spinner.classList.remove('d-none');
        
        try {
            const formData = new FormData(form);
            const paymentId = formData.get('payment_id');
            
            const response = await fetch(`<?= base_url('payments/upload-proof/') ?>${paymentId}`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Close modal and refresh page
                bootstrap.Modal.getInstance(document.getElementById('uploadProofModal')).hide();
                
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
            submitText.textContent = 'Upload';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>