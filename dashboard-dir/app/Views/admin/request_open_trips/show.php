<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Open Trip Request Details</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Requester Information</h6>
                <p><strong>Name:</strong> <?= esc($request['requester_name']) ?></p>
                <p><strong>Email:</strong> <?= esc($request['requester_email']) ?></p>
                <p><strong>Phone:</strong> <?= esc($request['requester_phone']) ?></p>
            </div>
            <div class="col-md-6">
                <h6>Trip Information</h6>
                <p><strong>Boat:</strong> <?= esc($request['boat_name']) ?> (Capacity: <?= $request['capacity'] ?>)</p>
                <p><strong>Route:</strong> <?= esc($request['departure_island']) ?> to <?= esc($request['arrival_island']) ?></p>
                <p><strong>Duration:</strong> <?= esc($request['estimated_duration']) ?></p>
                <p><strong>Proposed Date:</strong> <?= date('d M Y', strtotime($request['proposed_date'])) ?></p>
                <p><strong>Proposed Time:</strong> <?= date('H:i', strtotime($request['proposed_time'])) ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Passenger Requirements</h6>
                <p><strong>Minimum Passengers:</strong> <?= $request['min_passengers'] ?></p>
                <p><strong>Maximum Passengers:</strong> <?= $request['max_passengers'] ?></p>
            </div>
            <div class="col-md-6">
                <h6>Status</h6>
                <p>
                    <span class="badge bg-<?= 
                        $request['status'] == 'approved' ? 'success' : 
                        ($request['status'] == 'rejected' ? 'danger' : 'warning') 
                    ?>">
                        <?= ucfirst($request['status']) ?>
                    </span>
                </p>
                <?php if ($request['admin_notes']): ?>
                <p><strong>Admin Notes:</strong> <?= esc($request['admin_notes']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-4">
            <h6>Requester Notes</h6>
            <div class="border p-3 bg-light">
                <?= nl2br(esc($request['notes'])) ?>
            </div>
        </div>

        <form action="<?= base_url('admin/request-open-trips/' . $request['request_id'] . '/status/approved') ?>" method="post">
            <div class="mb-3">
                <label for="admin_notes" class="form-label">Admin Notes</label>
                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?= esc($request['admin_notes']) ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('admin/request-open-trips') ?>" class="btn btn-secondary">Back to List</a>
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">Approve Request</button>
                    <a href="<?= base_url('admin/request-open-trips/' . $request['request_id'] . '/status/rejected') ?>" class="btn btn-danger">Reject Request</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>