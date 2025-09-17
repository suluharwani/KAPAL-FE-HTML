<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Kapal Raja Ampat - Open Trip & Regular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #3a6ea5;
            --secondary-color: #6a3093;
            --open-trip-color: #d35400;
            --regular-trip-color: #2980b9;
            --featured-color: #9b59b6;
            --light-bg: #f8f9fa;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .search-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            color: #333;
        }
        
        .search-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .nav-pills .nav-link {
            border-radius: 25px;
            padding: 10px 20px;
            margin: 0 5px;
            font-weight: 600;
            color: #555;
            background-color: #f1f1f1;
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .nav-pills .nav-link#open-trip-tab.active {
            background: linear-gradient(45deg, var(--open-trip-color), #e67e22);
            color: white;
        }
        
        .trip-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .open-trip-badge {
            background: linear-gradient(45deg, var(--open-trip-color), #e67e22);
        }
        
        .regular-trip-badge {
            background: linear-gradient(45deg, var(--regular-trip-color), #3498db);
        }
        
        .feature-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
            background: linear-gradient(45deg, var(--featured-color), #8e44ad);
        }
        
        .price-tag {
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        .regular-price {
            color: var(--regular-trip-color);
        }
        
        .open-trip-price {
            color: var(--open-trip-color);
        }
        
        .price-details {
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        
        .price-per-person {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .price-per-trip {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .boat-img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        
        .result-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        }
        
        .open-trip-card {
            border-left: 4px solid var(--open-trip-color);
        }
        
        .regular-trip-card {
            border-left: 4px solid var(--regular-trip-color);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
        }
        
        .btn-danger {
            background: linear-gradient(45deg, var(--open-trip-color), #e67e22);
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(45deg, var(--success-color), #2ecc71);
            border: none;
        }
        
        .btn-warning {
            background: linear-gradient(45deg, var(--warning-color), #f1c40f);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
        }
        
        .btn-danger:hover {
            background: linear-gradient(45deg, #e67e22, var(--open-trip-color));
        }
        
        .btn-success:hover {
            background: linear-gradient(45deg, #2ecc71, var(--success-color));
        }
        
        .card-title {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .card-text {
            color: #34495e;
        }
        
        .starting-from {
            font-size: 0.8rem;
            color: #7f8c8d;
            font-weight: normal;
        }
        
        .seat-info {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .progress {
            height: 8px;
            margin-top: 5px;
        }
        
        .seat-availability {
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        
        .user-status-badge {
            position: absolute;
            top: 40px;
            right: 10px;
            z-index: 1;
            font-size: 0.7rem;
        }
        
        .confirmed-badge {
            background: linear-gradient(45deg, var(--success-color), #2ecc71);
        }
        
        .pending-badge {
            background: linear-gradient(45deg, var(--warning-color), #f1c40f);
        }
        
        .login-prompt {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .price-comparison {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid var(--success-color);
        }
        
        .price-breakdown {
            font-size: 0.8rem;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-ship me-2"></i>Raja Ampat Boat Services
            </a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i><?= $_SESSION['user_name'] ?? 'User' ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-user-circle me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="/my-bookings"><i class="fas fa-ticket-alt me-2"></i>Pemesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="/auth/login"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="search-card card">
                        <div class="card-body p-4">
                            <h2 class="card-title text-center mb-4 text-dark">Cari Jadwal Kapal</h2>
                            
                            <!-- Trip Type Tabs -->
                            <ul class="nav nav-pills justify-content-center mb-4" id="tripTypeTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="regular-tab" data-bs-toggle="pill" data-bs-target="#regular" type="button" role="tab">
                                        <i class="fas fa-calendar-day me-2"></i>Regular Trip
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="open-trip-tab" data-bs-toggle="pill" data-bs-target="#open-trip" type="button" role="tab">
                                        <i class="fas fa-users me-2"></i>Open Trip
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="tripTypeTabContent">
                                <!-- Regular Trip Form -->
                                <div class="tab-pane fade" id="regular" role="tabpanel">
                                    <form id="regularSearchForm">
                                        <input type="hidden" name="trip_type" value="regular">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="regularRouteSelect" class="form-label">Rute Regular</label>
                                                <select class="form-select" id="regularRouteSelect" name="route">
                                                    <option value="" selected>Semua Rute Regular</option>
                                                    <?php foreach ($regularRoutes as $route): ?>
                                                        <option value="<?= $route['route_id'] ?>">
                                                            <?= $route['departure_island'] ?> - <?= $route['arrival_island'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="regularDateInput" class="form-label">Tanggal Keberangkatan</label>
                                                <input type="date" class="form-control" id="regularDateInput" name="date">
                                            </div>
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-search me-2"></i>Cari Jadwal Regular
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Open Trip Form -->
                                <div class="tab-pane fade show active" id="open-trip" role="tabpanel">
                                    <form id="openTripSearchForm">
                                        <input type="hidden" name="trip_type" value="open_trip">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="openTripRouteSelect" class="form-label">Rute Open Trip</label>
                                                <select class="form-select" id="openTripRouteSelect" name="route">
                                                    <option value="" selected>Semua Rute Open Trip</option>
                                                    <?php foreach ($openTripRoutes as $route): ?>
                                                        <option value="<?= $route['route_id'] ?>">
                                                            <?= $route['departure_island'] ?> - <?= $route['arrival_island'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="openTripDateInput" class="form-label">Tanggal Keberangkatan</label>
                                                <input type="date" class="form-control" id="openTripDateInput" name="date">
                                            </div>
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fas fa-users me-2"></i>Cari Open Trip
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="results-section mb-5">
        <div class="container">
            <h2 class="mb-4">Hasil Pencarian</h2>
            
            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Sedang mencari jadwal...</p>
            </div>
            
            <div id="resultsContainer" class="row">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Silakan pilih jenis trip, rute, dan/atau tanggal untuk melihat jadwal kapal</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login Required</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda harus login terlebih dahulu untuk melakukan pemesanan.</p>
                    <div class="text-center">
                        <a href="/auth/login" class="btn btn-primary me-2">Login</a>
                        <a href="/auth/register" class="btn btn-outline-primary">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pesan Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="bookingModalContent">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Seat Modal -->
    <div class="modal fade" id="requestSeatModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Kursi Open Trip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="requestSeatForm">
                        <input type="hidden" id="requestScheduleId" name="schedule_id">
                        <div class="mb-3">
                            <label for="passengerName" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="passengerName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="passengerIdentity" class="form-label">Nomor Identitas (KTP/Passport)</label>
                            <input type="text" class="form-control" id="passengerIdentity" name="identity">
                        </div>
                        <div class="mb-3">
                            <label for="passengerPhone" class="form-label">Nomor Telepon *</label>
                            <input type="tel" class="form-control" id="passengerPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="passengerAge" class="form-label">Usia</label>
                            <input type="number" class="form-control" id="passengerAge" name="age" min="1" max="100">
                        </div>
                    </form>
                    <div id="seatRequestResult" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="submitSeatRequest">Request Kursi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simpan adminUrl dalam variabel JavaScript
    const adminUrl = '<?= $adminUrl ?>';
    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    const userId = <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>;
    
    // Variabel untuk menyimpan schedule_id yang sedang diproses
    let currentScheduleId = null;
    
    $(document).ready(function() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#regularDateInput, #openTripDateInput').attr('min', today);
        
        // Load Open Trip data secara default
        performSearch('open_trip');
        
        // Form submission handlers
        $('#regularSearchForm').on('submit', function(e) {
            e.preventDefault();
            performSearch('regular');
        });
        
        $('#openTripSearchForm').on('submit', function(e) {
            e.preventDefault();
            performSearch('open_trip');
        });
        
        // Change event handlers
        $('#regularRouteSelect, #regularDateInput').on('change', function() {
            performSearch('regular');
        });
        
        $('#openTripRouteSelect, #openTripDateInput').on('change', function() {
            performSearch('open_trip');
        });
        
        // Tab change handler
        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            const tabId = $(e.target).attr('data-bs-target');
            if (tabId === '#open-trip') {
                performSearch('open_trip');
            } else {
                performSearch('regular');
            }
        });
        
        // Setup open trip request functionality
        setupOpenTripRequest();
        
        // Event delegation untuk tombol yang dibuat dinamis
        $(document).on('click', '.book-btn', function() {
            const scheduleId = $(this).data('schedule-id');
            const tripType = $(this).data('trip-type');
            
            if (!isLoggedIn) {
                // Show login modal if not logged in
                $('#loginModal').modal('show');
            } else {
                // Load booking form via AJAX
                $.ajax({
                    url: `/booking/form/${scheduleId}?trip_type=${tripType}`,
                    method: 'GET',
                    success: function(html) {
                        $('#bookingModalContent').html(html);
                        $('#bookingModal').modal('show');
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memuat form pemesanan');
                    }
                });
            }
        });
        
        // Event delegation untuk request seat buttons
        $(document).on('click', '.request-seat-btn', function() {
            const scheduleId = $(this).data('schedule-id');
            
            // Simpan schedule_id di variabel global dan di form
            currentScheduleId = scheduleId;
            $('#requestScheduleId').val(scheduleId);
            
            // Reset form
            $('#requestSeatForm')[0].reset();
            $('#seatRequestResult').html('');
            
            // Tampilkan modal
            $('#requestSeatModal').modal('show');
        });
        
        // Event listener untuk modal show event
        $('#requestSeatModal').on('show.bs.modal', function() {
            // Pastikan schedule ID sudah diset sebelum modal ditampilkan
            if (currentScheduleId) {
                $('#requestScheduleId').val(currentScheduleId);
            }
        });
    });
    
    function performSearch(tripType) {
        let routeId, date;
        
        if (tripType === 'open_trip') {
            routeId = $('#openTripRouteSelect').val();
            date = $('#openTripDateInput').val();
        } else {
            routeId = $('#regularRouteSelect').val();
            date = $('#regularDateInput').val();
        }
        
        // Show loading spinner
        $('#loadingSpinner').show();
        $('#resultsContainer').html('');
        
        // Fetch data dari server
        $.ajax({
            url: `/searchSchedules?route=${routeId}&date=${date}&trip_type=${tripType}`,
            method: 'GET',
            dataType: 'json',
            success: function(schedules) {
                displayResults(schedules, tripType);
                $('#loadingSpinner').hide();
            },
            error: function(error) {
                console.error('Error:', error);
                $('#loadingSpinner').hide();
                $('#resultsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p class="text-danger">Terjadi kesalahan saat memuat data</p>
                        <small class="text-muted">Silakan coba lagi nanti</small>
                    </div>
                `);
            }
        });
    }
    
function displayResults(schedules, tripType) {
    const resultsContainer = $('#resultsContainer');
    
    if (!schedules || schedules.length === 0) {
        resultsContainer.html(`
            <div class="col-12 text-center py-5">
                <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada jadwal ${tripType === 'open_trip' ? 'Open Trip' : 'Regular'} yang ditemukan</p>
                <small class="text-muted">Coba ubah kriteria pencarian Anda</small>
            </div>
        `);
        return;
    }
    
    let html = '';
    
    schedules.forEach(schedule => {
        const isOpenTrip = tripType === 'open_trip';
        const formattedDate = new Date(schedule.departure_date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        
        const formattedTime = new Date('2000-01-01T' + schedule.departure_time).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Menggunakan URL gambar yang benar dengan fallback
        const imageUrl = schedule.image_url 
            ? adminUrl + schedule.image_url 
            : 'https://images.unsplash.com/photo-1530533718754-001d2668365a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
        
        // Format harga untuk Regular Trip dan Open Trip
        let pricePerTrip, pricePerPerson, priceText, priceDetails;
        
        if (isOpenTrip) {
            // Untuk Open Trip: Harga per orang dan per trip
            pricePerTrip = schedule.agreed_price || schedule.price || 0;
            pricePerPerson = schedule.price_per_person || Math.ceil(pricePerTrip / (schedule.capacity || 1));
            
            priceText = `Rp ${new Intl.NumberFormat('id-ID').format(pricePerPerson)}`;
            priceDetails = `
                <div class="price-details mt-2">
                    <div class="price-per-person">
                        <i class="fas fa-user me-1"></i>Per Orang: Rp ${new Intl.NumberFormat('id-ID').format(pricePerPerson)}
                    </div>
                    <div class="price-per-trip">
                        <i class="fas fa-ship me-1"></i>Per Trip: Rp ${new Intl.NumberFormat('id-ID').format(pricePerTrip)}
                    </div>
                    <div class="price-breakdown">
                        <small>Harga trip dibagi untuk ${schedule.capacity || 1} penumpang</small>
                    </div>
                </div>
            `;
        } else {
            // Untuk Regular Trip: Harga per trip
            pricePerTrip = schedule.price_per_trip || schedule.price || 0;
            pricePerPerson = Math.ceil(pricePerTrip / (schedule.capacity || 1));
            
            priceText = `Rp ${new Intl.NumberFormat('id-ID').format(pricePerTrip)}`;
            priceDetails = `
                <div class="price-details mt-2">
                    <div class="price-per-trip">
                        <i class="fas fa-ship me-1"></i>Per Trip: Rp ${new Intl.NumberFormat('id-ID').format(pricePerTrip)}
                    </div>
                    <div class="price-per-person">
                        <i class="fas fa-user me-1"></i>Per Orang: Rp ${new Intl.NumberFormat('id-ID').format(pricePerPerson)} (estimasi)
                    </div>
                    <div class="price-breakdown">
                        <small>Harga untuk seluruh kapal (max ${schedule.capacity || 1} penumpang)</small>
                    </div>
                </div>
            `;
        }
        
        // Hitung persentase kursi berdasarkan struktur database
        const totalSeats = schedule.total_seats || schedule.capacity || 0;
        const availableSeats = schedule.available_seats || 0;
        const bookedSeats = totalSeats - availableSeats;
        
        // Gunakan confirmed_seats dari database, default 0 jika tidak ada
        const confirmedSeats = schedule.confirmed_seats || 0;
        const pendingSeats = bookedSeats - confirmedSeats;
        
        const percentageConfirmed = totalSeats > 0 ? Math.round((confirmedSeats / totalSeats) * 100) : 0;
        const percentagePending = totalSeats > 0 ? Math.round((pendingSeats / totalSeats) * 100) : 0;
        const percentageAvailable = totalSeats > 0 ? Math.round((availableSeats / totalSeats) * 100) : 0;
        
        // Progress bar dengan multiple segments
        const progressBarHTML = `
            <div class="progress" style="height: 12px;">
                <div class="progress-bar bg-success" 
                     role="progressbar" 
                     style="width: ${percentageConfirmed}%" 
                     title="Terkonfirmasi: ${confirmedSeats} kursi">
                </div>
                <div class="progress-bar bg-warning" 
                     role="progressbar" 
                     style="width: ${percentagePending}%" 
                     title="Menunggu: ${pendingSeats} kursi">
                </div>
                <div class="progress-bar bg-light" 
                     role="progressbar" 
                     style="width: ${percentageAvailable}%" 
                     title="Tersedia: ${availableSeats} kursi">
                </div>
            </div>
        `;
        
        // Cek apakah user sudah memesan di open trip ini
        const userHasRequested = schedule.user_booking_status ? true : false;
        const userBookingStatus = schedule.user_booking_status || '';
        
        // Tombol aksi berbeda untuk open trip dan regular trip
        let actionButton;
        if (isOpenTrip) {
            if (userHasRequested) {
                // User sudah request kursi di open trip ini
                if (userBookingStatus === 'confirmed') {
                    actionButton = `
                        <span class="user-status-badge badge confirmed-badge">
                            <i class="fas fa-check-circle me-1"></i>TERKONFIRMASI
                        </span>
                        <button class="btn btn-sm btn-success" disabled>
                            <i class="fas fa-check me-1"></i>Sudah Terkonfirmasi
                        </button>
                    `;
                } else {
                    actionButton = `
                        <span class="user-status-badge badge pending-badge">
                            <i class="fas fa-clock me-1"></i>MENUNGGU
                        </span>
                        <button class="btn btn-sm btn-warning" disabled>
                            <i class="fas fa-clock me-1"></i>Menunggu Konfirmasi
                        </button>
                    `;
                }
            } else if (availableSeats > 0) {
                // Untuk open trip, tampilkan tombol request seat jika masih ada kursi
                actionButton = `
                    <button class="btn btn-sm btn-danger request-seat-btn" 
                            data-schedule-id="${schedule.schedule_id}">
                        <i class="fas fa-user-plus me-1"></i>Request Seat
                    </button>
                `;
            } else {
                // Tidak ada kursi tersedia
                actionButton = `
                    <button class="btn btn-sm btn-secondary" disabled>
                        <i class="fas fa-times me-1"></i>Kuota Penuh
                    </button>
                `;
            }
        } else {
            // Untuk regular trip, tampilkan tombol pesan seperti sebelumnya
            actionButton = `
                <button class="btn btn-sm btn-primary book-btn" 
                        data-schedule-id="${schedule.schedule_id}" 
                        data-trip-type="${tripType}">
                    <i class="fas fa-ticket-alt me-1"></i>Pesan
                </button>
            `;
        }
        
        // Tampilkan status jadwal
        const scheduleStatus = availableSeats <= 0 ? 
            `<span class="badge bg-danger ms-2">PENUH</span>` : 
            `<span class="badge bg-success ms-2">TERSEDIA</span>`;
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 result-card ${isOpenTrip ? 'open-trip-card' : 'regular-trip-card'}">
                    <span class="trip-badge badge ${isOpenTrip ? 'open-trip-badge' : 'regular-trip-badge'}">
                        <i class="fas ${isOpenTrip ? 'fa-users' : 'fa-calendar-day'} me-1"></i>
                        ${isOpenTrip ? 'OPEN TRIP' : 'REGULAR'}
                    </span>
                    
                    ${schedule.is_featured ? `
                        <span class="feature-badge badge">
                            <i class="fas fa-star me-1"></i>FEATURED
                        </span>
                    ` : ''}
                    
                    <img src="${imageUrl}" class="card-img-top boat-img" alt="${schedule.boat_name || 'Kapal'}">
                    
                    <div class="card-body">
                        <h5 class="card-title">${schedule.boat_name || 'Nama Kapal Tidak Tersedia'} ${scheduleStatus}</h5>
                        <p class="card-text mb-2">
                            <i class="fas fa-route me-2 text-primary"></i>
                            ${schedule.departure_island || 'N/A'} → ${schedule.arrival_island || 'N/A'}
                        </p>
                        <p class="card-text mb-2">
                            <i class="fas fa-calendar me-2 text-primary"></i>
                            ${formattedDate}
                        </p>
                        <p class="card-text mb-2">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            ${formattedTime}
                        </p>
                        
                        <div class="seat-info">
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <span class="seat-availability">
                                        <i class="fas fa-chair me-1"></i>
                                        ${availableSeats} dari ${totalSeats} kursi tersedia
                                        <br>
                                        <small>
                                            <span class="text-success">✓ ${confirmedSeats} terkonfirmasi</span> • 
                                            <span class="text-warning">⏳ ${pendingSeats} menunggu</span>
                                        </small>
                                    </span>
                                </div>
                                ${progressBarHTML}
                            </div>
                        </div>
                        
                        <div class="price-comparison">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag ${isOpenTrip ? 'open-trip-price' : 'regular-price'}">
                                    ${priceText}
                                    <small class="d-block starting-from">${isOpenTrip ? 'per orang' : 'per trip'}</small>
                                </span>
                                ${actionButton}
                            </div>
                            ${priceDetails}
                        </div>
                        
                        ${!isLoggedIn && isOpenTrip ? `
                            <div class="login-prompt mt-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Login untuk request kursi open trip
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    resultsContainer.html(html);
}
    
    function setupOpenTripRequest() {
        // Event listener untuk submit request
        $('#submitSeatRequest').on('click', function() {
            // Gunakan currentScheduleId yang sudah disimpan
            const scheduleId = currentScheduleId;
            
            if (!scheduleId) {
                $('#seatRequestResult').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan: Schedule ID tidak ditemukan
                    </div>
                `);
                return;
            }
            
            // Validasi form
            const name = $('#passengerName').val();
            const phone = $('#passengerPhone').val();
            
            if (!name || !phone) {
                $('#seatRequestResult').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Nama dan nomor telepon wajib diisi
                    </div>
                `);
                return;
            }
            
            // Ambil data dari form
            const formData = {
                schedule_id: scheduleId,
                name: name,
                identity: $('#passengerIdentity').val(),
                phone: phone,
                age: $('#passengerAge').val()
            };
            
            // Tampilkan loading state
            const submitBtn = $('#submitSeatRequest');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Memproses...');
            submitBtn.prop('disabled', true);
            
            $.ajax({
                url: '/home/requestOpenTripSeat',
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    const resultDiv = $('#seatRequestResult');
                    if (data.success) {
                        resultDiv.html(`
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>${data.message}
                            </div>
                        `);
                        // Refresh hasil pencarian setelah 2 detik
                        setTimeout(() => {
                            if ($('#open-trip-tab').hasClass('active')) {
                                performSearch('open_trip');
                            }
                            $('#requestSeatModal').modal('hide');
                        }, 2000);
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    } else {
                        resultDiv.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                            </div>
                        `);
                        // Kembalikan state tombol
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    let errorMessage = 'Terjadi kesalahan saat mengirim request';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    $('#seatRequestResult').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>${errorMessage}
                        </div>
                    `);
                    // Kembalikan state tombol
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            });
        });
        
        // Event listener untuk modal hidden event
        $('#requestSeatModal').on('hidden.bs.modal', function() {
            // Reset variabel global saat modal ditutup
            currentScheduleId = null;
            $('#requestSeatForm')[0].reset();
            $('#seatRequestResult').html('');
        });
    }
</script>
</body>
</html>