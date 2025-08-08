<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Boat</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/boats/store') ?>" method="post">
            <div class="mb-3">
                <label for="boat_name" class="form-label">Boat Name</label>
                <input type="text" class="form-control" id="boat_name" name="boat_name" required>
            </div>
            
            <div class="mb-3">
                <label for="boat_type" class="form-label">Boat Type</label>
                <select class="form-select" id="boat_type" name="boat_type" required>
                    <option value="">Select Type</option>
                    <option value="speedboat">Speedboat</option>
                    <option value="traditional">Traditional</option>
                    <option value="luxury">Luxury</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" required>
            </div>
            
            <div class="mb-3">
                <label for="price_per_trip" class="form-label">Price per Trip (Rp)</label>
                <input type="number" class="form-control" id="price_per_trip" name="price_per_trip" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="facilities" class="form-label">Facilities (comma separated)</label>
                <textarea class="form-control" id="facilities" name="facilities" rows="3" placeholder="Life jacket, Toilet, Snorkeling gear, etc"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?= base_url('admin/boats') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>