<!-- File: app/Views/boats/boat_open_trip_requests.php -->
<div class="container my-5">
    <h2 class="text-center mb-4">Open Trip Requests for My Boats</h2>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Boat</th>
                    <th>Route</th>
                    <th>Date & Time</th>
                    <th>Requested By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No open trip requests found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td>#<?= $request['request_id'] ?></td>
                            <td><?= $request['boat_name'] ?></td>
                            <td><?= $request['departure_island'] ?> - <?= $request['arrival_island'] ?></td>
                            <td><?= date('d M Y', strtotime($request['proposed_date'])) ?> at <?= date('H:i', strtotime($request['proposed_time'])) ?></td>
                            <td><?= $request['full_name'] ?><br><small><?= $request['email'] ?></small></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $request['status'] == 'pending' ? 'warning' : 
                                    ($request['status'] == 'approved' ? 'success' : 'danger') 
                                ?>">
                                    <?= ucfirst($request['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($request['status'] == 'pending'): ?>
                                    <button class="btn btn-sm btn-success approve-btn" 
                                            data-request-id="<?= $request['request_id'] ?>">
                                        Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-btn" 
                                            data-request-id="<?= $request['request_id'] ?>">
                                        Reject
                                    </button>
                                <?php elseif ($request['status'] == 'approved'): ?>
                                    <span class="text-success">Approved</span>
                                <?php else: ?>
                                    <span class="text-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.approve-btn').click(function() {
        const requestId = $(this).data('request-id');
        
        if (confirm('Are you sure you want to approve this open trip request?')) {
            $.ajax({
                url: '<?= base_url('boats/approve-open-trip-request/') ?>' + requestId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
    
    $('.reject-btn').click(function() {
        const requestId = $(this).data('request-id');
        
        if (confirm('Are you sure you want to reject this open trip request?')) {
            // Implement reject functionality
            alert('Reject functionality to be implemented');
        }
    });
});
</script>