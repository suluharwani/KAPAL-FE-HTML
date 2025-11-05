<div class="row">
    <div class="col-md-6">
        <h5>Informasi Booking</h5>
        <table class="table table-sm">
            <tr>
                <th>Kode Booking</th>
                <td><?= $booking['booking_code'] ?></td>
            </tr>
            <tr>
                <th>Rute</th>
                <td><?= $booking['departure_island'] ?> - <?= $booking['arrival_island'] ?></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td><?= date('d M Y', strtotime($booking['departure_date'])) ?></td>
            </tr>
            <tr>
                <th>Waktu</th>
                <td><?= date('H:i', strtotime($booking['departure_time'])) ?></td>
            </tr>
            <tr>
                <th>Kapal</th>
                <td><?= $booking['boat_name'] ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-<?= 
                        $booking['booking_status'] == 'confirmed' ? 'success' : 
                        ($booking['booking_status'] == 'pending' ? 'warning' : 
                        ($booking['booking_status'] == 'paid' ? 'primary' : 
                        ($booking['booking_status'] == 'completed' ? 'info' : 'danger'))) 
                    ?>">
                        <?= ucfirst($booking['booking_status']) ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5>Informasi Pembayaran</h5>
        <table class="table table-sm">
            <tr>
                <th>Jumlah Penumpang</th>
                <td><?= $booking['passenger_count'] ?> orang</td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Metode Pembayaran</th>
                <td><?= ucfirst($booking['payment_method'] ?? 'transfer') ?></td>
            </tr>
            <tr>
                <th>Status Pembayaran</th>
                <td>
                    <span class="badge bg-<?= 
                        $booking['payment_status'] == 'paid' ? 'success' : 
                        ($booking['payment_status'] == 'pending' ? 'warning' : 
                        ($booking['payment_status'] == 'partial' ? 'info' : 'danger')) 
                    ?>">
                        <?= ucfirst($booking['payment_status']) ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="mt-4">
    <h5>Daftar Penumpang</h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Nomor Telepon</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($passengers as $passenger): ?>
                    <tr>
                        <td><?= $passenger['full_name'] ?></td>
                        <td><?= $passenger['phone'] ?? '-' ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $passenger['status'] == 'confirmed' ? 'success' : 
                                ($passenger['status'] == 'pending' ? 'warning' : 'danger') 
                            ?>">
                                <?= ucfirst($passenger['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>