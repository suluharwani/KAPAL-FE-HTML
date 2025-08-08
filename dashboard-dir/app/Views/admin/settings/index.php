<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">System Settings</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/settings/update') ?>" method="post">
            <div class="mb-3">
                <label for="site_name" class="form-label">Site Name</label>
                <input type="text" class="form-control" id="site_name" name="site_name" 
                    value="<?= esc($settings['site_name'] ?? 'Raja Ampat Boats') ?>">
            </div>
            
            <div class="mb-3">
                <label for="site_email" class="form-label">Site Email</label>
                <input type="email" class="form-control" id="site_email" name="site_email" 
                    value="<?= esc($settings['site_email'] ?? 'info@rajaampatboats.com') ?>">
            </div>
            
            <div class="mb-3">
                <label for="site_phone" class="form-label">Contact Phone</label>
                <input type="text" class="form-control" id="site_phone" name="site_phone" 
                    value="<?= esc($settings['site_phone'] ?? '+62 812 3456 7890') ?>">
            </div>
            
            <div class="mb-3">
                <label for="site_address" class="form-label">Address</label>
                <textarea class="form-control" id="site_address" name="site_address" rows="3"><?= esc($settings['site_address'] ?? 'Jl. Wisata Bahari, Raja Ampat, Papua Barat') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <input type="text" class="form-control" id="currency" name="currency" 
                    value="<?= esc($settings['currency'] ?? 'IDR') ?>">
            </div>
            
            <div class="mb-3">
                <label for="timezone" class="form-label">Timezone</label>
                <select class="form-select" id="timezone" name="timezone">
                    <option value="Asia/Jakarta" <?= ($settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                    <option value="Asia/Jayapura" <?= ($settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                </select>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode" value="1" 
                    <?= ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' ?>>
                <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>