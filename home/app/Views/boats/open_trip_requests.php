<!-- Main Content -->
<main class="container my-5">
    <h2 class="text-center mb-4">My Open Trip Requests</h2>
    
    <div class="alert alert-info">
        <p>Berikut adalah daftar permintaan open trip yang telah Anda ajukan. Status akan diperbarui setelah admin memverifikasi.</p>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Request ID</th>
                    <th>Rute</th>
                    <th>Kapal</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Penumpang</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="9" class="text-center">Anda belum membuat permintaan open trip</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td>REQ-<?= str_pad($request['request_id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= $request['departure_island_name'] ?> - <?= $request['arrival_island_name'] ?></td>
                            <td><?= $request['boat_name'] ?> (<?= $request['boat_type'] ?>)</td>
                            <td><?= date('d M Y', strtotime($request['proposed_date'])) ?></td>
                            <td><?= date('H:i', strtotime($request['proposed_time'])) ?></td>
                            <td><?= $request['min_passengers'] ?>-<?= $request['max_passengers'] ?> orang</td>
                            <td>
                                <?php 
                                    $badgeClass = [
                                        'pending' => 'bg-warning',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'completed' => 'bg-primary'
                                    ];
                                    $status = $request['status'] ?? 'pending';
                                ?>
                                <span class="badge <?= $badgeClass[$status] ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td><?= $request['notes'] ?? '-' ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <?php if ($status == 'approved' && isset($request['open_trip_id'])): ?>
                                        <a href="<?= base_url('boats/open-trip-members/' . $request['open_trip_id']) ?>" 
                                           class="btn btn-sm btn-info" title="Kelola Member" data-bs-toggle="tooltip">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="<?= base_url('boats/open-trip-details/' . $request['open_trip_id']) ?>" 
                                           class="btn btn-sm btn-primary" title="Detail Trip" data-bs-toggle="tooltip">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($status == 'pending'): ?>
                                        <button class="btn btn-sm btn-warning edit-request" 
                                                data-request-id="<?= $request['request_id'] ?>"
                                                title="Edit Request" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger cancel-request" 
                                                data-request-id="<?= $request['request_id'] ?>"
                                                title="Batalkan Request" data-bs-toggle="tooltip">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($status == 'approved'): ?>
                                        <button class="btn btn-sm btn-info tomemberpage" 
                                                data-request-id="<?= $request['request_id'] ?>"
                                                title="Ke Halaman Member" data-bs-toggle="tooltip">
                                            <i class="fas fa-users"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-center mt-4">
        <a href="<?= base_url('boats/open-trip') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Open Trip
        </a>
    </div>
</main>

<!-- Modal untuk Edit Request -->
<div class="modal fade" id="editRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Open Trip Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRequestForm">
                <div class="modal-body" id="editRequestContent">
                    <!-- Konten akan diisi via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Aktifkan tooltip
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Handle edit request button
    $('.edit-request').click(function() {
        const requestId = $(this).data('request-id');
        
        $.get('<?= base_url('boats/get-request-details') ?>/' + requestId, function(response) {
            if (response.success) {
                $('#editRequestContent').html(response.html);
                $('#editRequestModal').modal('show');
            } else {
                alert(response.error || 'Gagal memuat data request');
            }
        }).fail(function() {
            alert('Error loading request details');
        });
    });
    
    // Handle cancel request button
    $('.cancel-request').click(function() {
        const requestId = $(this).data('request-id');
        
        if (confirm('Apakah Anda yakin ingin membatalkan request ini?')) {
            $.post('<?= base_url('boats/cancel-request') ?>', {
                request_id: requestId
            }, function(response) {
                if (response.success) {
                    alert('Request berhasil dibatalkan');
                    location.reload();
                } else {
                    alert(response.error || 'Gagal membatalkan request');
                }
            });
        }
    });
    
    // Handle complete request button
$('.tomemberpage').click(function() {
    const requestId = $(this).data('request-id');
    
    // Cek apakah request ini sudah memiliki open_trip_id
    $.get('<?= base_url('boats/get-open-trip-id') ?>', {
        request_id: requestId
    }, function(response) {
        if (response.success && response.open_trip_id) {
            // Redirect ke halaman member
            window.location.href = '<?= base_url('boats/open-trip-members/') ?>' + response.open_trip_id;
        } else {
            alert('Belum ada open trip yang dibuat untuk request ini atau terjadi kesalahan');
        }
    }).fail(function() {
        alert('Error checking request status');
    });
});
    
    // Handle edit form submission
    $('#editRequestForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('boats/update-request') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Request berhasil diperbarui');
                    $('#editRequestModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.error || 'Gagal memperbarui request');
                }
            },
            error: function() {
                alert('Error submitting form');
            }
        });
    });
});
</script>