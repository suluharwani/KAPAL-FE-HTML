<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Open Trips Management</h5>
        <div class="btn-group">
            <a href="<?= base_url('admin/open-trips') ?>" class="btn btn-sm btn-outline-secondary <?= !$status ? 'active' : '' ?>">All</a>
            <a href="<?= base_url('admin/open-trips?status=upcoming') ?>" class="btn btn-sm btn-outline-secondary <?= $status == 'upcoming' ? 'active' : '' ?>">Upcoming</a>
            <a href="<?= base_url('admin/open-trips?status=ongoing') ?>" class="btn btn-sm btn-outline-secondary <?= $status == 'ongoing' ? 'active' : '' ?>">Ongoing</a>
            <a href="<?= base_url('admin/open-trips?status=completed') ?>" class="btn btn-sm btn-outline-secondary <?= $status == 'completed' ? 'active' : '' ?>">Completed</a>
            <a href="<?= base_url('admin/open-trips?status=pending') ?>" class="btn btn-sm btn-outline-warning <?= $status == 'pending' ? 'active' : '' ?>">Pending Requests</a>
        </div>
        <a href="<?= base_url('admin/open-trips/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Boat</th>
                        <th>Route</th>
                        <th>Date & Time</th>
                        <th>Requester</th>
                        <th>Seats</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($openTrips as $index => $trip): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($trip['boat_name']) ?></td>
                        <td><?= esc($trip['departure_island']) ?> to <?= esc($trip['arrival_island']) ?></td>
                        <td>
                            <?= date('d M Y', strtotime($status == 'pending' ? $trip['proposed_date'] : $trip['departure_date'])) ?><br>
                            <?= date('H:i', strtotime($status == 'pending' ? $trip['proposed_time'] : $trip['departure_time'])) ?>
                        </td>
                        <td><?= esc($trip['requester_name']) ?></td>
                        <td>
                            <?php if ($status == 'pending'): ?>
                                <?= $trip['min_passengers'] ?> - <?= $trip['max_passengers'] ?>
                            <?php else: ?>
                                <?= $trip['reserved_seats'] + $trip['available_seats'] - $trip['reserved_seats'] ?> / 
                                <?= $trip['reserved_seats'] + $trip['available_seats'] ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= 
                                $trip['status'] == 'upcoming' ? 'primary' : 
                                ($trip['status'] == 'ongoing' ? 'success' : 
                                ($trip['status'] == 'completed' ? 'secondary' : 
                                ($trip['status'] == 'pending' ? 'warning' : 'danger'))) 
                            ?>">
                                <?= ucfirst($trip['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <?php if ($status == 'pending'): ?>
                                    <form action="<?= base_url('admin/open-trips/approve-request/' . $trip['request_id']) ?>" method="post">
                                        <button type="submit" class="btn btn-success" title="Approve">
                                            <i class="bi bi-check"></i> Approve
                                        </button>
                                    </form>
                                    <a href="<?= base_url('admin/request-open-trips/' . $trip['request_id'] . '/status/rejected') ?>" class="btn btn-danger" title="Reject">
                                        <i class="bi bi-x"></i> Reject
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id']) ?>" class="btn btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($trip['status'] == 'upcoming'): ?>
                                    <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id'] . '/status/ongoing') ?>" class="btn btn-success" title="Start Trip">
                                        <i class="bi bi-play"></i>
                                    </a>
                                    <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id'] . '/status/canceled') ?>" class="btn btn-danger" title="Cancel Trip">
                                        <i class="bi bi-x"></i>
                                    </a>
                                    <?php elseif ($trip['status'] == 'ongoing'): ?>
                                    <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id'] . '/status/completed') ?>" class="btn btn-secondary" title="Complete Trip">
                                        <i class="bi bi-check"></i>
                                    </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>