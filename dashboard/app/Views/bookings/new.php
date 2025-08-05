<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Pemesanan Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Pemesanan Kapal</h5>
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

                <form action="<?= base_url('bookings/create') ?>" method="post" data-ajax-submit="true">
    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="schedule_id" class="form-label">Jadwal Perjalanan</label>
                                <select class="form-select" id="schedule_id" name="schedule_id" required>
                                    <option value="">Pilih Jadwal</option>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <option value="<?= $schedule['id'] ?>">
                                            <?= esc($schedule['boat_name']) ?> - 
                                            <?= date('d M Y', strtotime($schedule['departure_date'])) ?> - 
                                            <?= $schedule['departure_time'] ?> - 
                                            <?= esc($schedule['route_name']) ?> - 
                                            Kursi tersedia: <?= $schedule['available_seats'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="passenger_count" class="form-label">Jumlah Penumpang</label>
                                <input type="number" class="form-control" id="passenger_count" name="passenger_count" min="1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="cash">Tunai</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan (Opsional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Data Penumpang</h6>
                                </div>
                                <div class="card-body">
                                    <div id="passengers-container">
                                        <!-- Passenger forms will be added here by JavaScript -->
                                    </div>
                                    <input type="hidden" id="passengers" name="passengers" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
        <a href="<?= base_url('bookings') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
            <span class="submit-text">Buat Pemesanan</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
            </div>
        </div>
    </div>
</div>

<!-- Passenger Form Template (hidden) -->
<div id="passenger-template" class="d-none">
    <div class="passenger-form mb-3 border p-3">
        <h6 class="mb-3">Penumpang <span class="passenger-number">1</span></h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control passenger-full_name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Identitas</label>
                <input type="text" class="form-control passenger-identity_number" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input type="tel" class="form-control passenger-phone" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Usia</label>
                <input type="number" class="form-control passenger-age" min="1" max="100" required>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passengerCount = document.getElementById('passenger_count');
    const passengersContainer = document.getElementById('passengers-container');
    const passengerTemplate = document.getElementById('passenger-template');
    const passengersInput = document.getElementById('passengers');
    
    // Generate passenger forms based on count
    passengerCount.addEventListener('change', updatePassengerForms);
    
    function updatePassengerForms() {
        const count = parseInt(passengerCount.value) || 0;
        passengersContainer.innerHTML = '';
        
        if (count > 0) {
            for (let i = 0; i < count; i++) {
                const newPassenger = passengerTemplate.cloneNode(true);
                newPassenger.classList.remove('d-none');
                newPassenger.querySelector('.passenger-number').textContent = i + 1;
                
                // Set unique IDs for inputs
                const inputs = newPassenger.querySelectorAll('input');
                inputs.forEach(input => {
                    const name = input.classList[1].replace('passenger-', '');
                    input.name = `passenger_${i}_${name}`;
                    input.id = `passenger_${i}_${name}`;
                });
                
                passengersContainer.appendChild(newPassenger);
            }
        }
    }
    
    // Handle form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const passengerForms = document.querySelectorAll('.passenger-form');
        const passengers = [];
        
        passengerForms.forEach(form => {
            passengers.push({
                full_name: form.querySelector('.passenger-full_name').value,
                identity_number: form.querySelector('.passenger-identity_number').value,
                phone: form.querySelector('.passenger-phone').value,
                age: form.querySelector('.passenger-age').value
            });
        });
        
        passengersInput.value = JSON.stringify(passengers);
    });
    
    // Initial update
    updatePassengerForms();
});
// Update form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-ajax-submit="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('[type="submit"]');
            const submitText = form.querySelector('.submit-text');
            const spinner = form.querySelector('.spinner-border');
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Memproses...';
            spinner.classList.remove('d-none');
            
            try {
                const formData = new FormData(form);
                
                // Convert passengers data to JSON
                const passengerForms = document.querySelectorAll('.passenger-form');
                const passengers = [];
                
                passengerForms.forEach(pForm => {
                    passengers.push({
                        full_name: pForm.querySelector('.passenger-full_name').value,
                        identity_number: pForm.querySelector('.passenger-identity_number').value,
                        phone: pForm.querySelector('.passenger-phone').value,
                        age: pForm.querySelector('.passenger-age').value
                    });
                });
                
                formData.set('passengers', JSON.stringify(passengers));
                
                const response = await fetch(form.action, {
                    method: form.method,
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
                submitText.textContent = 'Buat Pemesanan';
                spinner.classList.add('d-none');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>