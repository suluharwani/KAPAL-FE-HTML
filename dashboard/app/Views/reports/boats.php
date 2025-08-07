<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Laporan Utilisasi Kapal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Laporan Utilisasi Kapal</h5>
                <div>
                    <a href="<?= base_url('reports/export/boats?' . http_build_query($filters)) ?>" 
                       class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="get" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="<?= $filters['date_from'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="<?= $filters['date_to'] ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Kapal</th>
                                <th>Jumlah Trip</th>
                                <th>Penumpang</th>
                                <th>Pendapatan</th>
                                <th>Utilisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($boats)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data kapal</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($boats as $boat): ?>
                                    <tr>
                                        <td><?= esc($boat['boat_name']) ?></td>
                                        <td><?= $boat['trip_count'] ?></td>
                                        <td><?= $boat['passenger_count'] ?></td>
                                        <td>Rp <?= number_format($boat['total_revenue'], 0, ',', '.') ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?= $boat['utilization'] ?>%;" 
                                                     aria-valuenow="<?= $boat['utilization'] ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?= $boat['utilization'] ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>