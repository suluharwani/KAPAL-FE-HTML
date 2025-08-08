<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Contact Messages</h5>
        <div class="btn-group">
            <a href="<?= base_url('admin/contacts') ?>" class="btn btn-sm btn-outline-secondary <?= !$contacts ? 'active' : '' ?>">All</a>
            <a href="<?= base_url('admin/contacts?status=unread') ?>" class="btn btn-sm btn-outline-secondary <?=$contacts == 'unread' ? 'active' : '' ?>">Unread</a>
            <a href="<?= base_url('admin/contacts?status=read') ?>" class="btn btn-sm btn-outline-secondary <?=$contacts == 'read' ? 'active' : '' ?>">Read</a>
            <a href="<?= base_url('admin/contacts?status=replied') ?>" class="btn btn-sm btn-outline-secondary <?=$contacts == 'replied' ? 'active' : '' ?>">Replied</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $index => $contact): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($contact['name']) ?></td>
                        <td><?= esc($contact['email']) ?></td>
                        <td><?= esc($contact['subject']) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $contact['status'] == 'unread' ? 'danger' : 
                                ($contact['status'] == 'read' ? 'primary' : 
                                ($contact['status'] == 'replied' ? 'success' : 'secondary')) 
                            ?>">
                                <?= ucfirst($contact['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($contact['created_at'])) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/contacts/' . $contact['contact_id']) ?>" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/contacts/delete/' . $contact['contact_id']) ?>" class="btn btn-danger">
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