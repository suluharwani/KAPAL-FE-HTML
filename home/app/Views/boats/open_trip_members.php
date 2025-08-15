<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <h2 class="text-center mb-4">Manage Open Trip Members</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Trip Details</h3>
                <span class="badge bg-<?= 
                    $openTrip['status'] == 'upcoming' ? 'info' : 
                    ($openTrip['status'] == 'ongoing' ? 'warning' : 'success') 
                ?>">
                    <?= ucfirst($openTrip['status']) ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Route:</strong> <?= $openTrip['departure_island'] ?> - <?= $openTrip['arrival_island'] ?></p>
                    <p><strong>Date:</strong> <?= date('d M Y', strtotime($openTrip['departure_date'])) ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Boat:</strong> <?= $openTrip['boat_name'] ?> (<?= $openTrip['boat_type'] ?>)</p>
                    <p><strong>Time:</strong> <?= date('H:i', strtotime($openTrip['departure_time'])) ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Capacity:</strong> <?= $openTrip['reserved_seats'] + $openTrip['available_seats'] ?> seats</p>
                    <p><strong>Available:</strong> <?= $openTrip['available_seats'] ?> seats</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h3 class="mb-0">Trip Members</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Booking Code</th>
                            <th>Member Type</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Passengers</th>
                            <th>Total Price</th>
                            <th>Booking Status</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="10" class="text-center">No members yet</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $index => $member): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $member['booking_code'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $member['user_id'] ? 'primary' : 'warning' ?>">
                                            <?= $member['user_id'] ? 'Registered' : 'Guest' ?>
                                        </span>
                                    </td>
                                    <td><?= $member['full_name'] ?></td>
                                    <td><?= $member['phone'] ?? '-' ?></td>
                                    <td><?= $member['passenger_count'] ?></td>
                                    <td>Rp <?= number_format($member['total_price'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $member['booking_status'] == 'pending' ? 'warning' : 
                                            ($member['booking_status'] == 'confirmed' ? 'info' : 
                                            ($member['booking_status'] == 'paid' ? 'success' : 'danger')) 
                                        ?>">
                                            <?= ucfirst($member['booking_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $member['payment_status'] == 'pending' ? 'warning' : 
                                            ($member['payment_status'] == 'partial' ? 'info' : 
                                            ($member['payment_status'] == 'paid' ? 'success' : 'danger')) 
                                        ?>">
                                            <?= ucfirst($member['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-details" 
                                                data-booking-id="<?= $member['booking_id'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($member['booking_status'] != 'completed'): ?>
                                            <button class="btn btn-sm btn-primary edit-booking" 
                                                    data-booking-id="<?= $member['booking_id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('boats/open-trip') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Open Trips
                </a>
                
                <div>
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fas fa-plus me-2"></i> Add Guest Member
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteUserModal">
                        <i class="fas fa-user-plus me-2"></i> Invite User
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Member Details -->
<div class="modal fade" id="memberDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Member Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="memberDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add Guest Member -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Guest Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addGuestMemberForm" action="<?= base_url('boats/add-open-trip-guest') ?>" method="POST">
                <input type="hidden" name="open_trip_id" value="<?= $openTrip['open_trip_id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="guestName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="guestName" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="guestPhone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="guestPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="guestPassengers" class="form-label">Number of Passengers</label>
                        <input type="number" class="form-control" id="guestPassengers" name="passenger_count" 
                               min="1" max="<?= $openTrip['available_seats'] ?>" required>
                    </div>
                    <div id="passengerDetailsContainer">
                        <div class="mb-3 passenger-detail">
                            <label class="form-label">Passenger 1 Name</label>
                            <input type="text" class="form-control" name="passenger_names[]" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addPassengerBtn">
                        <i class="fas fa-plus me-1"></i> Add Passenger
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Invite User -->
<div class="modal fade" id="inviteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invite User to Join</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="inviteUserForm" action="<?= base_url('boats/invite-to-open-trip') ?>" method="POST">
                <input type="hidden" name="open_trip_id" value="<?= $openTrip['open_trip_id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">User Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                        <div class="form-text">Enter registered user email to invite</div>
                    </div>
                    <div class="mb-3">
                        <label for="invitePassengers" class="form-label">Number of Passengers</label>
                        <input type="number" class="form-control" id="invitePassengers" name="passenger_count" 
                               min="1" max="<?= $openTrip['available_seats'] ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // View member details
    $('.view-details').click(function() {
        const bookingId = $(this).data('booking-id');
        
        $.get('<?= base_url('boats/get-booking-details') ?>/' + bookingId, function(response) {
            if (response.success) {
                $('#memberDetailsContent').html(response.html);
                $('#memberDetailsModal').modal('show');
            } else {
                alert(response.error || 'Failed to load details');
            }
        }).fail(function() {
            alert('Error loading details');
        });
    });

    // Add passenger fields
    $('#addPassengerBtn').click(function() {
        const count = $('#passengerDetailsContainer .passenger-detail').length + 1;
        if (count <= $('#guestPassengers').val()) {
            $('#passengerDetailsContainer').append(`
                <div class="mb-3 passenger-detail">
                    <label class="form-label">Passenger ${count} Name</label>
                    <input type="text" class="form-control" name="passenger_names[]" required>
                </div>
            `);
        } else {
            alert('Number of passengers cannot exceed the specified count');
        }
    });

    // Update passenger fields when count changes
    $('#guestPassengers').change(function() {
        const currentCount = $('#passengerDetailsContainer .passenger-detail').length;
        const newCount = $(this).val();
        
        if (newCount > currentCount) {
            for (let i = currentCount + 1; i <= newCount; i++) {
                $('#passengerDetailsContainer').append(`
                    <div class="mb-3 passenger-detail">
                        <label class="form-label">Passenger ${i} Name</label>
                        <input type="text" class="form-control" name="passenger_names[]" required>
                    </div>
                `);
            }
        } else if (newCount < currentCount) {
            for (let i = currentCount; i > newCount; i--) {
                $('#passengerDetailsContainer .passenger-detail').last().remove();
            }
        }
    });

    // Form submission for adding guest member
    $('#addGuestMemberForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Guest member added successfully');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to add guest member');
                }
            },
            error: function() {
                alert('Error submitting form');
            }
        });
    });

    // Form submission for inviting user
    $('#inviteUserForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Invitation sent successfully');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to send invitation');
                }
            },
            error: function() {
                alert('Error submitting form');
            }
        });
    });
});
</script>

<?= $this->endSection() ?>