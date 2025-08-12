<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Testimonials</h5>
        <div class="btn-group">
            <a href="?status=approved" class="btn btn-sm btn-outline-success">Approved</a>
            <a href="?status=pending" class="btn btn-sm btn-outline-warning">Pending</a>
            <a href="?status=rejected" class="btn btn-sm btn-outline-danger">Rejected</a>
            <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-sm btn-outline-secondary">All</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="testimonialsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User/Guest</th>
                        <th>Content</th>
                        <th>Rating</th>
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
                            <?= $testimonial['user_name'] ?? $testimonial['guest_name'] ?>
                            <?php if (isset($testimonial['guest_email'])): ?>
                                <br><small><?= $testimonial['guest_email'] ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= character_limiter($testimonial['content'], 100) ?></td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $testimonial['rating'] ? '-fill text-warning' : '' ?>"></i>
                            <?php endfor; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= 
                                $testimonial['status'] == 'approved' ? 'success' : 
                                ($testimonial['status'] == 'pending' ? 'warning text-dark' : 'danger')
                            ?>">
                                <?= ucfirst($testimonial['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($testimonial['created_at'])) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <form method="post" action="<?= base_url('admin/testimonials/'.$testimonial['testimonial_id'].'/status') ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                <form method="post" action="<?= base_url('admin/testimonials/'.$testimonial['testimonial_id'].'/status') ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                                <button onclick="confirmDelete(<?= $testimonial['testimonial_id'] ?>)" class="btn btn-sm btn-outline-danger" title="Delete">
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

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('admin/testimonials/delete/') ?>' + id;
        }
    });
}
</script>

<?= $this->include('templates/admin_footer') ?>