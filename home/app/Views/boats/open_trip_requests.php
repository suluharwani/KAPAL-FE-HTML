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
                    <th>Aksi</th>
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
                            <td>
                                <?php if ($status == 'approved' && isset($request['open_trip_id'])): ?>
                                    <a href="<?= base_url('boats/open-trip-members/' . $request['open_trip_id']) ?>" 
                                       class="btn btn-sm btn-info" title="Manage Members">
                                        <i class="fas fa-users"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
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