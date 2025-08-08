<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gallery Management</h5> 
        <div class="btn-group">
            <a href="<?= base_url('admin/gallery') ?>" class="btn btn-sm btn-outline-secondary <?= !$category ? 'active' : '' ?>">All</a>
            <a href="<?= base_url('admin/gallery?category=kapal') ?>" class="btn btn-sm btn-outline-secondary <?= $category == 'kapal' ? 'active' : '' ?>">Boats</a>
            <a href="<?= base_url('admin/gallery?category=wisata') ?>" class="btn btn-sm btn-outline-secondary <?= $category == 'wisata' ? 'active' : '' ?>">Tourism</a>
            <a href="<?= base_url('admin/gallery?category=penumpang') ?>" class="btn btn-sm btn-outline-secondary <?= $category == 'penumpang' ? 'active' : '' ?>">Passengers</a>
            <a href="<?= base_url('admin/gallery?category=pulau') ?>" class="btn btn-sm btn-outline-secondary <?= $category == 'pulau' ? 'active' : '' ?>">Islands</a>
        </div>
        <a href="<?= base_url('admin/gallery/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($gallery as $item): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?= base_url($item['thumbnail_url']) ?>" class="card-img-top" alt="<?= esc($item['title']) ?>">
                    <div class="card-body">
                        <h6 class="card-title"><?= esc($item['title']) ?></h6>
                        <p class="card-text text-muted small">
                            <span class="badge bg-<?= 
                                $item['category'] == 'kapal' ? 'primary' : 
                                ($item['category'] == 'wisata' ? 'success' : 
                                ($item['category'] == 'penumpang' ? 'info' : 'warning')) 
                            ?>">
                                <?= ucfirst($item['category']) ?>
                            </span>
                            <?php if ($item['is_featured']): ?>
                                <span class="badge bg-danger">Featured</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted"><?= date('d M Y', strtotime($item['created_at'])) ?></small>
                            <div>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/gallery/delete/' . $item['gallery_id']) ?>" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>