<?php
// Helper function untuk konversi harga ke number
function to_number($val) {
    if ($val === null || $val === '') return 0;
    if (is_numeric($val)) return (float)$val;

    $clean = preg_replace('/[^\d,.-]/', '', $val);

    if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
    } elseif (strpos($clean, ',') !== false && strpos($clean, '.') === false) {
        $clean = str_replace(',', '.', $clean);
    } else {
        $clean = str_replace(',', '', $clean);
    }

    return (float)$clean;
}

// Inisialisasi variabel
$totalBooked = 0;
$totalPassengers = 0;
$totalRevenue = 0;
$pricePerPerson = $tripInfo['price_per_person'] ?? 0;
$boatCapacity = $tripInfo['capacity'] ?? 0;
$availableSeats = $tripInfo['available_seats'] ?? 0;

// Hitung statistik
foreach ($members as $member) {
    $totalBooked += $member['passenger_count'] ?? 1;
    
    $pricePerPersonRaw = $member['price_per_person'] ?? $member['price'] ?? $member['price_person'] ?? $member['custom_price'] ?? $pricePerPerson;
    $pricePerPersonValue = to_number($pricePerPersonRaw);
    $totalRevenue += $pricePerPersonValue * ($member['passenger_count'] ?? 1);
}

$netRevenue = $totalRevenue - ($totalRevenue * ($tripInfo['commission_rate'] ?? 0) / 100);

// Format currency helper
function format_rp($num) {
    return 'Rp ' . number_format($num, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Open Trip Members</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <style>
        .table-responsive {
            overflow-x: auto;
        }

        #membersTable {
            width: 100% !important;
            table-layout: auto;
            font-size: 0.85rem;
        }

        #membersTable th,
        #membersTable td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
            padding: 0.5rem;
        }

        .avatar-sm {
            width: 28px;
            height: 28px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress {
            height: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #2c3e50;
            z-index: 10;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .col-index { width: 4% !important; }
        .col-booking-code { width: 9% !important; }
        .col-type { width: 7% !important; }
        .col-name { width: 14% !important; }
        .col-contact { width: 14% !important; }
        .col-passengers { width: 7% !important; }
        .col-price { width: 11% !important; }
        .col-total { width: 11% !important; }
        .col-status { width: 8% !important; }
        .col-actions { width: 15% !important; }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        @media (max-width: 992px) {
            #membersTable {
                font-size: 0.8rem;
            }

            #membersTable th,
            #membersTable td {
                padding: 0.3rem;
                max-width: 120px;
            }

            .btn-group .btn {
                margin-bottom: 2px;
            }
        }

        @media (max-width: 768px) {
            .btn-group .btn {
                margin-bottom: 5px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header .btn-group {
                margin-top: 10px;
                width: 100%;
                flex-wrap: wrap;
            }

            .table-responsive {
                overflow-x: scroll;
            }

            #membersTable {
                width: max-content !important;
                min-width: 100%;
            }
        }

        [data-bs-toggle="tooltip"] {
            cursor: help;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Open Trip Members</h2>
            <div>
                <a href="<?= base_url('boats/open-trip') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Open Trips
                </a>
                <?php if (session('role') == 'admin'): ?>
                    <button class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#tripStatisticsModal">
                        <i class="fas fa-chart-bar me-2"></i> View Statistics
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Trip Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Trip Information</h4>
                <span class="badge bg-<?= $tripInfo['status'] == 'upcoming' ? 'success' : ($tripInfo['status'] == 'ongoing' ? 'warning' : 'secondary') ?>">
                    <?= ucfirst($tripInfo['status'] ?? 'unknown') ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (isset($tripInfo) && !empty($tripInfo)): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Route:</strong> <?= $tripInfo['departure_island'] ?? 'N/A' ?> - <?= $tripInfo['arrival_island'] ?? 'N/A' ?></p>
                            <p><strong>Date:</strong> <?= date('d M Y', strtotime($tripInfo['departure_date'] ?? 'now')) ?></p>
                            <p><strong>Time:</strong> <?= date('H:i', strtotime($tripInfo['departure_time'] ?? 'now')) ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Boat:</strong> <?= $tripInfo['boat_name'] ?? 'N/A' ?> (<?= $tripInfo['boat_type'] ?? 'N/A' ?>)</p>
                            <p><strong>Capacity:</strong> <?= $boatCapacity ?> seats</p>
                            <p><strong>Available:</strong>
                                <span class="badge bg-<?= $availableSeats > 0 ? 'success' : 'danger' ?>">
                                    <?= $availableSeats ?> seats
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Agreed Price:</strong> <?= format_rp($tripInfo['agreed_price'] ?? 0) ?></p>
                            <p><strong>Price per Person:</strong> <?= format_rp($pricePerPerson) ?></p>
                            <p><strong>Commission Rate:</strong> <?= $tripInfo['commission_rate'] ?? 0 ?>%</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Total Booked:</strong> <?= $totalBooked ?> seats</p>
                            <p><strong>Total Revenue:</strong> <?= format_rp($totalRevenue) ?></p>
                            <p><strong>Net Revenue:</strong> <?= format_rp($netRevenue) ?></p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Booking Progress</span>
                            <span><?= $totalBooked ?> / <?= $boatCapacity ?> (<?= $boatCapacity > 0 ? round(($totalBooked / $boatCapacity) * 100) : 0 ?>%)</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar 
                                <?= ($totalBooked / $boatCapacity) * 100 >= 80 ? 'bg-success' : (($totalBooked / $boatCapacity) * 100 >= 50 ? 'bg-info' : 'bg-warning') ?>"
                                role="progressbar"
                                style="width: <?= $boatCapacity > 0 ? ($totalBooked / $boatCapacity) * 100 : 0 ?>%;"
                                aria-valuenow="<?= $boatCapacity > 0 ? ($totalBooked / $boatCapacity) * 100 : 0 ?>"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">Trip information not available</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title"><?= $totalBooked ?></h5>
                                <p class="card-text">Total Booked</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title"><?= $availableSeats ?></h5>
                                <p class="card-text">Available Seats</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-chair fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title"><?= format_rp($totalRevenue) ?></h5>
                                <p class="card-text">Total Revenue</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title"><?= format_rp($netRevenue) ?></h5>
                                <p class="card-text">Net Revenue</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <div class="card">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Trip Members (<?= count($members) ?>)</h4>
                <div class="btn-group">
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fas fa-plus me-1"></i> Add Member
                    </button>
                    <button class="btn btn-sm btn-danger" id="deleteAllBtn">
                        <i class="fas fa-trash me-1"></i> Delete All
                    </button>
                    <button class="btn btn-sm btn-primary" id="printAllBtn">
                        <i class="fas fa-print me-1"></i> Print All
                    </button>
                    <button class="btn btn-sm btn-info" id="sendWhatsAppBtn">
                        <i class="fab fa-whatsapp me-1"></i> Send WhatsApp
                    </button>
                    <button class="btn btn-sm btn-outline-light" id="exportBtn">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($members)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No members yet</h5>
                        <p class="text-muted">Start by adding members to this open trip.</p>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="fas fa-plus me-1"></i> Add First Member
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="membersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Booking Code</th>
                                    <th width="8%">Type</th>
                                    <th width="15%">Name</th>
                                    <th width="15%">Contact</th>
                                    <th width="8%">Passengers</th>
                                    <th width="12%">Price/Person</th>
                                    <th width="12%">Total Price</th>
                                    <th width="8%">Status</th>
                                    <th width="10%">Confirmation</th>
                                    <th width="7%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $processedBookings = [];
                                foreach ($members as $index => $member):
                                    if (in_array($member['booking_id'], $processedBookings)) continue;

                                    $memberPricePerPerson = isset($member['custom_price']) && $member['custom_price'] > 0
                                        ? $member['custom_price']
                                        : $pricePerPerson;
                                    $memberTotalPrice = $memberPricePerPerson * ($member['passenger_count'] ?? 1);

                                    $confirmationStatus = $member['passenger_status'] ?? 'pending';
                                    $badgeClass = ($confirmationStatus == 'confirmed') ? 'bg-success' : (($confirmationStatus == 'rejected') ? 'bg-danger' : 'bg-warning');

                                    $allPassengers = array_filter($members, function ($m) use ($member) {
                                        return $m['booking_id'] == $member['booking_id'];
                                    });

                                    $processedBookings[] = $member['booking_id'];
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark"><?= $member['booking_code'] ?? 'N/A' ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $member['user_id'] ? 'primary' : 'warning' ?>">
                                                <?= $member['user_id'] ? 'Registered' : 'Guest' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-<?= $member['user_id'] ? 'primary' : 'warning' ?> text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <?= strtoupper(substr($member['full_name'] ?? '?', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= $member['full_name'] ?? 'N/A' ?></div>
                                                    <small class="text-muted"><?= date('M d, Y', strtotime($member['created_at'] ?? 'now')) ?></small>
                                                </div>
                                            </div>

                                            <?php if (count($allPassengers) > 0): ?>
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-outline-info toggle-passengers"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#passengers-<?= $member['booking_id'] ?>"
                                                        aria-expanded="false"
                                                        aria-controls="passengers-<?= $member['booking_id'] ?>">
                                                        <i class="fas fa-users me-1"></i>
                                                        <?= count($allPassengers) ?> passengers
                                                        <i class="fas fa-chevron-down ms-1"></i>
                                                    </button>

                                                    <div class="collapse mt-2" id="passengers-<?= $member['booking_id'] ?>">
                                                        <div class="card card-body p-2">
                                                            <h6 class="mb-2">Passenger Details:</h6>
                                                            <?php foreach ($allPassengers as $passengerIndex => $passenger): ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 <?= ($passengerIndex < count($allPassengers) - 1) ? 'border-bottom' : '' ?>">
                                                                    <div>
                                                                        <strong><?= $passenger['full_name'] ?? 'N/A' ?></strong>
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            Status:
                                                                            <span class="badge bg-<?= ($passenger['passenger_status'] == 'confirmed') ? 'success' : (($passenger['passenger_status'] == 'rejected') ? 'danger' : 'warning') ?>">
                                                                                <?= ucfirst($passenger['passenger_status'] ?? 'pending') ?>
                                                                            </span>
                                                                        </small>
                                                                    </div>
                                                                    <div class="btn-group">
                                                                        <?php if (($passenger['passenger_status'] ?? 'pending') == 'pending'): ?>
                                                                            <button class="btn btn-sm btn-success confirm-passenger"
                                                                                data-passenger-id="<?= $passenger['passenger_id'] ?? '' ?>"
                                                                                data-status="confirmed"
                                                                                title="Confirm">
                                                                                <i class="fas fa-check"></i>
                                                                            </button>
                                                                            <button class="btn btn-sm btn-danger confirm-passenger"
                                                                                data-passenger-id="<?= $passenger['passenger_id'] ?? '' ?>"
                                                                                data-status="rejected"
                                                                                title="Reject">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div><?= $member['phone'] ?? '-' ?></div>
                                            <small class="text-muted"><?= $member['email'] ?? '-' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info rounded-pill"><?= $member['passenger_count'] ?? 1 ?></span>
                                        </td>
                                        <td>
                                            <span class="text-<?= isset($member['custom_price']) && $member['custom_price'] > 0 ? 'success' : 'primary' ?>">
                                                <?= format_rp($memberPricePerPerson) ?>
                                                <?php if (isset($member['custom_price']) && $member['custom_price'] > 0): ?>
                                                    <br><small class="text-muted">Custom</small>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                <?= format_rp($memberTotalPrice) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $member['booking_status'] == 'confirmed' ? 'success' : ($member['booking_status'] == 'pending' ? 'warning' : ($member['booking_status'] == 'paid' ? 'info' : 'danger')) ?>">
                                                <?= ucfirst($member['booking_status'] ?? 'pending') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst($confirmationStatus) ?>
                                            </span>

                                            <?php if ($confirmationStatus == 'pending'): ?>
                                                <div class="btn-group mt-1">
                                                    <button class="btn btn-sm btn-success confirm-booking"
                                                        data-booking-id="<?= $member['booking_id'] ?>"
                                                        data-status="confirmed"
                                                        title="Confirm All">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger confirm-booking"
                                                        data-booking-id="<?= $member['booking_id'] ?>"
                                                        data-status="rejected"
                                                        title="Reject All">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info view-member"
                                                    data-booking-id="<?= $member['booking_id'] ?>"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-primary edit-member"
                                                    data-booking-id="<?= $member['booking_id'] ?>"
                                                    data-passenger-count="<?= $member['passenger_count'] ?? 1 ?>"
                                                    data-custom-price="<?= $member['custom_price'] ?? 0 ?>"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success print-ticket"
                                                    data-booking-id="<?= $member['booking_id'] ?>"
                                                    title="Print Ticket PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-member"
                                                    data-booking-id="<?= $member['booking_id'] ?>"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="5" class="text-end"><strong>Totals:</strong></td>
                                    <td><strong><?= $totalBooked ?></strong></td>
                                    <td>-</td>
                                    <td><strong><?= format_rp($totalRevenue) ?></strong></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
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
                        <input type="hidden" name="open_trip_id" value="<?= $tripInfo['open_trip_id'] ?? '' ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Member Type *</label>
                                    <select class="form-select" name="member_type" id="memberType" required>
                                        <option value="registered">Registered User</option>
                                        <option value="guest">Guest</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Number of Passengers *</label>
                                    <input type="number" class="form-control" name="passenger_count"
                                        id="passengerCount" min="1" max="<?= $availableSeats ?>"
                                        placeholder="Enter number of passengers" required>
                                    <div class="form-text">
                                        Maximum <?= $availableSeats ?> seats available
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="userEmailField">
                                <label class="form-label">User Email *</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter user email" required>
                                <div class="form-text">Enter the email of a registered user</div>
                            </div>

                            <div class="mb-3 d-none" id="guestInfoField">
                                <label class="form-label">Guest Name *</label>
                                <input type="text" class="form-control" name="guest_name" placeholder="Enter guest name">
                                <div class="form-text">Enter the full name of the guest</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" class="form-control" name="phone" placeholder="Enter phone number" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Custom Price per Person (Optional)</label>
                                <input type="number" class="form-control" name="custom_price"
                                    id="customPrice" placeholder="Enter custom price">
                                <div class="form-text">
                                    Default price: <?= format_rp($pricePerPerson) ?>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Price per Person:</strong>
                                        <span id="pricePerPersonDisplay"><?= format_rp($pricePerPerson) ?></span>
                                    </div>
                                    <div>
                                        <strong>Total Price:</strong>
                                        <span id="totalPriceDisplay"><?= format_rp(0) ?></span>
                                    </div>
                                </div>
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
                        <input type="hidden" name="open_trip_id" value="<?= $tripInfo['open_trip_id'] ?? '' ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Number of Passengers *</label>
                                <input type="number" class="form-control" name="passenger_count"
                                    id="editPassengerCount" min="1" max="<?= $boatCapacity ?>" required>
                                <div class="form-text">Current available seats: <?= $availableSeats ?></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Custom Price per Person</label>
                                <input type="number" class="form-control" name="custom_price"
                                    id="editCustomPrice" placeholder="Enter custom price">
                                <div class="form-text">Default price: <?= format_rp($pricePerPerson) ?></div>
                            </div>

                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Price per Person:</strong>
                                        <span id="editPricePerPersonDisplay"><?= format_rp(0) ?></span>
                                    </div>
                                    <div>
                                        <strong>Total Price:</strong>
                                        <span id="editTotalPriceDisplay"><?= format_rp(0) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
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

        <!-- Statistics Modal -->
        <div class="modal fade" id="tripStatisticsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Trip Statistics</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Booking Statistics</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Capacity:</span>
                                            <strong><?= $boatCapacity ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Booked Seats:</span>
                                            <strong><?= $totalBooked ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Available Seats:</span>
                                            <strong class="text-<?= $availableSeats > 0 ? 'success' : 'danger' ?>"><?= $availableSeats ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Booking Rate:</span>
                                            <strong><?= $boatCapacity > 0 ? round(($totalBooked / $boatCapacity) * 100) : 0 ?>%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Financial Statistics</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Revenue:</span>
                                            <strong><?= format_rp($totalRevenue) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Commission (<?= $tripInfo['commission_rate'] ?? 0 ?>%):</span>
                                            <strong><?= format_rp($totalRevenue * ($tripInfo['commission_rate'] ?? 0) / 100) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Net Revenue:</span>
                                            <strong><?= format_rp($netRevenue) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Average per Person:</span>
                                            <strong><?= format_rp($totalBooked > 0 ? $totalRevenue / $totalBooked : 0) ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Member Distribution</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>By Member Type</h6>
                                        <?php
                                        $registeredCount = 0;
                                        $guestCount = 0;
                                        foreach ($members as $member) {
                                            if ($member['user_id']) {
                                                $registeredCount++;
                                            } else {
                                                $guestCount++;
                                            }
                                        }
                                        ?>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Registered Users:</span>
                                            <strong><?= $registeredCount ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Guest Users:</span>
                                            <strong><?= $guestCount ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>By Booking Status</h6>
                                        <?php
                                        $statusCount = ['confirmed' => 0, 'pending' => 0, 'paid' => 0, 'cancelled' => 0];
                                        foreach ($members as $member) {
                                            $status = strtolower($member['booking_status'] ?? 'pending');
                                            if (isset($statusCount[$status])) {
                                                $statusCount[$status]++;
                                            }
                                        }
                                        ?>
                                        <?php foreach ($statusCount as $status => $count): ?>
                                            <?php if ($count > 0): ?>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><?= ucfirst($status) ?>:</span>
                                                    <strong><?= $count ?></strong>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete All Confirmation Modal -->
        <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete All</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete ALL members from this trip? This action cannot be undone.</p>
                        <div class="alert alert-danger">
                            <strong>Warning:</strong> This will remove all booking records and passenger data for this trip.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteAll">Delete All Members</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Constants
            const pricePerPerson = <?= $pricePerPerson ?>;
            const availableSeats = <?= $availableSeats ?>;
            const capacity = <?= $boatCapacity ?>;

            // Initialize DataTable
            $('#membersTable').DataTable({
                responsive: true,
                ordering: true,
                searching: true,
                pageLength: 10,
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
                language: {
                    search: "Search members:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ members",
                    infoEmpty: "Showing 0 to 0 of 0 members",
                    infoFiltered: "(filtered from _MAX_ total members)"
                }
            });

            // Toggle between user email and guest info fields
            $('#memberType').change(function() {
                if ($(this).val() === 'registered') {
                    $('#userEmailField').removeClass('d-none');
                    $('#guestInfoField').addClass('d-none');
                    $('input[name="email"]').prop('required', true);
                    $('input[name="guest_name"]').prop('required', false);
                } else {
                    $('#userEmailField').addClass('d-none');
                    $('#guestInfoField').removeClass('d-none');
                    $('input[name="email"]').prop('required', false);
                    $('input[name="guest_name"]').prop('required', true);
                }
            });

            // Trigger change event on page load
            $('#memberType').trigger('change');

            // Calculate total price when passenger count or custom price changes
            $('#passengerCount, #customPrice').on('input', function() {
                calculateTotalPrice();
            });

            // Calculate total price for edit form
            $('#editPassengerCount, #editCustomPrice').on('input', function() {
                calculateEditTotalPrice();
            });

            function calculateTotalPrice() {
                const count = parseInt($('#passengerCount').val()) || 0;
                const customPrice = parseFloat($('#customPrice').val()) || pricePerPerson;
                const totalPrice = count * customPrice;

                $('#pricePerPersonDisplay').text('Rp ' + customPrice.toLocaleString('id-ID'));
                $('#totalPriceDisplay').text('Rp ' + totalPrice.toLocaleString('id-ID'));
            }

            function calculateEditTotalPrice() {
                const count = parseInt($('#editPassengerCount').val()) || 0;
                const customPrice = parseFloat($('#editCustomPrice').val()) || pricePerPerson;
                const totalPrice = count * customPrice;

                $('#editPricePerPersonDisplay').text('Rp ' + customPrice.toLocaleString('id-ID'));
                $('#editTotalPriceDisplay').text('Rp ' + totalPrice.toLocaleString('id-ID'));
            }

            // View member details
            $(document).on('click', '.view-member', function() {
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
            $(document).on('click', '.edit-member', function() {
                const bookingId = $(this).data('booking-id');
                const passengerCount = $(this).data('passenger-count');
                const customPrice = $(this).data('custom-price');

                $('#editBookingId').val(bookingId);
                $('#editPassengerCount').val(passengerCount);
                $('#editCustomPrice').val(customPrice);

                calculateEditTotalPrice();
                $('#editMemberModal').modal('show');
            });

            // Delete member
            $(document).on('click', '.delete-member', function() {
                const bookingId = $(this).data('booking-id');

                if (confirm('Are you sure you want to delete this member? This action cannot be undone.')) {
                    $.post('<?= base_url('boats/delete-member') ?>', {
                        booking_id: bookingId,
                        open_trip_id: '<?= $tripInfo['open_trip_id'] ?? '' ?>',
                        _token: '<?= csrf_hash() ?>'
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

                const form = $(this);
                const formData = form.serialize();
                const submitBtn = form.find('button[type="submit"]');
                const originalBtnText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding Member...');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    },
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalBtnText);

                        if (response.success) {
                            const successHtml = `
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-check-circle"></i> Member Added Successfully!</h5>
                                    <p><strong>Booking Code:</strong> ${response.data.booking_code}</p>
                                    <p><strong>Passengers:</strong> ${response.data.passenger_count}</p>
                                    <p><strong>Total Price:</strong> IDR ${response.data.total_price.toLocaleString('id-ID')}</p>
                                    <p><strong>Available Seats Left:</strong> ${response.data.available_seats}</p>
                                </div>
                            `;

                            $('#addMemberModal .modal-body').prepend(successHtml);

                            setTimeout(function() {
                                $('#addMemberModal').modal('hide');
                                form[0].reset();
                                $('.alert-success').remove();
                                location.reload();
                            }, 3000);
                        } else {
                            if (response.errors) {
                                $.each(response.errors, function(field, error) {
                                    const input = form.find('[name="' + field + '"]');
                                    input.addClass('is-invalid');
                                    input.after('<div class="invalid-feedback">' + error + '</div>');
                                });
                            } else {
                                alert('Error: ' + (response.error || response.message || 'Failed to add member'));
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        submitBtn.prop('disabled', false).html(originalBtnText);

                        if (xhr.status === 403) {
                            alert('Access forbidden. Please ensure you are making an AJAX request.');
                        } else {
                            alert('Network error: ' + error + '. Please check your connection and try again.');
                        }
                    }
                });
            });

            // Edit member form submission
            $('#editMemberForm').submit(function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serialize();
                const submitBtn = form.find('button[type="submit"]');
                const originalBtnText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalBtnText);

                        if (response.success) {
                            alert('Member updated successfully');
                            $('#editMemberModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.error || 'Failed to update member');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Network error: ' + error + '. Please check your connection and try again.');
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // Delete all members
            $(document).on('click', '#deleteAllBtn', function() {
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteAllModal'));
                deleteModal.show();
            });

            $(document).on('click', '#confirmDeleteAll', function() {
                const openTripId = '<?= $tripInfo['open_trip_id'] ?? '' ?>';

                $.post('<?= base_url('boats/delete-all-members') ?>', {
                    open_trip_id: openTripId,
                    _token: '<?= csrf_hash() ?>'
                }, function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.error || 'Failed to delete all members');
                    }
                    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteAllModal'));
                    deleteModal.hide();
                }).fail(function(xhr, status, error) {
                    alert('Network error: ' + error);
                });
            });

            // Export functionality
            $('#exportBtn').click(function() {
                const openTripId = '<?= $tripInfo['open_trip_id'] ?? '' ?>';
                window.location.href = '<?= base_url('boats/export-members/') ?>' + openTripId;
            });

            // Print All functionality
            $('#printAllBtn').click(function() {
                const openTripId = '<?= $tripInfo['open_trip_id'] ?? '' ?>';
                window.open('<?= base_url('boats/print-tickets') ?>?open_trip_id=' + openTripId, '_blank');
            });

            // Print individual ticket
            $(document).on('click', '.print-ticket', function() {
                const bookingId = $(this).data('booking-id');
                const originalText = $(this).html();
                $(this).html('<i class="fas fa-spinner fa-spin"></i>');

                window.open('<?= base_url('boats/download-tickets-pdf') ?>?booking_ids=' + bookingId, '_blank');

                setTimeout(() => {
                    $(this).html(originalText);
                }, 2000);
            });

            // Toggle passenger details
            $(document).on('click', '.toggle-passengers', function() {
                const icon = $(this).find('.fa-chevron-down');
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });

            // Confirm individual passenger
            $(document).on('click', '.confirm-passenger', function() {
                const passengerId = $(this).data('passenger-id');
                const status = $(this).data('status');
                const button = $(this);

                if (confirm(`Are you sure you want to ${status} this passenger?`)) {
                    const originalHtml = button.html();
                    button.html('<i class="fas fa-spinner fa-spin"></i>');

                    $.post('<?= base_url('boats/confirm-passenger') ?>', {
                        passenger_id: passengerId,
                        status: status,
                        _token: '<?= csrf_hash() ?>'
                    }, function(response) {
                        if (response.success) {
                            alert(`Passenger ${status} successfully`);
                            location.reload();
                        } else {
                            alert(response.error || 'Failed to update passenger status');
                            button.html(originalHtml);
                        }
                    }).fail(function() {
                        alert('Network error. Please try again.');
                        button.html(originalHtml);
                    });
                }
            });

            // Confirm all passengers in booking
            $(document).on('click', '.confirm-booking', function() {
                const bookingId = $(this).data('booking-id');
                const status = $(this).data('status');
                const button = $(this);

                if (confirm(`Are you sure you want to ${status} ALL passengers in this booking?`)) {
                    const originalHtml = button.html();
                    button.html('<i class="fas fa-spinner fa-spin"></i>');

                    $.post('<?= base_url('boats/confirm-booking-passengers') ?>', {
                        booking_id: bookingId,
                        status: status,
                        _token: '<?= csrf_hash() ?>'
                    }, function(response) {
                        if (response.success) {
                            alert(`All passengers ${status} successfully`);
                            location.reload();
                        } else {
                            alert(response.error || 'Failed to update passenger status');
                            button.html(originalHtml);
                        }
                    }).fail(function() {
                        alert('Network error. Please try again.');
                        button.html(originalHtml);
                    });
                }
            });

            // Reset form when modal is closed
            $('#addMemberModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.alert-success').remove();
                $('#pricePerPersonDisplay').text('Rp ' + pricePerPerson.toLocaleString('id-ID'));
                $('#totalPriceDisplay').text('Rp 0');
            });

            $('#editMemberModal').on('hidden.bs.modal', function() {
                $('#editPricePerPersonDisplay').text('Rp 0');
                $('#editTotalPriceDisplay').text('Rp 0');
            });
        });
    </script>
</body>
</html>