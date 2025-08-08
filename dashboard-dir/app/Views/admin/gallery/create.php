<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Gallery Item</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/gallery/store') ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="kapal">Boat</option>
                    <option value="wisata">Tourism</option>
                    <option value="penumpang">Passenger</option>
                    <option value="pulau">Island</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                <div class="form-text">Main image (max 5MB)</div>
            </div>
            
            <div class="mb-3">
                <label for="thumbnail" class="form-label">Thumbnail (Optional)</label>
                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                <div class="form-text">Leave empty to auto-generate thumbnail</div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                <label class="form-check-label" for="is_featured">Featured Item</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="<?= base_url('admin/gallery') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>