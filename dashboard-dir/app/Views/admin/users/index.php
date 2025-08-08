<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">User Management</h5>
        <div class="btn-group">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary <?= !$_SESSION['role'] ? 'active' : '' ?>">All</a>
            <a href="<?= base_url('admin/users?role=admin') ?>" class="btn btn-sm btn-outline-secondary <?= $_SESSION['role'] == 'admin' ? 'active' : '' ?>">Admins</a>
            <a href="<?= base_url('admin/users?role=customer') ?>" class="btn btn-sm btn-outline-secondary <?= $_SESSION['role'] == 'customer' ? 'active' : '' ?>">Customers</a>
        </div>
        <div class="input-group ms-2" style="width: 300px;">
            <form action="<?= base_url('admin/users') ?>" method="get" class="d-flex w-100">
                <input type="text" class="form-control form-control-sm" name="search" placeholder="Search..." 
                    value="">
                <button class="btn btn-outline-secondary btn-sm" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm ms-2">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($user['username']) ?></td>
                        <td><?= esc($user['full_name']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['phone']) ?></td>
                        <td>
                            <span class="badge bg-<?= $user['role'] == 'admin' ? 'primary' : 'success' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/users/edit/' . $user['user_id']) ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button onclick="confirmDelete(this)" data-url="<?= base_url('admin/users/delete/' . $user['user_id']) ?>" class="btn btn-danger" <?= $user['user_id'] == session()->get('user_id') ? 'disabled' : '' ?>>
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