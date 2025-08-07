<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Laporan Pemesanan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Laporan Pemesanan</h5>
                <div>
                    <a href="<?= base_url('reports/export/bookings?' . http_build_query($filters)) ?>" 
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
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= $filters['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $filters['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="cancelled" <?= $filters['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="boat_id" class="form-label">Kapal</label>
                            <select class="form-select" id="boat_id" name="boat_id">
                                <option value="">Semua Kapal</option>
                                <?php foreach ($boats as $boat): ?>
                                    <option value="<?= $boat['id'] ?>" <?= $filters['boat_id'] == $boat['id'] ? 'selected' : '' ?>>
                                        <?= esc($boat['boat_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="route_id" class="form-label">Rute</label>
                            <select class="form-select" id="route_id" name="route_id">
                                <option value="">Semua Rute</option>
                                <?php foreach ($routes as $route): ?>
                                    <option value="<?= $route['id'] ?>" <?= $filters['route_id'] == $route['id'] ? 'selected' : '' ?>>
                                        <?= esc($route['departure_island_name']) ?> → <?= esc($route['arrival_island_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="alert alert-info">
                    <strong>Total Pemesanan:</strong> <?= number_format($total, 0, ',', '.') ?> | 
                    <strong>Total Pendapatan:</strong> Rp <?= number_format($total_revenue, 0, ',', '.') ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Kode Booking</th>
                                <th>Tanggal</th>
                                <th>Kapal</th>
                                <th>Rute</th>
                                <th>Penumpang</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pemesanan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bookings as $index => $booking): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $booking['booking_code'] ?></td>
                                        <td><?= date('d M Y', strtotime($booking['created_at'])) ?></td>
                                        <td><?= esc($booking['boat_name']) ?></td>
                                        <td><?= esc($booking['route_name']) ?></td>
                                        <td><?= $booking['passenger_count'] ?></td>
                                        <td>Rp <?= number_format($booking['total_amount'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = [
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'cancelled' => 'danger'
                                            ][$booking['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
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