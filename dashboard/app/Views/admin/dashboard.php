<?= $this->include('templates/header') ?>

<h1 class="h3 mb-4">Dashboard</h1>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Kapal</h5>
                <h2 class="card-text"><?= $boats_count ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Pemesanan Hari Ini</h5>
                <h2 class="card-text"><?= $today_bookings ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Pembayaran Baru</h5>
                <h2 class="card-text"><?= $new_payments ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Open Trip Aktif</h5>
                <h2 class="card-text"><?= $active_trips ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Pemesanan Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_bookings)): ?>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?= $booking['id'] ?></td>
                                        <td><?= $booking['customer_name'] ?></td>
                                        <td><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                                        <td><span class="badge bg-<?= $booking['status'] == 'confirmed' ? 'success' : 'warning' ?>"><?= $booking['status'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Pembayaran Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_payments)): ?>
                                <?php foreach ($recent_payments as $payment): ?>
                                    <tr>
                                        <td><?= $payment['id'] ?></td>
                                        <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                                        <td><?= ucfirst($payment['payment_method']) ?></td>
                                        <td><span class="badge bg-<?= $payment['status'] == 'verified' ? 'success' : 'warning' ?>"><?= $payment['status'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>