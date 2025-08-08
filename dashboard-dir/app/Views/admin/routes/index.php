<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Routes Management</h5>
        <a href="<?= base_url('admin/routes/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Route</th>
                        <th>Duration</th>
                        <th>Distance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($routes as $index => $route): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= esc($route['departure_island']) ?> 
                            <i class="bi bi-arrow-right"></i> 
                            <?= esc($route['arrival_island']) ?>
                        </td>
                        <td><?= esc($route['estimated_duration']) ?></td>
                        <td><?= $route['distance'] ? $route['distance'] . ' km' : '-' ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/routes/edit/' . $route['route_id']) ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/routes/delete/' . $route['route_id']) ?>" class="btn btn-danger">
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