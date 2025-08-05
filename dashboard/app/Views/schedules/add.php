<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Tambah Jadwal Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Tambah Jadwal</h5>
            </div>
            <div class="card-body">
                <form id="scheduleForm" action="<?= base_url('schedules/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="route_id" class="form-label">Rute Perjalanan</label>
                            <select class="form-select" id="route_id" name="route_id" required>
                                <option value="">Pilih Rute</option>
                                <?php foreach ($routes as $route): ?>
                                    <option value="<?= $route['id'] ?>">
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
                                    <option value="<?= $boat['id'] ?>">
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
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="departure_time" class="form-label">Waktu Keberangkatan</label>
                            <input type="time" class="form-control" id="departure_time" name="departure_time" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="available_seats" class="form-label">Kursi Tersedia</label>
                            <input type="number" class="form-control" id="available_seats" name="available_seats" 
                                   min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available">Available</option>
                                <option value="full">Full</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('schedules') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="submit-text">Simpan</span>
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
    
    // Set waktu default ke jam berikutnya
    const now = new Date();
    const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
    document.getElementById('departure_time').value = 
        nextHour.getHours().toString().padStart(2, '0') + ':' + 
        nextHour.getMinutes().toString().padStart(2, '0');
    
    // Update kursi tersedia saat kapal dipilih
    document.getElementById('boat_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const capacity = selectedOption.text.match(/\((\d+) kursi\)/)[1];
            document.getElementById('available_seats').value = capacity;
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
            submitText.textContent = 'Simpan';
            spinner.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>