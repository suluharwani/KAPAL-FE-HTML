<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Testimonials Management</h5>
        <div class="btn-group">
            <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-sm btn-outline-secondary <?= !$this->request->getGet('status') ? 'active' : '' ?>">All</a>
            <a href="<?= base_url('admin/testimonials?status=pending') ?>" class="btn btn-sm btn-outline-secondary <?= $this->request->getGet('status') == 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="<?= base_url('admin/testimonials?status=approved') ?>" class="btn btn-sm btn-outline-secondary <?= $this->request->getGet('status') == 'approved' ? 'active' : '' ?>">Approved</a>
            <a href="<?= base_url('admin/testimonials?status=rejected') ?>" class="btn btn-sm btn-outline-secondary <?= $this->request->getGet('status') == 'rejected' ? 'active' : '' ?>">Rejected</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= esc($testimonial['user_name'] ?? $testimonial['guest_name']) ?>
                            <?php if ($testimonial['guest_email']): ?>
                                <br><small class="text-muted"><?= esc($testimonial['guest_email']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $testimonial['rating'] ? '-fill text-warning' : '' ?>"></i>
                            <?php endfor; ?>
                        </td>
                        <td><?= character_limiter(esc($testimonial['content']), 50) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $testimonial['status'] == 'approved' ? 'success' : 
                                ($testimonial['status'] == 'rejected' ? 'danger' : 'warning') 
                            ?>">
                                <?= ucfirst($testimonial['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($testimonial['created_at'])) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <form action="<?= base_url('admin/testimonials/' . $testimonial['testimonial_id'] . '/status') ?>" method="post" class="me-2">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                <form action="<?= base_url('admin/testimonials/' . $testimonial['testimonial_id'] . '/status') ?>" method="post" class="me-2">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/testimonials/delete/' . $testimonial['testimonial_id']) ?>" class="btn btn-sm btn-danger">
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