<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Blog Posts</h5>
        <a href="<?= base_url('admin/blogs/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Published At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blogs as $index => $blog): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($blog['title']) ?></td>
                        <td><?= esc($blog['category_name'] ?? 'Uncategorized') ?></td>
                        <td><?= esc($blog['author_id']) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $blog['status'] == 'published' ? 'success' : 
                                ($blog['status'] == 'draft' ? 'warning text-dark' : 'secondary') 
                            ?>">
                                <?= ucfirst($blog['status']) ?>
                            </span>
                        </td>
                        <td><?= $blog['published_at'] ? date('d M Y', strtotime($blog['published_at'])) : '-' ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/blogs/edit/' . $blog['blog_id']) ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/blogs/delete/' . $blog['blog_id']) ?>" class="btn btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>