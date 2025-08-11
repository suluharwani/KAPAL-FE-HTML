<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Open Trip Requests</h5>
        <div class="btn-group">
            <a href="<?= base_url('admin/request-open-trips') ?>" class="btn btn-sm btn-outline-secondary <?= !$this->request->getGet('status') ? 'active' : '' ?>">All</a>
            <a href="<?= base_url('admin/request-open-trips?status=pending') ?>" class="btn btn-sm btn-outline-secondary <?= $this->request->getGet('status') == 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="<?= base_url('admin/request-open-trips?status=approved') ?>" class="btn btn-sm btn-outline-secondary <?= $this->request->getGet('status') == 'approved' ? 'active' : '' ?>">Approved</a>
            <a href="<?= base_url('admin/request-open-trips?status=rejected') ?>" class="btn btn-sm btn-outline-secondary <?= $this->request->getGet('status') == 'rejected' ? 'active' : '' ?>">Rejected</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Requester</th>
                        <th>Boat</th>
                        <th>Route</th>
                        <th>Proposed Date</th>
                        <th>Passengers</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $index => $request): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($request['requester_name']) ?></td>
                        <td><?= esc($request['boat_name']) ?></td>
                        <td><?= esc($request['departure_island']) ?> to <?= esc($request['arrival_island']) ?></td>
                        <td>
                            <?= date('d M Y', strtotime($request['proposed_date'])) ?><br>
                            <?= date('H:i', strtotime($request['proposed_time'])) ?>
                        </td>
                        <td><?= $request['min_passengers'] ?>-<?= $request['max_passengers'] ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $request['status'] == 'approved' ? 'success' : 
                                ($request['status'] == 'rejected' ? 'danger' : 'warning') 
                            ?>">
                                <?= ucfirst($request['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/request-open-trips/' . $request['request_id']) ?>" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($request['status'] == 'pending'): ?>
                                <a href="<?= base_url('admin/request-open-trips/' . $request['request_id'] . '/status/approved') ?>" class="btn btn-success">
                                    <i class="bi bi-check"></i>
                                </a>
                                <a href="<?= base_url('admin/request-open-trips/' . $request['request_id'] . '/status/rejected') ?>" class="btn btn-danger">
                                    <i class="bi bi-x"></i>
                                </a>
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