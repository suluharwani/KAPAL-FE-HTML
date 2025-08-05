<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Detail Open Trip<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Open Trip #<?= esc($openTrip['id']) ?></h5>
                <div>
                    <?php if (session()->get('role') === 'admin' && $openTrip['status'] === 'pending'): ?>
                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#approvalModal">
                            <i class="bi bi-check-circle"></i> Proses
                        </button>
                    <?php endif; ?>
                    <a href="<?= base_url('open-trip') ?>" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Informasi Kapal</h6>
                        <p>
                            <strong>Nama Kapal:</strong> <?= esc($openTrip['boat_name']) ?><br>
                            <strong>Tipe:</strong> <?= esc($openTrip['boat_type']) ?><br>
                            <strong>Kapasitas:</strong> <?= $openTrip['capacity'] ?> orang
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Status Open Trip</h6>
                        <p>
                            <strong>Status:</strong> 
                            <?php 
                            $badgeClass = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'completed' => 'primary',
                                'cancelled' => 'secondary'
                            ][$openTrip['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= ucfirst($openTrip['status']) ?>
                            </span><br>
                            <strong>Diajukan oleh:</strong> <?= esc($openTrip['requester_name']) ?><br>
                            <strong>Tanggal:</strong> <?= date('d M Y', strtotime($openTrip['departure_date'])) ?><br>
                            <strong>Waktu:</strong> <?= substr($openTrip['departure_time'], 0, 5) ?>
                        </p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Informasi Rute</h6>
                        <p>
                            <strong>Rute:</strong> <?= esc($openTrip['route_name']) ?><br>
                            <strong>Keberangkatan:</strong> <?= esc($openTrip['departure_island_name']) ?><br>
                            <strong>Tujuan:</strong> <?= esc($openTrip['arrival_island_name']) ?><br>
                            <strong>Durasi:</strong> <?= esc($openTrip['estimated_duration']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Kuota Penumpang</h6>
                        <p>
                            <strong>Minimal:</strong> <?= $openTrip['min_passengers'] ?> orang<br>
                            <strong>Maksimal:</strong> <?= $openTrip['max_passengers'] ?> orang<br>
                            <strong>Tersedia:</strong> <?= $openTrip['available_seats'] ?> kursi
                        </p>
                    </div>
                </div>
                
                <?php if (!empty($openTrip['notes'])): ?>
                    <div class="mb-4">
                        <h6>Catatan</h6>
                        <p><?= nl2br(esc($openTrip['notes'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($openTrip['admin_notes'])): ?>
                    <div class="mb-4">
                        <h6>Catatan Admin</h6>
                        <p><?= nl2br(esc($openTrip['admin_notes'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($openTrip['status'] === 'approved' && $openTrip['available_seats'] > 0): ?>
                    <div class="text-center mt-4">
                        <button class="btn btn-success btn-lg join-trip-btn" 
                                data-trip-id="<?= $openTrip['id'] ?>">
                            <i class="bi bi-people"></i> Gabung Open Trip Ini
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approval (Admin Only) -->
<?php if (session()->get('role') === 'admin' && $openTrip['status'] === 'pending'): ?>
<div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Permintaan Open Trip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approvalForm" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusApproved" value="approved" checked>
                                <label class="form-check-label" for="statusApproved">Setujui</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusRejected" value="rejected">
                                <label class="form-check-label" for="statusRejected">Tolak</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Catatan Admin</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="submit-text">Simpan</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle approval form submission
    document.getElementById('approvalForm').addEventListener('submit', async function(e) {
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
            
            const response = await fetch(`<?= base_url('open-trip/approve/' . $openTrip['id']) ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Close modal and refresh page
                bootstrap.Modal.getInstance(document.getElementById('approvalModal')).hide();
                window.location.reload();
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
<?php endif; ?>

<!-- Join Trip Modal (same as in index.php) -->
<div class="modal fade" id="joinTripModal" tabindex="-1" aria-hidden="true">
    <!-- Modal content same as in index.php -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle join trip button click in view page
    document.querySelector('.join-trip-btn')?.addEventListener('click', function() {
        const tripId = this.getAttribute('data-trip-id');
        document.getElementById('trip_id').value = tripId;
        
        const modal = new bootstrap.Modal(document.getElementById('joinTripModal'));
        modal.show();
    });
    
    // Rest of the join trip modal functionality same as in index.php
});
</script>
<?= $this->endSection() ?>