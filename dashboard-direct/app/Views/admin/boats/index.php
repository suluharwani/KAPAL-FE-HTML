<?= $this->extend('templates/admin_header') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Boats Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Boats</li>
    </ol>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-ship me-1"></i>
                    Boats List
                </div>
                <a href="<?= base_url('admin/boats/add') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Boat
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="boatsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Price/Trip</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($boats as $boat): ?>
                        <tr>
                            <td><?= $boat['boat_id'] ?></td>
                            <td><?= $boat['boat_name'] ?></td>
                            <td><?= ucfirst($boat['boat_type']) ?></td>
                            <td><?= $boat['capacity'] ?></td>
                            <td>Rp <?= number_format($boat['price_per_trip'], 0, ',', '.') ?></td>
                            <td>
                                <a href="<?= base_url('admin/boats/edit/' . $boat['boat_id']) ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-boat" data-id="<?= $boat['boat_id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $pager->links() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#boatsTable').DataTable({
        responsive: true
    });
    
    // Delete boat with SweetAlert confirmation
    $('.delete-boat').click(function() {
        const boatId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('admin/boats/delete') ?>/" + boatId;
            }
        });
    });
});
</script>
<?= $this->endSection() ?>