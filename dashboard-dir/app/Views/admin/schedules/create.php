<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Schedule</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/schedules/store') ?>" method="post">
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

            <div class="mb-3">
                <label for="available_seats" class="form-label">Available Seats</label>
                <input type="number" class="form-control" id="available_seats" name="available_seats" min="1" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_open_trip" name="is_open_trip" value="1">
                <label class="form-check-label" for="is_open_trip">Is Open Trip?</label>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?= base_url('admin/schedules') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>