<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Route</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/routes/update/' . $route['route_id']) ?>" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="departure_island_id" class="form-label">Departure Island</label>
                        <select class="form-select" id="departure_island_id" name="departure_island_id" required>
                            <option value="">Select Island</option>
                            <?php foreach ($islands as $island): ?>
                            <option value="<?= $island['island_id'] ?>" <?= $route['departure_island_id'] == $island['island_id'] ? 'selected' : '' ?>>
                                <?= esc($island['island_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="arrival_island_id" class="form-label">Arrival Island</label>
                        <select class="form-select" id="arrival_island_id" name="arrival_island_id" required>
                            <option value="">Select Island</option>
                            <?php foreach ($islands as $island): ?>
                            <option value="<?= $island['island_id'] ?>" <?= $route['arrival_island_id'] == $island['island_id'] ? 'selected' : '' ?>>
                                <?= esc($island['island_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="estimated_duration" class="form-label">Estimated Duration</label>
                        <input type="text" class="form-control" id="estimated_duration" name="estimated_duration" 
                            value="<?= esc($route['estimated_duration']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="distance" class="form-label">Distance (km)</label>
                        <input type="number" step="0.01" class="form-control" id="distance" name="distance" 
                            value="<?= $route['distance'] ?>">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?= esc($route['notes']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('admin/routes') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>