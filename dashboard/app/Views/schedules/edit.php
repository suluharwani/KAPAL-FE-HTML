<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Edit Jadwal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Data Jadwal</h5>
            </div>
            <div class="card-body">
                <form id="scheduleForm" action="<?= base_url('schedules/update/' . $schedule['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="route_id" class="form-label">Rute Perjalanan</label>
                            <select class="form-select" id="route_id" name="route_id" required>
                                <option value="">Pilih Rute</option>
                                <?php foreach ($routes as $route): ?>
                                    <option value="<?= $route['id'] ?>" 
                                        <?= $route['id'] == $schedule['route_id'] ? 'selected' : '' ?>>
                                        <?= esc($route['departure_island_name']) ?> 
                                        → <?= esc($route['arrival_island_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="boat_id" class="form-label">Kapal</label>
                            <select class="form-select" id="boat_id" name="boat_id" required>
                                <option value="">Pilih Kapal</option>
                                <?php foreach ($boats as $boat): ?>
                                    <option value="<?= $boat['id'] ?>" 
                                        <?= $boat['id'] == $schedule['boat_id'] ? 'selected' : '' ?>>
                                        <?= esc($boat['boat_name']) ?> (<?= $boat['capacity'] ?> kursi)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departure_date" class="form-label">Tanggal Keberangkatan</label>
                            <input type="date" class="form-control" id="departure_date" name="departure_date" 
                                   value="<?= $schedule['departure_date'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="departure_time" class="form-label">Waktu Keberangkatan</label>
                            <input type="time" class="form-control" id="departure_time" name="departure_time" 
                                   value="<?= substr($schedule['departure_time'], 0, 5) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="available_seats" class="form-label">Kursi Tersedia</label>
                            <input type="number" class="form-control" id="available_seats" name="available_seats" 
                                   value="<?= $schedule['available_seats'] ?>" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" <?= $schedule['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="full" <?= $schedule['status'] == 'full' ? 'selected' : '' ?>>Full</option>
                                <option value="cancelled" <?= $schedule['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"><?= esc($schedule['notes']) ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('schedules/view/' . $schedule['id']) ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="submit-text">Simpan Perubahan</span>
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
    const form = document.getElementById('scheduleForm');
    
    // Update kursi tersedia saat kapal dipilih
    document.getElementById('boat_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const capacity = selectedOption.text.match(/\((\d+) kursi\)/)[1];
            document.getElementById('available_seats').max = capacity;
        }
    });
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
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
            submitText.textContent = 'Simpan Perubahan';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>