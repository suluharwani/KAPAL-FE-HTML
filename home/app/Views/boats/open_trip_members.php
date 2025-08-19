
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Open Trip Members</h2>
        <a href="<?= base_url('boats/open-trip') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Open Trips
        </a>
    </div>

   <!-- Di open_trip_members.php -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Trip Information</h4>
    </div>
    <div class="card-body">
        <?php if (isset($tripInfo) && !empty($tripInfo)): ?>
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Route:</strong> <?= $tripInfo['departure_island'] ?> - <?= $tripInfo['arrival_island'] ?></p>
                    <p><strong>Date:</strong> <?= date('d M Y', strtotime($tripInfo['departure_date'])) ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Boat:</strong> <?= $tripInfo['boat_name'] ?> (<?= $tripInfo['boat_type'] ?>)</p>
                    <p><strong>Time:</strong> <?= date('H:i', strtotime($tripInfo['departure_time'])) ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Capacity:</strong> <?= $tripInfo['capacity'] ?> seats</p>
                    <p><strong>Available:</strong> <?= $tripInfo['available_seats'] ?> seats</p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Trip information not available</div>
        <?php endif; ?>
    </div>
</div>

    <!-- Members Table -->
    <div class="card">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Trip Members</h4>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="fas fa-plus me-1"></i> Add Member
            </button>
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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No members yet</td>
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
                                    <td>
                                        <span class="badge bg-<?= 
                                            $member['booking_status'] == 'confirmed' ? 'success' : 
                                            ($member['booking_status'] == 'pending' ? 'warning' : 'danger') 
                                        ?>">
                                            <?= ucfirst($member['booking_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-member" 
                                                data-booking-id="<?= $member['booking_id'] ?>"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary edit-member" 
                                                data-booking-id="<?= $member['booking_id'] ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-member" 
                                                data-booking-id="<?= $member['booking_id'] ?>"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMemberForm" action="<?= base_url('boats/add-member') ?>" method="POST">
                <input type="hidden" name="open_trip_id" value="<?= $tripInfo['open_trip_id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member Type</label>
                        <select class="form-select" name="member_type" id="memberType">
                            <option value="registered">Registered User</option>
                            <option value="guest">Guest</option>
                        </select>
                    </div>
                    <div class="mb-3" id="userEmailField">
                        <label class="form-label">User Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter user email">
                    </div>
                    <div class="mb-3" id="userPhoneField">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" placeholder="Enter user phone">
                    </div>
                    <div class="mb-3 d-none" id="guestInfoField">
                        <label class="form-label">Guest Name</label>
                        <input type="text" class="form-control" name="guest_name" placeholder="Enter guest name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Number of Passengers</label>
                        <input type="number" class="form-control" name="passenger_count" 
                               min="1" max="<?= $tripInfo['available_seats'] ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Member Modal -->
<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-hidden="true">
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

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMemberForm" action="<?= base_url('boats/update-member') ?>" method="POST">
                <input type="hidden" name="booking_id" id="editBookingId">
                <div class="modal-body" id="editMemberContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle between user email and guest info fields
    $('#memberType').change(function() {
        if ($(this).val() === 'registered') {
            $('#userEmailField').removeClass('d-none');
            $('#guestInfoField').addClass('d-none');
        } else {
            $('#userEmailField').addClass('d-none');
            $('#guestInfoField').removeClass('d-none');
        }
    });

    // View member details
    $('.view-member').click(function() {
        const bookingId = $(this).data('booking-id');
        
        $.get('<?= base_url('boats/get-member-details') ?>/' + bookingId, function(response) {
            if (response.success) {
                $('#memberDetailsContent').html(response.html);
                $('#viewMemberModal').modal('show');
            } else {
                alert(response.error || 'Failed to load member details');
            }
        });
    });

    // Edit member
    $('.edit-member').click(function() {
        const bookingId = $(this).data('booking-id');
        $('#editBookingId').val(bookingId);
        
        $.get('<?= base_url('boats/get-member-edit') ?>/' + bookingId, function(response) {
            if (response.success) {
                $('#editMemberContent').html(response.html);
                $('#editMemberModal').modal('show');
            } else {
                alert(response.error || 'Failed to load edit form');
            }
        });
    });

    // Delete member
    $('.delete-member').click(function() {
        const bookingId = $(this).data('booking-id');
        
        if (confirm('Are you sure you want to delete this member?')) {
            $.post('<?= base_url('boats/delete-member') ?>', {
                booking_id: bookingId
            }, function(response) {
                if (response.success) {
                    alert('Member deleted successfully');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to delete member');
                }
            });
        }
    });

    // Add member form submission
    $('#addMemberForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Member added successfully');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to add member');
                }
            }
        });
    });

    // Edit member form submission
    $('#editMemberForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Member updated successfully');
                    $('#editMemberModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to update member');
                }
            }
        });
    });
});
</script>
