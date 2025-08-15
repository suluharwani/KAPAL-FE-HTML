<div class="row">
    <div class="col-md-6">
        <h4>Booking Information</h4>
        <table class="table table-bordered">
            <tr>
                <th>Booking Code</th>
                <td><?= $booking['booking_code'] ?></td>
            </tr>
            <tr>
                <th>Booking Date</th>
                <td><?= date('d M Y H:i', strtotime($booking['created_at'])) ?></td>
            </tr>
            <tr>
                <th>Booking Status</th>
                <td>
                    <span class="badge bg-<?= 
                        $booking['booking_status'] == 'pending' ? 'warning' : 
                        ($booking['booking_status'] == 'confirmed' ? 'info' : 
                        ($booking['booking_status'] == 'paid' ? 'success' : 'danger')) 
                    ?>">
                        <?= ucfirst($booking['booking_status']) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Payment Status</th>
                <td>
                    <span class="badge bg-<?= 
                        $booking['payment_status'] == 'pending' ? 'warning' : 
                        ($booking['payment_status'] == 'partial' ? 'info' : 
                        ($booking['payment_status'] == 'paid' ? 'success' : 'danger')) 
                    ?>">
                        <?= ucfirst($booking['payment_status']) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Total Price</th>
                <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h4>Member Information</h4>
        <table class="table table-bordered">
            <tr>
                <th>Member Type</th>
                <td>
                    <span class="badge bg-<?= $booking['user_id'] ? 'primary' : 'warning' ?>">
                        <?= $booking['user_id'] ? 'Registered User' : 'Guest' ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?= $booking['full_name'] ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?= $booking['phone'] ?? '-' ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= $booking['email'] ?? '-' ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="mt-4">
    <h4>Passenger Details</h4>
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

<?php if (!empty($payments)): ?>
<div class="mt-4">
    <h4>Payment History</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Method</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $index => $payment): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                    <td><?= date('d M Y H:i', strtotime($payment['payment_date'])) ?></td>
                    <td><?= ucfirst($payment['payment_method']) ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $payment['status'] == 'pending' ? 'warning' : 
                            ($payment['status'] == 'verified' ? 'success' : 'danger') 
                        ?>">
                            <?= ucfirst($payment['status']) ?>
                        </span>
                    </td>
                    <td><?= $payment['notes'] ?? '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>