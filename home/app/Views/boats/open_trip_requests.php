<!-- Main Content -->
<main class="container my-5">
    <h2 class="text-center mb-4">My Open Trip Requests</h2>
    
    <div class="alert alert-info">
        <p>Below is the list of open trip requests you have submitted. Status will be updated after admin verification.</p>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Request ID</th>
                    <th>Route</th>
                    <th>Boat</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="9" class="text-center">You haven't made any open trip requests yet</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td>REQ-<?= str_pad($request['request_id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= $request['departure_island_name'] ?? 'N/A' ?> - <?= $request['arrival_island_name'] ?? 'N/A' ?></td>
                            <td><?= $request['boat_name'] ?? 'N/A' ?> (<?= $request['boat_type'] ?? 'N/A' ?>)</td>
                            <td><?= date('d M Y', strtotime($request['proposed_date'])) ?></td>
                            <td><?= date('H:i', strtotime($request['proposed_time'])) ?></td>
                            <td><?= isset($request['capacity']) ? $request['capacity'] . ' people' : 'N/A' ?></td>
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
                                <span class="badge <?= $badgeClass[$status] ?? 'bg-secondary' ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td><?= $request['notes'] ?? '-' ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <?php if ($status == 'approved' && isset($request['open_trip_id'])): ?>
                                        <a href="<?= base_url('boats/open-trip-members/' . $request['open_trip_id']) ?>" 
                                           class="btn btn-sm btn-info" title="Manage Members" data-bs-toggle="tooltip">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="<?= base_url('boats/open-trip-details/' . $request['open_trip_id']) ?>" 
                                           class="btn btn-sm btn-primary" title="Trip Details" data-bs-toggle="tooltip">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($status == 'pending'): ?>
                                        <button class="btn btn-sm btn-danger cancel-request" 
                                                data-request-id="<?= $request['request_id'] ?>"
                                                title="Cancel Request" data-bs-toggle="tooltip">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($status == 'approved'): ?>
                                        <button class="btn btn-sm btn-info tomemberpage" 
                                                data-request-id="<?= $request['request_id'] ?>"
                                                title="Go to Member Page" data-bs-toggle="tooltip">
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
            <i class="fas fa-arrow-left me-2"></i>Back to Open Trip
        </a>
    </div>
</main>

<!-- Modal for Edit Request -->
<div class="modal fade" id="editRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Open Trip Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRequestForm">
                <div class="modal-body" id="editRequestContent">
                    <!-- Content will be filled via AJAX -->
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
    // Enable tooltip
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Handle cancel request button
    $('.cancel-request').click(function() {
        const requestId = $(this).data('request-id');
        
        if (confirm('Are you sure you want to cancel this request?')) {
            $.post('<?= base_url('boats/cancel-request') ?>', {
                request_id: requestId
            }, function(response) {
                if (response.success) {
                    alert('Request successfully cancelled');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to cancel request');
                }
            });
        }
    });
    
    // Handle complete request button
    $('.tomemberpage').click(function() {
        const requestId = $(this).data('request-id');
        
        // Check if this request already has an open_trip_id
        $.get('<?= base_url('boats/get-open-trip-id') ?>', {
            request_id: requestId
        }, function(response) {
            if (response.success && response.open_trip_id) {
                // Redirect to member page
                window.location.href = '<?= base_url('boats/open-trip-members/') ?>' + response.open_trip_id;
            } else {
                alert('No open trip has been created for this request or an error occurred');
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
                    alert('Request successfully updated');
                    $('#editRequestModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to update request');
                }
            },
            error: function() {
                alert('Error submitting form');
            }
        });
    });
});
</script>