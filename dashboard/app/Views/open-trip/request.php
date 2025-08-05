<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Ajukan Open Trip<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Ajukan Open Trip</h5>
            </div>
            <div class="card-body">
                <form id="openTripForm" action="<?= base_url('open-trip/submit-request') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="boat_id" class="form-label">Kapal</label>
                        <select class="form-select" id="boat_id" name="boat_id" required>
                            <option value="">Pilih Kapal</option>
                            <?php foreach ($boats as $boat): ?>
                                <option value="<?= $boat['id'] ?>">
                                    <?= esc($boat['boat_name']) ?> (<?= esc($boat['boat_type']) ?>, Kapasitas: <?= $boat['capacity'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Rute</label>
                        <select class="form-select" id="route_id" name="route_id" required>
                            <option value="">Pilih Rute</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?= $route['id'] ?>">
                                    <?= esc($route['departure_island_name']) ?> ke <?= esc($route['arrival_island_name']) ?> 
                                    (<?= esc($route['estimated_duration']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="proposed_date" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="proposed_date" name="proposed_date" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="proposed_time" class="form-label">Waktu</label>
                            <input type="time" class="form-control" id="proposed_time" name="proposed_time" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="min_passengers" class="form-label">Minimal Penumpang</label>
                            <input type="number" class="form-control" id="min_passengers" name="min_passengers" 
                                   min="5" max="50" value="10" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_passengers" class="form-label">Maksimal Penumpang</label>
                            <input type="number" class="form-control" id="max_passengers" name="max_passengers" 
                                   min="5" max="50" value="20" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('open-trip') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="submit-text">Ajukan</span>
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
    // Set minimum date to today
    document.getElementById('proposed_date').min = new Date().toISOString().split('T')[0];
    
    // Handle form submission
    document.getElementById('openTripForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('[type="submit"]');
        const submitText = form.querySelector('.submit-text');
        const spinner = form.querySelector('.spinner-border');
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Mengajukan...';
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
            submitText.textContent = 'Ajukan';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>