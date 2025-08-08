<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">FAQs Management</h5>
        <a href="<?= base_url('admin/faqs/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th>Category</th>
                        <th>Featured</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faqs as $index => $faq): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($faq['question']) ?></td>
                        <td><?= ucfirst($faq['category']) ?></td>
                        <td>
                            <?php if ($faq['is_featured']): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $faq['display_order'] ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/faqs/edit/' . $faq['faq_id']) ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/faqs/delete/' . $faq['faq_id']) ?>" class="btn btn-danger">
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