<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Create Open Trip</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/open-trips/store') ?>" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="boat_id" class="form-label">Boat</label>
                        <select class="form-select" id="boat_id" name="boat_id" required>
                            <option value="">Select Boat</option>
                            <?php foreach ($boats as $boat): ?>
                            <option value="<?= $boat['boat_id'] ?>"><?= esc($boat['boat_name']) ?> (Capacity: <?= $boat['capacity'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
    <div class="mb-3">
        <label for="route_id" class="form-label">Route</label>
        <select class="form-select" id="route_id" name="route_id" required>
            <option value="">Select Route</option>
            <?php foreach ($routes as $route): ?>
                <option value="<?= $route['route_id'] ?>">
                    <?= esc($route['departure_island']) ?> to <?= esc($route['arrival_island']) ?>
                    (<?= esc($route['estimated_duration']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="departure_date" class="form-label">Departure Date</label>
                        <input type="date" class="form-control" id="departure_date" name="departure_date" min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="departure_time" class="form-label">Departure Time</label>
                        <input type="time" class="form-control" id="departure_time" name="departure_time" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="min_passengers" class="form-label">Minimum Passengers</label>
                        <input type="number" class="form-control" id="min_passengers" name="min_passengers" min="1" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="max_passengers" class="form-label">Maximum Passengers</label>
                        <input type="number" class="form-control" id="max_passengers" name="max_passengers" min="1" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Create Open Trip</button>
            <a href="<?= base_url('admin/open-trips') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>