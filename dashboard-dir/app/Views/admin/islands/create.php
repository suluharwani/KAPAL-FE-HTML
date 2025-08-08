<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Island</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/islands/store') ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="island_name" class="form-label">Island Name</label>
                <input type="text" class="form-control" id="island_name" name="island_name" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="form-text">Optional island image (max 5MB)</div>
            </div>
            
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?= base_url('admin/islands') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>