<!-- Main Content -->
<main class="container my-5">
    <h2 class="text-center mb-4">Open Trip</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Open Trip Schedule</h3>
                </div>
                <div class="card-body">
                    <p>Below is the available open trip schedule. You can join existing open trips or create a new request.</p>
                    <p class="text-warning"><small>* Prices may change according to agreement with admin</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#requestOpenTripModal">
                <i class="fas fa-plus me-2"></i>Request New Open Trip
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
                    <th>Route</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Boat</th>
                    <th>Capacity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($openTrips)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-ship fa-3x mb-3"></i>
                                <p>No open trips available at the moment.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($openTrips as $trip): ?>
                        <tr>
                            <td><?= $trip['departure_island'] ?> - <?= $trip['arrival_island'] ?></td>
                            <td><?= date('d M Y', strtotime($trip['departure_date'])) ?></td>
                            <td><?= date('H:i', strtotime($trip['departure_time'])) ?></td>
                            <td><?= $trip['boat_name'] ?></td>
                            <td>
                                <span class="badge bg-<?= $trip['available_seats'] > 0 ? 'success' : 'danger' ?>">
                                    <?= $trip['available_seats'] ?>/<?= $trip['capacity'] ?> people
                                </span>
                            </td>
                            <td>
                                <?php if (isset($trip['price_per_person']) && !empty($trip['price_per_person'])): ?>
                                    Rp <?= number_format($trip['price_per_person'], 0, ',', '.') ?> / person
                                    <?php if (isset($trip['agreed_price']) && !empty($trip['agreed_price'])): ?>
                                        <br><small class="text-muted">Total: Rp <?= number_format($trip['agreed_price'], 0, ',', '.') ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php 
                                    // Calculate price per person based on boat price and capacity
                                    $pricePerPerson = $trip['capacity'] > 0 ? $trip['price_per_trip'] / $trip['capacity'] : 0;
                                    ?>
                                    Rp <?= number_format($pricePerPerson, 0, ',', '.') ?> / person
                                    <br><small class="text-muted">Total: Rp <?= number_format($trip['price_per_trip'], 0, ',', '.') ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $trip['status'] == 'upcoming' ? 'info' : 
                                    ($trip['status'] == 'ongoing' ? 'warning' : 
                                    ($trip['status'] == 'completed' ? 'success' : 'danger')) 
                                ?>">
                                    <?= ucfirst($trip['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary join-btn" 
                                        data-trip-id="<?= $trip['open_trip_id'] ?>"
                                        data-boat-name="<?= $trip['boat_name'] ?>"
                                        data-price="<?= $trip['price_per_person'] ?? $trip['price_per_trip'] ?>"
                                        data-show-contact="<?= $trip['show_contact_admin'] ?? 0 ?>"
                                        data-available-seats="<?= $trip['available_seats'] ?>"
                                        <?= $trip['available_seats'] <= 0 ? 'disabled' : '' ?>>
                                    <?= $trip['available_seats'] <= 0 ? 'Full' : 'Join' ?>
                                </button>
                                <?php if (session('role') == 'admin'): ?>
                                    <button class="btn btn-sm btn-warning edit-price-btn mt-1"
                                            data-trip-id="<?= $trip['open_trip_id'] ?>"
                                            data-agreed-price="<?= $trip['agreed_price'] ?? '' ?>"
                                            data-commission-rate="<?= $trip['commission_rate'] ?? 0 ?>"
                                            data-show-contact="<?= $trip['show_contact_admin'] ?? 1 ?>"
                                            data-capacity="<?= $trip['capacity'] ?>">
                                        <i class="fas fa-edit"></i> Price
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Request Open Trip Modal -->
    <div class="modal fade" id="requestOpenTripModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request New Open Trip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="openTripRequestForm">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="requestBoat" class="form-label">Boat</label>
                <select class="form-select" id="requestBoat" name="boat_id" required>
                    <option value="" selected disabled>Select Boat</option>
                    <?php foreach ($boats as $boat): ?>
                        <option value="<?= $boat['boat_id'] ?>" data-capacity="<?= $boat['capacity'] ?>">
                            <?= $boat['boat_name'] ?> (<?= $boat['boat_type'] ?> - Capacity: <?= $boat['capacity'] ?> people)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Capacity: <span id="boatCapacityText">-</span> people</div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="requestRoute" class="form-label">Route</label>
                <select class="form-select" id="requestRoute" name="route_id" required>
                    <option value="" selected disabled>Select Route</option>
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
                <label for="proposedDate" class="form-label">Date</label>
                <input type="date" class="form-control" id="proposedDate" name="proposed_date" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="proposedTime" class="form-label">Time</label>
                <input type="time" class="form-control" id="proposedTime" name="proposed_time" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="requestNotes" class="form-label">Notes (Optional)</label>
            <textarea class="form-control" id="requestNotes" name="notes" rows="3" placeholder="Add notes or special requests"></textarea>
        </div>
        
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle"></i> Important Information:</h6>
            <ul class="mb-0">
                <li>Request will be verified by admin first</li>
                <li>Number of passengers follows the selected boat capacity</li>
                <li>Price will be determined through agreement with admin</li>
                <li>You will get commission from every passenger who joins</li>
                <li>Request status will be sent via email</li>
            </ul>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit Request</button>
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
                    <h5 class="modal-title">Request Successful</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <p id="requestSuccessMessage"></p>
                        <p class="fw-bold">Request ID: <span id="requestId"></span></p>
                    </div>
                    <div class="alert alert-info">
                        <p>Your request will be verified by admin. We will send notification via email after the request is approved.</p>
                        <p class="mb-0">Please check the <a href="<?= base_url('boats/my-open-trip-requests') ?>" class="alert-link">My Requests</a> page to see request status.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Price Modal (Admin Only) -->
    <?php if (session('role') == 'admin'): ?>
    <div class="modal fade" id="editPriceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Open Trip Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPriceForm">
                    <input type="hidden" name="open_trip_id" id="editOpenTripId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Agreed Price (Total)</label>
                            <input type="number" class="form-control" name="agreed_price" id="editAgreedPrice" required>
                            <div class="form-text">Total price agreed with customer</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Commission (%)</label>
                            <input type="number" class="form-control" name="commission_rate" id="editCommissionRate" 
                                   min="0" max="100" step="0.01" required>
                            <div class="form-text">Commission percentage for the open trip creator</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Show "Contact Admin"</label>
                            <select class="form-select" name="show_contact_admin" id="editShowContact">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <div class="form-text">If yes, price will be hidden and show contact admin message</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Commission:</strong> <span id="commissionAmount">Rp 0</span><br>
                            <strong>Price per person:</strong> <span id="pricePerPerson">Rp 0</span><br>
                            <strong>Net income:</strong> <span id="netIncome">Rp 0</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
$(document).ready(function() {

    // Handle open trip request form submission
     $('#openTripRequestForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        // Show loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        $.ajax({
            url: '<?= base_url('boats/request-open-trip') ?>',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                
                if (response.success) {
                    $('#requestOpenTripModal').modal('hide');
                    $('#requestSuccessMessage').text(response.message);
                    $('#requestId').text(response.request_id);
                    $('#requestSuccessModal').modal('show');
                    
                    // Reset form
                    $('#openTripRequestForm')[0].reset();
                    $('#boatCapacityText').text('-');
                    
                    // Reload page after 3 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    alert(response.error || 'An error occurred');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessages = '';
                    for (const key in response.errors) {
                        errorMessages += response.errors[key] + '\n';
                    }
                    alert(errorMessages);
                } else {
                    alert('Network error occurred. Please try again.');
                }
            }
        });
    });
    
    // Set max passengers based on boat capacity when boat is selected
    $('#requestBoat').change(function() {
        const selectedOption = $(this).find('option:selected');
        const capacity = selectedOption.data('capacity');
        
        if (capacity) {
            $('#boatCapacityText').text(capacity);
        } else {
            $('#boatCapacityText').text('-');
        }
    });
    
    // Handle edit price button (admin only)
    $('.edit-price-btn').click(function() {
        const tripId = $(this).data('trip-id');
        const agreedPrice = $(this).data('agreed-price') || '';
        const commissionRate = $(this).data('commission-rate') || 0;
        const showContact = $(this).data('show-contact') || 1;
        const capacity = $(this).data('capacity') || 1;
        
        $('#editOpenTripId').val(tripId);
        $('#editAgreedPrice').val(agreedPrice);
        $('#editCommissionRate').val(commissionRate);
        $('#editShowContact').val(showContact);
        
        // Calculate commission and price per person
        calculatePriceDetails(agreedPrice, commissionRate, capacity);
        
        $('#editPriceModal').modal('show');
    });
    
    // Calculate price details when values change
    $('#editAgreedPrice, #editCommissionRate').on('input', function() {
        const agreedPrice = parseFloat($('#editAgreedPrice').val()) || 0;
        const commissionRate = parseFloat($('#editCommissionRate').val()) || 0;
        const capacity = $('.edit-price-btn').data('capacity') || 1;
        
        calculatePriceDetails(agreedPrice, commissionRate, capacity);
    });
    
    function calculatePriceDetails(agreedPrice, commissionRate, capacity) {
        const commissionAmount = (agreedPrice * commissionRate) / 100;
        const pricePerPerson = capacity > 0 ? agreedPrice / capacity : 0;
        const netIncome = agreedPrice - commissionAmount;
        
        $('#commissionAmount').text('Rp ' + commissionAmount.toLocaleString('id-ID'));
        $('#pricePerPerson').text('Rp ' + pricePerPerson.toLocaleString('id-ID'));
        $('#netIncome').text('Rp ' + netIncome.toLocaleString('id-ID'));
    }
    
    // Handle edit price form submission
    $('#editPriceForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '<?= base_url('boats/update-open-trip-price') ?>',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                
                if (response.success) {
                    alert(response.message);
                    $('#editPriceModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.error || 'An error occurred');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessages = '';
                    for (const key in response.errors) {
                        errorMessages += response.errors[key] + '\n';
                    }
                    alert(errorMessages);
                } else {
                    alert('Network error occurred. Please try again.');
                }
            }
        });
    });
    
    // Join open trip button - add show_contact_admin check
    $('.join-btn').click(function() {
        const showContact = $(this).data('show-contact');
        const availableSeats = $(this).data('available-seats');
        
        if (availableSeats <= 0) {
            alert('Sorry, this open trip is full.');
            return;
        }
        
        if (showContact == 1) {
            alert('Please contact admin for price information and registration.\n\nAdmin Contact:\n- Email: admin@rajaampatboats.com\n- WhatsApp: +62 812-3456-7890');
            return;
        }
        
        const tripId = $(this).data('trip-id');
        const boatName = $(this).data('boat-name');
        const price = $(this).data('price');
        
        // Continue with normal booking process
        $('#modalScheduleId').val('');
        $('#modalOpenTripId').val(tripId);
        $('#modalBoatName').val(boatName);
        $('#modalPrice').val('Rp ' + (price || 0).toLocaleString('id-ID'));
        $('#passengerCount').val(1);
        $('#passengerNamesContainer').html('<input type="text" class="form-control mb-2" name="passenger_names[]" placeholder="Passenger Name 1" required>');
        
        $('#bookingModal').modal('show');
    });
    
    // Set minimum date for proposed date
    const today = new Date().toISOString().split('T')[0];
    $('#proposedDate').attr('min', today);
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>

<!-- Booking Modal (Must be in layout or separate include) -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Open Trip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bookingForm">
                <input type="hidden" id="modalScheduleId" name="schedule_id">
                <input type="hidden" id="modalOpenTripId" name="open_trip_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Boat</label>
                        <input type="text" class="form-control" id="modalBoatName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price per person</label>
                        <input type="text" class="form-control" id="modalPrice" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Number of Passengers</label>
                        <input type="number" class="form-control" id="passengerCount" name="passengers" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Passenger Names</label>
                        <div id="passengerNamesContainer">
                            <input type="text" class="form-control mb-2" name="passenger_names[]" placeholder="Passenger Name 1" required>
                        </div>
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