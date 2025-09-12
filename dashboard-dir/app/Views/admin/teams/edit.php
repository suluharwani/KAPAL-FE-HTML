<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Team Member</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/teams/update/' . $team['team_id']) ?>" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc($team['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position" value="<?= esc($team['position']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Max size: 1MB, Format: JPG, PNG, JPEG</div>
                        <?php if ($team['image']): ?>
                            <div class="mt-2">
                                <img src="<?= base_url($team['image']) ?>" alt="Current Image" style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" value="<?= $team['display_order'] ?>">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?= $team['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio/Description</label>
                        <textarea class="form-control" id="bio" name="bio" rows="5" required><?= esc($team['bio']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Social Media Links</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                            <input type="url" class="form-control" name="social_facebook" placeholder="Facebook URL" value="<?= esc($team['social_facebook']) ?>">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                            <input type="url" class="form-control" name="social_twitter" placeholder="Twitter URL" value="<?= esc($team['social_twitter']) ?>">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                            <input type="url" class="form-control" name="social_instagram" placeholder="Instagram URL" value="<?= esc($team['social_instagram']) ?>">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                            <input type="url" class="form-control" name="social_linkedin" placeholder="LinkedIn URL" value="<?= esc($team['social_linkedin']) ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('admin/teams') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>