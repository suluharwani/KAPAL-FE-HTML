<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Detail Pembayaran<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Pembayaran #<?= esc($payment['id']) ?></h5>
                <a href="<?= base_url('bookings/view/' . $payment['booking_id']) ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Pemesanan
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Informasi Pemesanan</h6>
                        <p>
                            <strong>Kode Booking:</strong> #<?= esc($payment['booking_code']) ?><br>
                            <strong>Kapal:</strong> <?= esc($payment['boat_name']) ?><br>
                            <strong>Rute:</strong> <?= esc($payment['route_name']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Detail Pembayaran</h6>
                        <p>
                            <strong>Status:</strong> 
                            <?php 
                            $badgeClass = [
                                'pending' => 'warning',
                                'verified' => 'success',
                                'rejected' => 'danger'
                            ][$payment['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= ucfirst($payment['status']) ?>
                            </span><br>
                            <strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($payment['payment_date'])) ?><br>
                            <strong>Metode:</strong> <?= ucfirst($payment['payment_method']) ?><br>
                            <strong>Jumlah:</strong> Rp <?= number_format($payment['amount'], 0, ',', '.') ?>
                        </p>
                    </div>
                </div>
                
                <?php if ($payment['payment_method'] === 'transfer'): ?>
                    <div class="mb-4">
                        <h6>Informasi Transfer</h6>
                        <p>
                            <strong>Bank:</strong> <?= esc($payment['bank_name']) ?><br>
                            <strong>No. Rekening:</strong> <?= esc($payment['account_number']) ?><br>
                            <strong>Catatan:</strong> <?= !empty($payment['notes']) ? esc($payment['notes']) : '-' ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Bukti Pembayaran</h6>
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($payment['receipt_url'])): ?>
                            <img src="<?= esc($payment['receipt_url']) ?>" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 400px;">
                            <div class="mt-3">
                                <a href="<?= esc($payment['receipt_url']) ?>" class="btn btn-sm btn-primary" download>
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Tidak ada bukti pembayaran</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($payment['additional_proofs'])): ?>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Bukti Tambahan</h6>
                            <?php if ($payment['status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadAdditionalProofModal">
                                    <i class="bi bi-plus"></i> Tambah Bukti
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($payment['additional_proofs'] as $proof): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <img src="<?= esc($proof['image_url']) ?>" class="card-img-top" alt="Bukti Tambahan">
                                            <div class="card-body text-center">
                                                <small class="text-muted">
                                                    <?= date('d M Y H:i', strtotime($proof['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($payment['status'] === 'pending'): ?>
                    <div class="text-center py-4">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadAdditionalProofModal">
                            <i class="bi bi-upload"></i> Upload Bukti Tambahan
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Bukti Tambahan -->
<div class="modal fade" id="uploadAdditionalProofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Tambahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="additionalProofForm" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="additional_proof" class="form-label">File Bukti Pembayaran</label>
                        <input class="form-control" type="file" id="additional_proof" name="additional_proof" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                    </div>
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
    // Handle additional proof form submission
    document.getElementById('additionalProofForm').addEventListener('submit', async function(e) {
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
            
            const response = await fetch(`<?= base_url('payments/upload-proof/' . $payment['id']) ?>`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Close modal and refresh page
                bootstrap.Modal.getInstance(document.getElementById('uploadAdditionalProofModal')).hide();
                window.location.reload();
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