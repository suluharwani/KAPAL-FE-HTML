<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Daftar Pemesanan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pemesanan</h5>
                <a href="<?= base_url('bookings/new') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Pemesanan Baru
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Booking</th>
                                <th>Tanggal</th>
                                <th>Kapal</th>
                                <th>Jumlah Penumpang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data pemesanan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bookings as $index => $booking): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>#<?= esc($booking['booking_code']) ?></td>
                                        <td><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                                        <td><?= esc($booking['boat_name']) ?></td>
                                        <td><?= esc($booking['passenger_count']) ?> orang</td>
                                        <td>
                                            <?php 
                                            $badgeClass = [
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'cancelled' => 'danger',
                                                'completed' => 'primary'
                                            ][$booking['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('bookings/view/' . $booking['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('bookings/invoice/' . $booking['id']) ?>" class="btn btn-sm btn-secondary" title="Invoice">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                            <?php if ($booking['status'] === 'pending'): ?>
                                                <a href="<?= base_url('bookings/cancel/' . $booking['id']) ?>" class="btn btn-sm btn-danger" title="Batalkan" onclick="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
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
<?= $this->endSection() ?>