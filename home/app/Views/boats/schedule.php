<!-- Main Content -->
<main class="container my-5">
    <h2 class="text-center mb-4">Boat Schedule</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Search Schedule</h3>
        </div>
        <div class="card-body">
            <form id="scheduleSearchForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="searchFromIsland" class="form-label">From Island</label>
                        <select class="form-select" id="searchFromIsland">
                            <option value="">All Islands</option>
                            <?php foreach ($islands as $island): ?>
                                <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="searchToIsland" class="form-label">To Island</label>
                        <select class="form-select" id="searchToIsland">
                            <option value="">All Islands</option>
                            <?php foreach ($islands as $island): ?>
                                <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="searchDate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="searchDate">
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Route</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Boat</th>
                    <th>Capacity</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="scheduleTableBody">
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= $schedule['departure_island'] ?> - <?= $schedule['arrival_island'] ?></td>
                        <td><?= date('d M Y', strtotime($schedule['departure_date'])) ?></td>
                        <td><?= date('H:i', strtotime($schedule['departure_time'])) ?></td>
                        <td><?= $schedule['boat_name'] ?></td>
                        <td><?= $schedule['capacity'] ?> people</td>
                        <td>Rp <?= number_format($schedule['price_per_trip'], 0, ',', '.') ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary book-btn" 
                                    data-schedule-id="<?= $schedule['schedule_id'] ?>"
                                    data-boat-name="<?= $schedule['boat_name'] ?>"
                                    data-price="<?= $schedule['price_per_trip'] ?>">
                                Book
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Boat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bookingForm">
                    <div class="modal-body">
                        <input type="hidden" id="modalScheduleId">
                        <div class="mb-3">
                            <input type="hidden" id="modalOpenTripId" name="open_trip_id">
                            <label class="form-label">Boat</label>
                            <input type="text" class="form-control" id="modalBoatName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="text" class="form-control" id="modalPrice" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="passengerCount" class="form-label">Number of Passengers</label>
                            <input type="number" class="form-control" id="passengerCount" name="passengers" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Passenger Names</label>
                            <div id="passengerNamesContainer">
                                <input type="text" class="form-control mb-2" name="passenger_names[]" placeholder="Passenger Name 1" required>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addPassengerBtn">Add Passenger</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Book Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Booking Success Modal -->
    <div class="modal fade" id="bookingSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Booking Successful</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <p id="successMessage"></p>
                        <p class="fw-bold">Booking Code: <span id="bookingCode"></span></p>
                    </div>
                    <div class="alert alert-info">
                        <h6>Payment Instructions:</h6>
                        <p>Please transfer the amount of <span id="totalAmount"></span> to the following account:</p>
                        <p>BCA Bank: 1234567890 a.n. Raja Ampat Boat Services</p>
                        <p>Mandiri Bank: 0987654321 a.n. Raja Ampat Boat Services</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    // Handle booking button click
    $('.book-btn').click(function() {
        const scheduleId = $(this).data('schedule-id');
        const boatName = $(this).data('boat-name');
        const price = $(this).data('price');
        
        $('#modalScheduleId').val(scheduleId);
        $('#modalBoatName').val(boatName);
        $('#modalPrice').val('Rp ' + price.toLocaleString('id-ID'));
        $('#passengerCount').val(1);
        $('#passengerNamesContainer').html('<input type="text" class="form-control mb-2" name="passenger_names[]" placeholder="Passenger Name 1" required>');
        
        $('#bookingModal').modal('show');
    });
    
    // Add passenger field
    $('#addPassengerBtn').click(function() {
        const count = $('#passengerNamesContainer input').length + 1;
        $('#passengerNamesContainer').append('<input type="text" class="form-control mb-2" name="passenger_names[]" placeholder="Passenger Name ' + count + '" required>');
    });
    
    // Handle booking form submission
    $('#bookingForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            schedule_id: $('#modalScheduleId').val(),
            passengers: $('#passengerCount').val(),
            passenger_names: $('input[name="passenger_names[]"]').map(function() {
                return $(this).val();
            }).get()
        };
        
        $.ajax({
            url: '<?= base_url('boats/book') ?>',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#bookingModal').modal('hide');
                    $('#successMessage').text(response.message);
                    $('#bookingCode').text(response.data.booking_code);
                    $('#totalAmount').text('Rp ' + response.data.total_price.toLocaleString('id-ID'));
                    $('#bookingSuccessModal').modal('show');
                } else {
                    alert(response.error || 'An error occurred');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response.error || 'An error occurred');
            }
        });
    });
    
    // Handle schedule search form
    $('#scheduleSearchForm').submit(function(e) {
        e.preventDefault();
        
        const fromIsland = $('#searchFromIsland').val();
        const toIsland = $('#searchToIsland').val();
        const date = $('#searchDate').val();
        
        $.ajax({
            url: '<?= base_url('boats/check') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                from_island: fromIsland,
                to_island: toIsland,
                departure_date: date
            },
            success: function(response) {
                if (response.success) {
                    let html = '';
                    response.data.forEach(schedule => {
                        html += `
                            <tr>
                                <td>${schedule.departure_island} - ${schedule.arrival_island}</td>
                                <td>${new Date(schedule.departure_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</td>
                                <td>${schedule.departure_time}</td>
                                <td>${schedule.boat_name}</td>
                                <td>${schedule.capacity} people</td>
                                <td>Rp ${schedule.price_per_trip.toLocaleString('id-ID')}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary book-btn" 
                                            data-schedule-id="${schedule.schedule_id}"
                                            data-boat-name="${schedule.boat_name}"
                                            data-price="${schedule.price_per_trip}">
                                        Book
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#scheduleTableBody').html(html);
                    
                    // Rebind click events for new booking buttons
                    $('.book-btn').click(function() {
                        const scheduleId = $(this).data('schedule-id');
                        const boatName = $(this).data('boat-name');
                        const price = $(this).data('price');
                        
                        $('#modalScheduleId').val(scheduleId);
                        $('#modalBoatName').val(boatName);
                        $('#modalPrice').val('Rp ' + price.toLocaleString('id-ID'));
                        $('#passengerCount').val(1);
                        $('#passengerNamesContainer').html('<input type="text" class="form-control mb-2" name="passenger_names[]" placeholder="Passenger Name 1" required>');
                        
                        $('#bookingModal').modal('show');
                    });
                } else {
                    alert('No schedules available');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response.error || 'An error occurred');
            }
        });
    });
});
</script>