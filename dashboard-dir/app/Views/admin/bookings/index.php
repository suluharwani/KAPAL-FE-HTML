<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Bookings List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Booking Code</th>
                        <th>Customer</th>
                        <th>Boat</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $index => $booking): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($booking['booking_code']) ?></td>
                        <td><?= esc($booking['full_name']) ?></td>
                        <td><?= esc($booking['boat_name']) ?></td>
                        <td><?= date('d M Y', strtotime($booking['departure_date'])) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $booking['booking_status'] == 'confirmed' ? 'primary' : 
                                ($booking['booking_status'] == 'paid' ? 'success' : 
                                ($booking['booking_status'] == 'completed' ? 'secondary' : 
                                ($booking['booking_status'] == 'canceled' ? 'danger' : 'warning'))) 
                            ?>">
                                <?= ucfirst($booking['booking_status']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= 
                                $booking['payment_status'] == 'paid' ? 'success' : 
                                ($booking['payment_status'] == 'partial' ? 'info' : 
                                ($booking['payment_status'] == 'failed' ? 'danger' : 'warning')) 
                            ?>">
                                <?= ucfirst($booking['payment_status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/bookings/' . $booking['booking_id']) ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>