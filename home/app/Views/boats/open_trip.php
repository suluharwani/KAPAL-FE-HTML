<!-- Main Content -->
<main class="container my-5">
    <h2 class="text-center mb-4">Open Trip</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Jadwal Open Trip</h3>
                </div>
                <div class="card-body">
                    <p>Berikut adalah jadwal open trip yang tersedia. Anda bisa bergabung dengan open trip yang sudah ada atau membuat permintaan baru.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#requestOpenTripModal">
                <i class="fas fa-plus me-2"></i>Request Open Trip Baru
            </button>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('boats/my-open-trip-requests') ?>" class="btn btn-outline-primary btn-lg w-100">
    <i class="fas fa-list me-2"></i>My Requests
</a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Rute</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Kapal</th>
                    <th>Kapasitas</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($openTrips as $trip): ?>
                    <tr>
                        <td><?= $trip['departure_island'] ?> - <?= $trip['arrival_island'] ?></td>
                        <td><?= date('d M Y', strtotime($trip['departure_date'])) ?></td>
                        <td><?= date('H:i', strtotime($trip['departure_time'])) ?></td>
                        <td><?= $trip['boat_name'] ?></td>
                        <td><?= $trip['available_seats'] ?>/<?= $trip['capacity'] ?> orang</td>
                        <td>Rp <?= number_format($trip['price_per_trip'], 0, ',', '.') ?></td>
                        <td>
                            <span class="badge bg-info"><?= ucfirst($trip['status']) ?></span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary join-btn" 
                                    data-trip-id="<?= $trip['open_trip_id'] ?>"
                                    data-boat-name="<?= $trip['boat_name'] ?>"
                                    data-price="<?= $trip['price_per_trip'] ?>">
                                Gabung
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Request Open Trip Modal -->
    <div class="modal fade" id="requestOpenTripModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Open Trip Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="openTripRequestForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="requestBoat" class="form-label">Kapal</label>
                                <select class="form-select" id="requestBoat" name="boat_id" required>
                                    <option value="" selected disabled>Pilih Kapal</option>
                                    <?php foreach ($boats as $boat): ?>
                                        <option value="<?= $boat['boat_id'] ?>"><?= $boat['boat_name'] ?> (<?= $boat['boat_type'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="requestRoute" class="form-label">Rute</label>
                                <select class="form-select" id="requestRoute" name="route_id" required>
                                    <option value="" selected disabled>Pilih Rute</option>
                                    <?php foreach ($routes as $route): ?>
                                        <option value="<?= $route['route_id'] ?>">
                                            <?= $route['departure_island_name'] ?> - <?= $route['arrival_island_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="proposedDate" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="proposedDate" name="proposed_date" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proposedTime" class="form-label">Waktu</label>
                                <input type="time" class="form-control" id="proposedTime" name="proposed_time" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="minPassengers" class="form-label">Minimal Penumpang</label>
                                <input type="number" class="form-control" id="minPassengers" name="min_passengers" min="2" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="maxPassengers" class="form-label">Maksimal Penumpang</label>
                                <input type="number" class="form-control" id="maxPassengers" name="max_passengers" min="2" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="requestNotes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="requestNotes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ajukan Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div class="modal fade" id="requestSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Request Berhasil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <p id="requestSuccessMessage"></p>
                        <p class="fw-bold">Request ID: <span id="requestId"></span></p>
                    </div>
                    <div class="alert alert-info">
                        <p>Request Anda akan diverifikasi oleh admin. Kami akan mengirimkan notifikasi via email setelah request disetujui.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    // Handle open trip request form submission
    $('#openTripRequestForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('boats/request-open-trip') ?>',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#requestOpenTripModal').modal('hide');
                    $('#requestSuccessMessage').text(response.message);
                    $('#requestId').text(response.request_id);
                    $('#requestSuccessModal').modal('show');
                    
                    // Reload page after 3 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    alert(response.error || 'Terjadi kesalahan');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response.errors) {
                    let errorMessages = '';
                    for (const key in response.errors) {
                        errorMessages += response.errors[key] + '\n';
                    }
                    alert(errorMessages);
                } else {
                    alert(response.error || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    // Set max passengers based on boat capacity when boat is selected
    $('#requestBoat').change(function() {
        const boatId = $(this).val();
        if (boatId) {
            $.ajax({
                url: '<?= base_url('boats/get-boat-capacity') ?>',
                type: 'POST',
                dataType: 'json',
                data: { boat_id: boatId },
                success: function(response) {
                    if (response.success) {
                        $('#maxPassengers').attr('max', response.capacity);
                    }
                }
            });
        }
    });
    
    // Join open trip button
    $('.join-btn').click(function() {
        const tripId = $(this).data('trip-id');
        const boatName = $(this).data('boat-name');
        const price = $(this).data('price');
        
        // Similar to regular booking, but with open_trip_id
        // You can reuse the booking modal with additional hidden field for open_trip_id
        $('#modalScheduleId').val('');
        $('#modalOpenTripId').val(tripId);
        $('#modalBoatName').val(boatName);
        $('#modalPrice').val('Rp ' + price.toLocaleString('id-ID'));
        $('#passengerCount').val(1);
        $('#passengerNamesContainer').html('<input type="text" class="form-control mb-2" name="passenger_names[]" placeholder="Nama Penumpang 1" required>');
        
        $('#bookingModal').modal('show');
    });
});
</script>