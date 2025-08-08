<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Booking Details - <?= esc($booking['booking_code']) ?></h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <p><strong>Name:</strong> <?= esc($booking['customer_name']) ?></p>
                <p><strong>Email:</strong> <?= esc($booking['email']) ?></p>
                <p><strong>Phone:</strong> <?= esc($booking['phone']) ?></p>
            </div>
            <div class="col-md-6">
                <h6>Trip Information</h6>
                <p><strong>Boat:</strong> <?= esc($booking['boat_name']) ?></p>
                <p><strong>Route:</strong> <?= esc($booking['departure_island']) ?> to <?= esc($booking['arrival_island']) ?></p>
                <p><strong>Date:</strong> <?= date('d M Y', strtotime($booking['departure_date'])) ?></p>
                <p><strong>Time:</strong> <?= date('H:i', strtotime($booking['departure_time'])) ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Booking Status</h6>
                <form action="<?= base_url('admin/bookings/' . $booking['booking_id'] . '/status') ?>" method="post" class="mb-3">
                    <div class="input-group">
                        <select class="form-select" name="status">
                            <option value="pending" <?= $booking['booking_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $booking['booking_status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="paid" <?= $booking['booking_status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="completed" <?= $booking['booking_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="canceled" <?= $booking['booking_status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <h6>Payment Status</h6>
                <form action="<?= base_url('admin/bookings/' . $booking['booking_id'] . '/payment-status') ?>" method="post">
                    <div class="input-group">
                        <select class="form-select" name="status">
                            <option value="pending" <?= $booking['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="partial" <?= $booking['payment_status'] == 'partial' ? 'selected' : '' ?>>Partial</option>
                            <option value="paid" <?= $booking['payment_status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="failed" <?= $booking['payment_status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4">
            <h6>Passengers (<?= count($passengers) ?>)</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>ID Number</th>
                            <th>Phone</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passengers as $passenger): ?>
                        <tr>
                            <td><?= esc($passenger['full_name']) ?></td>
                            <td><?= esc($passenger['identity_number']) ?></td>
                            <td><?= esc($passenger['phone']) ?></td>
                            <td><?= $passenger['age'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= base_url('admin/bookings') ?>" class="btn btn-secondary">Back to List</a>
            <button class="btn btn-success">Print Ticket</button>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>