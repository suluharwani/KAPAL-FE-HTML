<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Pembayaran Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6>Detail Pemesanan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Kode Booking:</strong> #<?= esc($booking['booking_code']) ?><br>
                                <strong>Kapal:</strong> <?= esc($booking['boat_name']) ?><br>
                                <strong>Rute:</strong> <?= esc($booking['route_name']) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Tanggal:</strong> <?= date('d M Y', strtotime($booking['departure_date'])) ?><br>
                                <strong>Waktu:</strong> <?= $booking['departure_time'] ?><br>
                                <strong>Total:</strong> Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <form id="paymentForm" action="<?= base_url('payments/create') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah Pembayaran (Rp)</label>
                        <input type="number" class="form-control" id="amount" name="amount" 
                               value="<?= $booking['total_price'] ?>" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <?php foreach ($paymentMethods as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="bankFields" class="mb-3 d-none">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bank_name" class="form-label">Bank Tujuan</label>
                                <select class="form-select" id="bank_name" name="bank_name">
                                    <option value="">Pilih Bank</option>
                                    <?php foreach ($banks as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_number" class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" id="account_number" name="account_number">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="receipt_image" class="form-label">Bukti Pembayaran</label>
                        <input class="form-control" type="file" id="receipt_image" name="receipt_image" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('bookings/view/' . $booking['id']) ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="submit-text">Simpan Pembayaran</span>
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
    // Tampilkan field bank jika metode transfer dipilih
    document.getElementById('payment_method').addEventListener('change', function() {
        const bankFields = document.getElementById('bankFields');
        if (this.value === 'transfer') {
            bankFields.classList.remove('d-none');
            document.getElementById('bank_name').setAttribute('required', '');
        } else {
            bankFields.classList.add('d-none');
            document.getElementById('bank_name').removeAttribute('required');
        }
    });
    
    // Handle form submission
    document.getElementById('paymentForm').addEventListener('submit', async function(e) {
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
            submitText.textContent = 'Simpan Pembayaran';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>