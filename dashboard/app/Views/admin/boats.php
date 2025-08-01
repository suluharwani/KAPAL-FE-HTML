<?= $this->include('templates/header') ?>

<h1 class="h3 mb-4">Manajemen Kapal</h1>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kapal</h5>
        <a href="/admin/boats/add" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Kapal
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="boatsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kapal</th>
                        <th>Tipe</th>
                        <th>Kapasitas</th>
                        <th>Harga/Trip</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($boats as $boat): ?>
                        <tr>
                            <td><?= $boat['id'] ?></td>
                            <td><?= $boat['boat_name'] ?></td>
                            <td><?= ucfirst($boat['boat_type']) ?></td>
                            <td><?= $boat['capacity'] ?> orang</td>
                            <td>Rp <?= number_format($boat['price_per_trip'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= $boat['status'] == 'available' ? 'success' : 'danger' ?>">
                                    <?= $boat['status'] == 'available' ? 'Tersedia' : 'Tidak Tersedia' ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/boats/edit/<?= $boat['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-boat" data-id="<?= $boat['id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus kapal ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>

<script>
$(document).ready(function() {
    // Handle delete button click
    $('.delete-boat').click(function() {
        var boatId = $(this).data('id');
        $('#confirmDelete').attr('href', '/admin/boats/delete/' + boatId);
        $('#deleteModal').modal('show');
    });
});
</script>