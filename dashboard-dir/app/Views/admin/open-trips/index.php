<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Open Trips Management</h5>
        <div class="btn-group">
            <a href="<?= base_url('admin/open-trips') ?>" class="btn btn-sm btn-outline-secondary <?= !$status ? 'active' : '' ?>">All</a>
            <a href="<?= base_url('admin/open-trips?status=upcoming') ?>" class="btn btn-sm btn-outline-secondary <?= $status == 'upcoming' ? 'active' : '' ?>">Upcoming</a>
            <a href="<?= base_url('admin/open-trips?status=ongoing') ?>" class="btn btn-sm btn-outline-secondary <?= $status == 'ongoing' ? 'active' : '' ?>">Ongoing</a>
            <a href="<?= base_url('admin/open-trips?status=completed') ?>" class="btn btn-sm btn-outline-secondary <?= $status == 'completed' ? 'active' : '' ?>">Completed</a>
            <a href="<?= base_url('admin/open-trips?status=pending') ?>" class="btn btn-sm btn-outline-warning <?= $status == 'pending' ? 'active' : '' ?>">Pending Requests</a>
        </div>
        <a href="<?= base_url('admin/open-trips/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Boat</th>
                        <th>Route</th>
                        <th>Date & Time</th>
                        <th>Requester</th>
                        <th>Seats</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($openTrips)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                No open trips found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($openTrips as $index => $trip): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= esc($trip['boat_name']) ?></td>
                            <td><?= esc($trip['departure_island']) ?> to <?= esc($trip['arrival_island']) ?></td>
                            <td>
                                <?= date('d M Y', strtotime($status == 'pending' ? $trip['proposed_date'] : $trip['departure_date'])) ?><br>
                                <small class="text-muted"><?= date('H:i', strtotime($status == 'pending' ? $trip['proposed_time'] : $trip['departure_time'])) ?></small>
                            </td>
                            <td><?= esc($trip['requester_name']) ?></td>
                            <td>
                                <?php if ($status == 'pending'): ?>
                                    <!-- Untuk pending requests, gunakan max_passengers jika ada, atau capacity boat -->
                                    <?= isset($trip['max_passengers']) ? $trip['max_passengers'] : (isset($trip['capacity']) ? $trip['capacity'] : 'N/A') ?> seats
                                <?php else: ?>
                                    <!-- Untuk open trips yang sudah dibuat -->
                                    <?= ($trip['reserved_seats'] ?? 0) ?> / <?= ($trip['reserved_seats'] ?? 0) + ($trip['available_seats'] ?? 0) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $trip['status'] == 'upcoming' ? 'primary' : 
                                    ($trip['status'] == 'ongoing' ? 'success' : 
                                    ($trip['status'] == 'completed' ? 'secondary' : 
                                    ($trip['status'] == 'pending' ? 'warning' : 'danger'))) 
                                ?>">
                                    <?= ucfirst($trip['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($status == 'pending'): ?>
                                        <!-- Tombol Approve dengan Modal -->
                                        <button type="button" 
                                                class="btn btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal<?= $trip['request_id'] ?>"
                                                title="Approve Request">
                                            <i class="bi bi-check"></i> Approve
                                        </button>

                                        <!-- Tombol Reject -->
                                        <a href="<?= base_url('admin/request-open-trips/' . $trip['request_id'] . '/status/rejected') ?>" 
                                           class="btn btn-danger" 
                                           title="Reject"
                                           onclick="return confirm('Are you sure you want to reject this request?')">
                                            <i class="bi bi-x"></i> Reject
                                        </a>

                                        <!-- Modal untuk Approve -->
                                        <div class="modal fade" id="approveModal<?= $trip['request_id'] ?>" tabindex="-1" aria-labelledby="approveModalLabel<?= $trip['request_id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title" id="approveModalLabel<?= $trip['request_id'] ?>">
                                                            <i class="bi bi-check-circle"></i> Approve Open Trip Request
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= base_url('admin/open-trips/approve-request/' . $trip['request_id']) ?>" method="post" id="approveForm<?= $trip['request_id'] ?>">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="agreed_price" id="agreed_price_hidden<?= $trip['request_id'] ?>">
                                                        <input type="hidden" name="price_per_person" id="price_per_person_hidden<?= $trip['request_id'] ?>">
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="agreed_price_display<?= $trip['request_id'] ?>" class="form-label fw-semibold">
                                                                        Total Price (IDR) <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text" 
                                                                               class="form-control" 
                                                                               id="agreed_price_display<?= $trip['request_id'] ?>" 
                                                                               name="agreed_price_display" 
                                                                               required 
                                                                               placeholder="Enter total price"
                                                                               oninput="formatPrice('<?= $trip['request_id'] ?>', 'agreed')">
                                                                    </div>
                                                                    <div class="form-text">
                                                                        Total price for the entire trip
                                                                    </div>
                                                                    <small class="text-danger d-none" id="agreed_price_error<?= $trip['request_id'] ?>">
                                                                        Please enter a valid price (minimum Rp 1,000)
                                                                    </small>
                                                                </div>
                                                                
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="price_per_person_display<?= $trip['request_id'] ?>" class="form-label fw-semibold">
                                                                        Price Per Person (IDR) <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text" 
                                                                               class="form-control" 
                                                                               id="price_per_person_display<?= $trip['request_id'] ?>" 
                                                                               name="price_per_person_display" 
                                                                               required 
                                                                               placeholder="Enter price per person"
                                                                               oninput="formatPrice('<?= $trip['request_id'] ?>', 'person')">
                                                                    </div>
                                                                    <div class="form-text">
                                                                        Price per person for individual bookings
                                                                    </div>
                                                                    <small class="text-danger d-none" id="person_price_error<?= $trip['request_id'] ?>">
                                                                        Please enter a valid price (minimum Rp 1,000)
                                                                    </small>
                                                                </div>
                                                            </div>

                                                            <!-- Auto Calculation Info -->
                                                            <div class="alert alert-warning" id="calculation_info<?= $trip['request_id'] ?>">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-calculator me-2"></i>
                                                                    <div>
                                                                        <strong>Calculation Info:</strong>
                                                                        <div id="calculation_details<?= $trip['request_id'] ?>">
                                                                            Enter both prices to see calculation details
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="alert alert-info">
                                                                <h6 class="alert-heading mb-2"><i class="bi bi-info-circle"></i> Request Details</h6>
                                                                <div class="row small">
                                                                    <div class="col-6">
                                                                        <strong>Boat:</strong><br>
                                                                        <?= esc($trip['boat_name']) ?>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <strong>Route:</strong><br>
                                                                        <?= esc($trip['departure_island']) ?> to <?= esc($trip['arrival_island']) ?>
                                                                    </div>
                                                                    <div class="col-6 mt-2">
                                                                        <strong>Date:</strong><br>
                                                                        <?= date('d M Y', strtotime($trip['proposed_date'])) ?>
                                                                    </div>
                                                                    <div class="col-6 mt-2">
                                                                        <strong>Time:</strong><br>
                                                                        <?= date('H:i', strtotime($trip['proposed_time'])) ?>
                                                                    </div>
                                                                    <div class="col-6 mt-2">
                                                                        <strong>Capacity:</strong><br>
                                                                        <?= isset($trip['capacity']) ? $trip['capacity'] . ' persons' : 'N/A' ?>
                                                                    </div>
                                                                    <div class="col-6 mt-2">
                                                                        <strong>Requester:</strong><br>
                                                                        <?= esc($trip['requester_name']) ?>
                                                                    </div>
                                                                    <?php if (!empty($trip['notes'])): ?>
                                                                    <div class="col-12 mt-2">
                                                                        <strong>Notes:</strong><br>
                                                                        <?= esc($trip['notes']) ?>
                                                                    </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-circle"></i> Cancel
                                                            </button>
                                                            <button type="submit" class="btn btn-success" id="submit_btn<?= $trip['request_id'] ?>">
                                                                <i class="bi bi-check-circle"></i> Approve Request
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <?php else: ?>
                                        <!-- Untuk open trips yang sudah dibuat -->
                                        <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id']) ?>" 
                                           class="btn btn-info" 
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <?php if ($trip['status'] == 'upcoming'): ?>
                                            <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id'] . '/status/ongoing') ?>" 
                                               class="btn btn-success" 
                                               title="Start Trip"
                                               onclick="return confirm('Are you sure you want to start this trip?')">
                                                <i class="bi bi-play"></i>
                                            </a>
                                            <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id'] . '/status/canceled') ?>" 
                                               class="btn btn-danger" 
                                               title="Cancel Trip"
                                               onclick="return confirm('Are you sure you want to cancel this trip?')">
                                                <i class="bi bi-x"></i>
                                            </a>
                                        <?php elseif ($trip['status'] == 'ongoing'): ?>
                                            <a href="<?= base_url('admin/open-trips/' . $trip['open_trip_id'] . '/status/completed') ?>" 
                                               class="btn btn-secondary" 
                                               title="Complete Trip"
                                               onclick="return confirm('Are you sure you want to mark this trip as completed?')">
                                                <i class="bi bi-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Function untuk format price input dengan thousand separator
function formatPrice(requestId, type) {
    const displayInput = document.getElementById(`${type === 'agreed' ? 'agreed_price_display' : 'price_per_person_display'}${requestId}`);
    const hiddenInput = document.getElementById(`${type === 'agreed' ? 'agreed_price_hidden' : 'price_per_person_hidden'}${requestId}`);
    const errorElement = document.getElementById(`${type === 'agreed' ? 'agreed_price_error' : 'person_price_error'}${requestId}`);
    const submitBtn = document.getElementById(`submit_btn${requestId}`);
    
    // Hapus semua karakter non-digit
    let value = displayInput.value.replace(/[^\d]/g, '');
    
    // Validasi minimum value
    if (value.length > 0) {
        const numericValue = parseInt(value);
        
        if (numericValue < 1000) {
            // Show error
            errorElement.classList.remove('d-none');
            submitBtn.disabled = true;
            hiddenInput.value = '';
        } else {
            // Hide error
            errorElement.classList.add('d-none');
            submitBtn.disabled = false;
            
            // Format dengan thousand separator
            const formattedValue = numericValue.toLocaleString('id-ID');
            displayInput.value = formattedValue;
            
            // Set nilai numerik ke hidden input
            hiddenInput.value = numericValue;
        }
    } else {
        // Reset jika kosong
        errorElement.classList.add('d-none');
        hiddenInput.value = '';
    }
    
    // Update calculation info
    updateCalculationInfo(requestId);
}

// Function untuk update calculation info
function updateCalculationInfo(requestId) {
    const agreedPrice = parseInt(document.getElementById(`agreed_price_hidden${requestId}`).value) || 0;
    const personPrice = parseInt(document.getElementById(`price_per_person_hidden${requestId}`).value) || 0;
    const capacity = <?= isset($trip['capacity']) ? $trip['capacity'] : 0 ?>;
    const infoElement = document.getElementById(`calculation_details${requestId}`);
    
    if (agreedPrice > 0 && personPrice > 0 && capacity > 0) {
        const estimatedRevenue = personPrice * capacity;
        const profit = estimatedRevenue - agreedPrice;
        const profitPercentage = agreedPrice > 0 ? ((profit / agreedPrice) * 100).toFixed(1) : 0;
        
        let calculationText = `
            <div class="mt-1">
                <small>
                    <strong>Estimated Revenue:</strong> Rp ${estimatedRevenue.toLocaleString('id-ID')}<br>
                    <strong>Potential Profit:</strong> Rp ${profit.toLocaleString('id-ID')} (${profitPercentage}%)<br>
                    <strong>Break-even Point:</strong> ${Math.ceil(agreedPrice / personPrice)} persons
                </small>
            </div>
        `;
        
        infoElement.innerHTML = calculationText;
        
        // Highlight profit/loss
        if (profit > 0) {
            infoElement.classList.add('text-success');
            infoElement.classList.remove('text-danger');
        } else if (profit < 0) {
            infoElement.classList.add('text-danger');
            infoElement.classList.remove('text-success');
        } else {
            infoElement.classList.remove('text-success', 'text-danger');
        }
    } else {
        infoElement.innerHTML = 'Enter both prices to see calculation details';
        infoElement.classList.remove('text-success', 'text-danger');
    }
    
    // Enable/disable submit button based on both prices
    const submitBtn = document.getElementById(`submit_btn${requestId}`);
    const agreedError = document.getElementById(`agreed_price_error${requestId}`);
    const personError = document.getElementById(`person_price_error${requestId}`);
    
    const hasAgreedPrice = agreedPrice >= 1000;
    const hasPersonPrice = personPrice >= 1000;
    const hasAgreedError = !agreedError.classList.contains('d-none');
    const hasPersonError = !personError.classList.contains('d-none');
    
    submitBtn.disabled = !(hasAgreedPrice && hasPersonPrice && !hasAgreedError && !hasPersonError);
}

// Reset form ketika modal ditutup
document.addEventListener('DOMContentLoaded', function() {
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            var forms = this.querySelectorAll('form');
            forms.forEach(function(form) {
                form.reset();
                // Reset hidden inputs juga
                const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => input.value = '');
                
                // Enable submit buttons
                const submitBtns = form.querySelectorAll('button[type="submit"]');
                submitBtns.forEach(btn => btn.disabled = false);
                
                // Hide error messages
                const errorMessages = form.querySelectorAll('.text-danger');
                errorMessages.forEach(error => error.classList.add('d-none'));
                
                // Reset calculation info
                const calculationInfos = form.querySelectorAll('[id^="calculation_details"]');
                calculationInfos.forEach(info => {
                    info.innerHTML = 'Enter both prices to see calculation details';
                    info.classList.remove('text-success', 'text-danger');
                });
            });
        });
    });
});

// Validasi form sebelum submit
document.addEventListener('DOMContentLoaded', function() {
    var forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requestId = form.id.replace('approveForm', '');
            const agreedHidden = document.getElementById(`agreed_price_hidden${requestId}`);
            const personHidden = document.getElementById(`price_per_person_hidden${requestId}`);
            
            const agreedPrice = parseInt(agreedHidden.value) || 0;
            const personPrice = parseInt(personHidden.value) || 0;
            
            if (agreedPrice < 1000 || personPrice < 1000) {
                e.preventDefault();
                
                // Show appropriate errors
                if (agreedPrice < 1000) {
                    const agreedError = document.getElementById(`agreed_price_error${requestId}`);
                    agreedError.classList.remove('d-none');
                }
                
                if (personPrice < 1000) {
                    const personError = document.getElementById(`person_price_error${requestId}`);
                    personError.classList.remove('d-none');
                }
                
                alert('Please enter valid prices for both total price and price per person (minimum Rp 1,000 each)');
            }
        });
    });
});

// Allow only numbers and backspace for price input
document.addEventListener('DOMContentLoaded', function() {
    const priceInputs = document.querySelectorAll('input[name="agreed_price_display"], input[name="price_per_person_display"]');
    priceInputs.forEach(function(input) {
        input.addEventListener('keydown', function(e) {
            // Allow: backspace, delete, tab, escape, enter, numbers
            if ([46, 8, 9, 27, 13, 110].includes(e.keyCode) || 
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) || 
                (e.keyCode === 67 && e.ctrlKey === true) || 
                (e.keyCode === 86 && e.ctrlKey === true) || 
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop the keypress if not
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });
});
</script>

<style>
.modal-header.bg-success {
    background: linear-gradient(135deg, #198754, #157347);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.alert-info {
    border-left: 4px solid #0dcaf0;
}

.alert-warning {
    border-left: 4px solid #ffc107;
    background-color: #fff3cd;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Hover effects for buttons */
.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(25, 135, 84, 0.3);
}

.btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.btn-info:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(13, 202, 240, 0.3);
}

/* Disabled button style */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.text-success {
    color: #198754 !important;
}

.text-danger {
    color: #dc3545 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .row .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>

<?= $this->include('templates/admin_footer') ?>