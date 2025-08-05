<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Daftar Open Trip<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Open Trip Tersedia</h5>
                <a href="<?= base_url('open-trip/request') ?>" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Ajukan Open Trip
                </a>
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
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kapal</th>
                                <th>Rute</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($openTrips)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada open trip tersedia</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($openTrips as $index => $trip): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($trip['boat_name']) ?></td>
                                        <td><?= esc($trip['route_name']) ?></td>
                                        <td><?= date('d M Y', strtotime($trip['departure_date'])) ?></td>
                                        <td><?= substr($trip['departure_time'], 0, 5) ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'completed' => 'primary',
                                                'cancelled' => 'secondary'
                                            ][$trip['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>">
                                                <?= ucfirst($trip['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('open-trip/view/' . $trip['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($trip['status'] === 'approved' && $trip['available_seats'] > 0): ?>
                                                <button class="btn btn-sm btn-success join-trip-btn" 
                                                        data-trip-id="<?= $trip['id'] ?>" 
                                                        title="Gabung">
                                                    <i class="bi bi-people"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pager): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Join Trip -->
<div class="modal fade" id="joinTripModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gabung Open Trip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="joinTripForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="passenger_count" id="passenger_count" value="1">
                    <input type="hidden" id="trip_id" name="trip_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah Penumpang</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="decrement-passenger">-</button>
                            <input type="text" class="form-control text-center" id="passenger_display" value="1" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="increment-passenger">+</button>
                        </div>
                    </div>
                    
                    <div id="passengers-container">
                        <!-- Passenger forms will be added here dynamically -->
                        <div class="passenger-form mb-3 p-3 border rounded">
                            <h6>Penumpang #1</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="passengers[0][full_name]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Identitas</label>
                                    <input type="text" class="form-control" name="passengers[0][identity_number]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" name="passengers[0][phone]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Usia</label>
                                    <input type="number" class="form-control" name="passengers[0][age]" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="submit-text">Gabung</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle join trip button clicks
    document.querySelectorAll('.join-trip-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tripId = this.getAttribute('data-trip-id');
            document.getElementById('trip_id').value = tripId;
            
            const modal = new bootstrap.Modal(document.getElementById('joinTripModal'));
            modal.show();
        });
    });
    
    // Handle passenger count changes
    let passengerCount = 1;
    const maxPassengers = 10;
    
    document.getElementById('increment-passenger').addEventListener('click', function() {
        if (passengerCount < maxPassengers) {
            passengerCount++;
            updatePassengerForms();
        }
    });
    
    document.getElementById('decrement-passenger').addEventListener('click', function() {
        if (passengerCount > 1) {
            passengerCount--;
            updatePassengerForms();
        }
    });
    
    function updatePassengerForms() {
        document.getElementById('passenger_display').value = passengerCount;
        document.getElementById('passenger_count').value = passengerCount;
        
        const container = document.getElementById('passengers-container');
        container.innerHTML = '';
        
        for (let i = 0; i < passengerCount; i++) {
            const formHtml = `
                <div class="passenger-form mb-3 p-3 border rounded">
                    <h6>Penumpang #${i+1}</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="passengers[${i}][full_name]" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Identitas</label>
                            <input type="text" class="form-control" name="passengers[${i}][identity_number]" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" name="passengers[${i}][phone]" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usia</label>
                            <input type="number" class="form-control" name="passengers[${i}][age]" min="1" required>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', formHtml);
        }
    }
    
    // Handle join trip form submission
    document.getElementById('joinTripForm').addEventListener('submit', async function(e) {
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
            const passengers = [];
            
            // Collect passenger data
            for (let i = 0; i < passengerCount; i++) {
                passengers.push({
                    full_name: formData.get(`passengers[${i}][full_name]`),
                    identity_number: formData.get(`passengers[${i}][identity_number]`),
                    phone: formData.get(`passengers[${i}][phone]`),
                    age: formData.get(`passengers[${i}][age]`)
                });
            }
            
            const tripId = formData.get('trip_id');
            const data = {
                passenger_count: passengerCount,
                passengers: passengers
            };
            
            const response = await fetch(`<?= base_url('open-trip/join/') ?>${tripId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Close modal and redirect
                bootstrap.Modal.getInstance(document.getElementById('joinTripModal')).hide();
                
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
            submitText.textContent = 'Gabung';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>