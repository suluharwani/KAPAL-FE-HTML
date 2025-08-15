<div class="row">
    <div class="col-md-6">
        <h5>Booking Information</h5>
        <table class="table table-bordered">
            <tr>
                <th>Booking Code</th>
                <td><?= $member['booking_code'] ?></td>
            </tr>
            <tr>
                <th>Booking Date</th>
                <td><?= date('d M Y H:i', strtotime($member['created_at'])) ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-<?= 
                        $member['booking_status'] == 'confirmed' ? 'success' : 
                        ($member['booking_status'] == 'pending' ? 'warning' : 'danger') 
                    ?>">
                        <?= ucfirst($member['booking_status']) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Total Price</th>
                <td>Rp <?= number_format($member['total_price'], 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h5>Member Information</h5>
        <table class="table table-bordered">
            <tr>
                <th>Type</th>
                <td><?= $member['user_id'] ? 'Registered User' : 'Guest' ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?= $member['full_name'] ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= $member['email'] ?? '-' ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?= $member['phone'] ?? '-' ?></td>
            </tr>
        </table>
    </div>
</div>

<?php if (!empty($passengers)): ?>
<div class="mt-4">
    <h5>Passenger Details</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Identity Number</th>
                <th>Phone</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($passengers as $index => $passenger): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $passenger['full_name'] ?></td>
                    <td><?= $passenger['identity_number'] ?? '-' ?></td>
                    <td><?= $passenger['phone'] ?? '-' ?></td>
                    <td><?= $passenger['age'] ?? '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>