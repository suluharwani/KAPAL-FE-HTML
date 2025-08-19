<?= $this->include('templates/admin_header') ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Boats</h6>
                        <h2 class="mb-0"><?= $total_boats ?></h2>
                    </div>
                    <i class="bi bi-boat fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Today's Bookings</h6>
                        <h2 class="mb-0"><?= $today_bookings ?></h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pending Payments</h6>
                        <h2 class="mb-0"><?= $pending_payments ?></h2>
                    </div>
                    <i class="bi bi-cash-coin fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">New Messages</h6>
                        <h2 class="mb-0"><?= $new_messages ?></h2>
                    </div>
                    <i class="bi bi-envelope fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking Code</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_bookings as $index => $booking): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $booking['booking_code'] ?></td>
                                <td><?= $booking['full_name'] ?? 'Guest' ?></td>
                                <td><?= date('Y-m-d', strtotime($booking['created_at'])) ?></td>
                                <td>
                                    <?php if ($booking['payment_status'] === 'paid'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($recent_activities as $activity): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted"><?= $activity['time_ago'] ?></small>
                            <p class="mb-0"><?= $activity['description'] ?></p>
                        </div>
                        <?php if ($activity['is_new']): ?>
                            <span class="badge bg-primary rounded-pill">New</span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>