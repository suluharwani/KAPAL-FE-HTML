<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Schedules Management</h5>
        <a href="<?= base_url('admin/schedules/create') ?>" class="btn btn-primary btn-sm">
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
                        <th>Seats</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $index => $schedule): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($schedule['boat_name']) ?></td>
                        <td>
                            <?= esc($schedule['departure_island']) ?> 
                            <i class="bi bi-arrow-right"></i> 
                            <?= esc($schedule['arrival_island']) ?>
                        </td>
                        <td>
                            <?= date('d M Y', strtotime($schedule['departure_date'])) ?><br>
                            <?= date('H:i', strtotime($schedule['departure_time'])) ?>
                        </td>
                        <td><?= $schedule['available_seats'] ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $schedule['status'] == 'available' ? 'success' : 
                                ($schedule['status'] == 'full' ? 'danger' : 'secondary') 
                            ?>">
                                <?= ucfirst($schedule['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($schedule['is_open_trip']): ?>
                                <span class="badge bg-info">Open Trip</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Regular</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/schedules/edit/' . $schedule['schedule_id']) ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/schedules/delete/' . $schedule['schedule_id']) ?>" class="btn btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
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