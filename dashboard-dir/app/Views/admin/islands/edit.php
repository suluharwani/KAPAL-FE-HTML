<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Island</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/islands/update/' . $island['island_id']) ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="island_name" class="form-label">Island Name</label>
                <input type="text" class="form-control" id="island_name" name="island_name" value="<?= esc($island['island_name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= esc($island['description']) ?></textarea>
            </div>
            
            <?php if ($island['image_url']): ?>
            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div>
                    <img src="<?= base_url($island['image_url']) ?>" class="img-thumbnail" style="max-height: 200px;">
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                    <label class="form-check-label" for="remove_image">Remove current image</label>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="image" class="form-label">New Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="form-text">Leave empty to keep current image (max 5MB)</div>
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('admin/islands') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>