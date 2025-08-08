<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Message Details</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Name:</strong> <?= esc($contact['name']) ?></p>
                <p><strong>Email:</strong> <?= esc($contact['email']) ?></p>
                <p><strong>Phone:</strong> <?= esc($contact['phone']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Subject:</strong> <?= esc($contact['subject']) ?></p>
                <p><strong>Date:</strong> <?= date('d M Y H:i', strtotime($contact['created_at'])) ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?= 
                        $contact['status'] == 'unread' ? 'danger' : 
                        ($contact['status'] == 'read' ? 'primary' : 
                        ($contact['status'] == 'replied' ? 'success' : 'secondary')) 
                    ?>">
                        <?= ucfirst($contact['status']) ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="mb-4">
            <h6>Message</h6>
            <div class="border p-3 bg-light">
                <?= nl2br(esc($contact['message'])) ?>
            </div>
        </div>

        <form action="<?= base_url('admin/contacts/' . $contact['contact_id'] . '/status') ?>" method="post">
            <div class="mb-3">
                <label for="status" class="form-label">Update Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="unread" <?= $contact['status'] == 'unread' ? 'selected' : '' ?>>Unread</option>
                    <option value="read" <?= $contact['status'] == 'read' ? 'selected' : '' ?>>Read</option>
                    <option value="replied" <?= $contact['status'] == 'replied' ? 'selected' : '' ?>>Replied</option>
                    <option value="spam" <?= $contact['status'] == 'spam' ? 'selected' : '' ?>>Spam</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="reply" class="form-label">Reply Message</label>
                <textarea class="form-control" id="reply" name="reply" rows="5"></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('admin/contacts') ?>" class="btn btn-secondary">Back to List</a>
                <button type="submit" class="btn btn-primary">Update & Send Reply</button>
            </div>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>