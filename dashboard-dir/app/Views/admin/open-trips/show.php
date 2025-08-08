<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Open Trip Details</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Trip Information</h6>
                <p><strong>Boat:</strong> <?= esc($openTrip['boat_name']) ?></p>
                <p><strong>Route:</strong> <?= esc($openTrip['departure_island']) ?> to <?= esc($openTrip['arrival_island']) ?></p>
                <p><strong>Date:</strong> <?= date('d M Y', strtotime($openTrip['departure_date'])) ?></p>
                <p><strong>Time:</strong> <?= date('H:i', strtotime($openTrip['departure_time'])) ?></p>
                <p><strong>Duration:</strong> <?= esc($openTrip['estimated_duration']) ?></p>
                <p><strong>Price per Trip:</strong> Rp <?= number_format($openTrip['price_per_trip'], 0, ',', '.') ?></p>
            </div>
            <div class="col-md-6">
                <h6>Requester Information</h6>
                <p><strong>Name:</strong> <?= esc($openTrip['requester_name']) ?></p>
                <p><strong>Email:</strong> <?= esc($openTrip['requester_email']) ?></p>
                <p><strong>Phone:</strong> <?= esc($openTrip['requester_phone']) ?></p>
                <p><strong>Min Passengers:</strong> <?= $openTrip['min_passengers'] ?></p>
                <p><strong>Max Passengers:</strong> <?= $openTrip['max_passengers'] ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?= 
                        $openTrip['status'] == 'upcoming' ? 'primary' : 
                        ($openTrip['status'] == 'ongoing' ? 'success' : 
                        ($openTrip['status'] == 'completed' ? 'secondary' : 'danger')) 
                    ?>">
                        <?= ucfirst($openTrip['status']) ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="mb-4">
            <h6>Bookings (<?= count($bookings) ?>)</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Passengers</th>
                            <th>Total Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= esc($booking['booking_code']) ?></td>
                            <td><?= esc($booking['full_name']) ?></td>
                            <td><?= ucfirst($booking['open_trip_type']) ?></td>
                            <td><?= $booking['passenger_count'] ?></td>
                            <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
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
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= base_url('admin/open-trips') ?>" class="btn btn-secondary">Back to List</a>
            <div class="btn-group">
                <?php if ($openTrip['status'] == 'upcoming'): ?>
                <a href="<?= base_url('admin/open-trips/' . $openTrip['open_trip_id'] . '/status/ongoing') ?>" class="btn btn-success">
                    <i class="bi bi-play"></i> Start Trip
                </a>
                <a href="<?= base_url('admin/open-trips/' . $openTrip['open_trip_id'] . '/status/canceled') ?>" class="btn btn-danger">
                    <i class="bi bi-x"></i> Cancel Trip
                </a>
                <?php elseif ($openTrip['status'] == 'ongoing'): ?>
                <a href="<?= base_url('admin/open-trips/' . $openTrip['open_trip_id'] . '/status/completed') ?>" class="btn btn-secondary">
                    <i class="bi bi-check"></i> Complete Trip
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>