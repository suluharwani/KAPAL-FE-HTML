<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Boats List</h5>
        <a href="<?= base_url('admin/boats/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Boat Name</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Price/Trip</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($boats as $index => $boat): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($boat['boat_name']) ?></td>
                        <td><?= ucfirst($boat['boat_type']) ?></td>
                        <td><?= $boat['capacity'] ?></td>
                        <td>Rp <?= number_format($boat['price_per_trip'], 0, ',', '.') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/boats/edit/' . $boat['boat_id']) ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/boats/delete/' . $boat['boat_id']) ?>" class="btn btn-danger">
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