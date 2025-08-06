<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Laporan Pendapatan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Laporan Pendapatan</h5>
                <div>
                    <a href="<?= base_url('reports/export/revenue?' . http_build_query($filters)) ?>" 
                       class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="get" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="<?= $filters['date_from'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="<?= $filters['date_to'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="group_by" class="form-label">Kelompokkan Berdasarkan</label>
                            <select class="form-select" id="group_by" name="group_by">
                                <option value="day" <?= $filters['group_by'] == 'day' ? 'selected' : '' ?>>Harian</option>
                                <option value="week" <?= $filters['group_by'] == 'week' ? 'selected' : '' ?>>Mingguan</option>
                                <option value="month" <?= $filters['group_by'] == 'month' ? 'selected' : '' ?>>Bulanan</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="alert alert-info">
                    <strong>Total Pendapatan:</strong> Rp <?= number_format($total, 0, ',', '.') ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah Booking</th>
                                <th>Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data pendapatan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data as $item): ?>
                                    <tr>
                                        <td><?= $item['date'] ?></td>
                                        <td><?= $item['booking_count'] ?></td>
                                        <td>Rp <?= number_format($item['total_revenue'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Chart -->
                <div class="mt-4">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const labels = <?= json_encode(array_column($data, 'date')) ?>;
    const revenueData = <?= json_encode(array_column($data, 'total_revenue')) ?>;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: revenueData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>