<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Kapal Raja Ampat - Open Trip & Regular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
        .search-section {
            background: linear-gradient(135deg, #ffffffff 0%, #764ba2 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            color: white;
        }
        .search-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .nav-pills .nav-link {
            border-radius: 25px;
            padding: 10px 20px;
            margin: 0 5px;
            font-weight: 600;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(45deg, #ffffffff, #aa60f3ff);
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
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        }
        .regular-trip-badge {
            background: linear-gradient(45deg, #4834d4, #686de0);
        }
        .feature-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
            background: linear-gradient(45deg, #f093fb, #f5576c);
        }
        .price-tag {
            font-size: 1.3rem;
            font-weight: bold;
            color: #198754;
        }
        .open-trip-price {
            color: #e74c3c;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .result-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .open-trip-card {
            border-left: 4px solid #e74c3c;
        }
        .regular-trip-card {
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-ship me-2"></i>Raja Ampat Boat Services
            </a>
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
                                    <button class="nav-link active" id="regular-tab" data-bs-toggle="pill" data-bs-target="#regular" type="button" role="tab">
                                        <i class="fas fa-calendar-day me-2"></i>Regular Trip
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="open-trip-tab" data-bs-toggle="pill" data-bs-target="#open-trip" type="button" role="tab">
                                        <i class="fas fa-users me-2"></i>Open Trip
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="tripTypeTabContent">
                                <!-- Regular Trip Form -->
                                <div class="tab-pane fade show active" id="regular" role="tabpanel">
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
                                <div class="tab-pane fade" id="open-trip" role="tabpanel">
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
                <!-- Regular Trips -->
                <?php if (!empty($regularSchedules)): ?>
                    <?php foreach ($regularSchedules as $schedule): ?>
                        <?php  echo view('schedule_card', ['schedule' => $schedule, 'type' => 'regular']) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Open Trips -->
                <?php if (!empty($openTripSchedules)): ?>
                    <?php foreach ($openTripSchedules as $schedule): ?>
                        <?php  echo view('schedule_card', ['schedule' => $schedule, 'type' => 'open_trip']) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (empty($regularSchedules) && empty($openTripSchedules)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Silakan pilih jenis trip, rute, dan/atau tanggal untuk melihat jadwal kapal</p>
                    </div>
                <?php endif; ?>
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
    <!-- Bootstrap & JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('regularDateInput').min = today;
            document.getElementById('openTripDateInput').min = today;
            
            // Form submission handlers
            document.getElementById('regularSearchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch('regular');
            });
            
            document.getElementById('openTripSearchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch('open_trip');
            });
            
            // Change event handlers
            document.getElementById('regularRouteSelect').addEventListener('change', function() {
                performSearch('regular');
            });
            
            document.getElementById('regularDateInput').addEventListener('change', function() {
                performSearch('regular');
            });
            
            document.getElementById('openTripRouteSelect').addEventListener('change', function() {
                performSearch('open_trip');
            });
            
            document.getElementById('openTripDateInput').addEventListener('change', function() {
                performSearch('open_trip');
            });
            
            // Tab change handler
            document.getElementById('tripTypeTab').addEventListener('shown.bs.tab', function(e) {
                const tabId = e.target.getAttribute('data-bs-target');
                if (tabId === '#open-trip') {
                    performSearch('open_trip');
                } else {
                    performSearch('regular');
                }
            });
        });
        
        function performSearch(tripType) {
            let routeId, date;
            
            if (tripType === 'open_trip') {
                routeId = document.getElementById('openTripRouteSelect').value;
                date = document.getElementById('openTripDateInput').value;
            } else {
                routeId = document.getElementById('regularRouteSelect').value;
                date = document.getElementById('regularDateInput').value;
            }
            
            // Show loading spinner
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('resultsContainer').innerHTML = '';
            
            // Fetch data from server
            fetch(`/home/searchSchedules?route=${routeId}&date=${date}&trip_type=${tripType}`)
                .then(response => response.json())
                .then(schedules => {
                    displayResults(schedules, tripType);
                    document.getElementById('loadingSpinner').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingSpinner').style.display = 'none';
                    document.getElementById('resultsContainer').innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <p class="text-danger">Terjadi kesalahan saat memuat data</p>
                        </div>
                    `;
                });
        }
        
        function displayResults(schedules, tripType) {
            const resultsContainer = document.getElementById('resultsContainer');
            
            if (schedules.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada jadwal ${tripType === 'open_trip' ? 'Open Trip' : 'Regular'} yang ditemukan</p>
                        <small class="text-muted">Coba ubah kriteria pencarian Anda</small>
                    </div>
                `;
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
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 result-card ${isOpenTrip ? 'open-trip-card' : 'regular-trip-card'}">
                            <span class="trip-badge badge ${isOpenTrip ? 'open-trip-badge' : 'regular-trip-badge'}">
                                <i class="fas ${isOpenTrip ? 'fa-users' : 'fa-calendar-day'} me-1"></i>
                                ${isOpenTrip ? 'OPEN TRIP' : 'REGULAR'}
                            </span>
                            
                            ${schedule.is_featured ? `
                                <span class="feature-badge badge">
                                    <i class="fas fa-star me-1"></i>Featured
                                </span>
                            ` : ''}
                            
                            <img src="${schedule.image_url || 'https://images.unsplash.com/photo-1530533718754-001d2668365a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'}" 
                                 class="boat-img card-img-top" 
                                 alt="${schedule.boat_name}">
                                 
                            <div class="card-body">
                                <h5 class="card-title">${schedule.boat_name}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    ${schedule.departure_island} → ${schedule.arrival_island}
                                </h6>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i> ${formattedDate}
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-clock me-1"></i> ${formattedTime}
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">
                                        <i class="fas fa-users me-1"></i> 
                                        ${schedule.available_seats} kursi tersedia
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-hourglass-half me-1"></i> 
                                        ${schedule.estimated_duration} jam
                                    </span>
                                </div>
                                
                                ${isOpenTrip && schedule.price_per_person ? `
                                    <div class="alert alert-info py-2 mb-3">
                                        <small>
                                            <i class="fas fa-info-circle me-1"></i>
                                            Harga per orang: <strong>Rp ${formatPrice(schedule.price_per_person)}</strong>
                                        </small>
                                    </div>
                                ` : ''}
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="price-tag ${isOpenTrip ? 'open-trip-price' : ''}">
                                        Rp ${formatPrice(isOpenTrip && schedule.price_per_person ? schedule.price_per_person : schedule.price_per_trip)}
                                    </span>
                                    <button class="btn ${isOpenTrip ? 'btn-danger' : 'btn-primary'} btn-sm">
                                        ${isOpenTrip ? 'Join Trip' : 'Pesan Sekarang'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            resultsContainer.innerHTML = html;
        }
        
        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        }
        // Fungsi untuk menangani klik tombol pesan
function setupBookingButtons() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('book-btn')) {
            const scheduleId = e.target.getAttribute('data-schedule-id');
            const boatName = e.target.getAttribute('data-boat-name');
            const isOpenTrip = e.target.getAttribute('data-is-open-trip') === '1';
            
            // Cek apakah user sudah login
            checkLoginStatus().then(isLoggedIn => {
                if (isLoggedIn) {
                    // Jika sudah login, buka modal booking
                    openBookingModal(scheduleId, boatName, isOpenTrip);
                } else {
                    // Jika belum login, tampilkan modal login
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                }
            }).catch(error => {
                console.error('Error:', error);
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            });
        }
    });
}

// Fungsi untuk cek status login
async function checkLoginStatus() {
    try {
        const response = await fetch('/auth/check');
        const result = await response.json();
        return result.isLoggedIn;
    } catch (error) {
        console.error('Error checking login status:', error);
        return false;
    }
}

// Fungsi untuk membuka modal booking
async function openBookingModal(scheduleId, boatName, isOpenTrip) {
    try {
        // Tampilkan loading
        document.getElementById('bookingModalContent').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat form pemesanan...</p>
            </div>
        `;

        const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
        bookingModal.show();

        // Load form booking via AJAX
        const response = await fetch(`/booking/create/${scheduleId}`);
        const html = await response.text();
        
        document.getElementById('bookingModalContent').innerHTML = html;
        
    } catch (error) {
        console.error('Error loading booking form:', error);
        document.getElementById('bookingModalContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Terjadi kesalahan saat memuat form pemesanan. Silakan coba lagi.
            </div>
        `;
    }
}

// Panggil fungsi setup saat DOM ready
document.addEventListener('DOMContentLoaded', function() {
    setupBookingButtons();
});
function setupBookingButtons() {
    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('book-btn')) {
            const button = e.target;
            const scheduleId = button.getAttribute('data-schedule-id');
            
            // Set loading state
            button.classList.add('loading');
            button.disabled = true;
            
            try {
                const isLoggedIn = await checkLoginStatus();
                
                if (isLoggedIn) {
                    window.location.href = `/booking/create/${scheduleId}`;
                } else {
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                    sessionStorage.setItem('redirectAfterLogin', `/booking/create/${scheduleId}`);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                // Remove loading state
                button.classList.remove('loading');
                button.disabled = false;
            }
        }
    });
}
// Simple implementation - langsung redirect dengan cek login
function setupBookingButtons() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('book-btn')) {
            const scheduleId = e.target.getAttribute('data-schedule-id');
            
            // Cek login sederhana (asumsi ada global variable)
            const isLoggedIn = <?= session()->get('isLoggedIn') ? 'true' : 'false' ?>;
            
            if (isLoggedIn) {
                window.location.href = `/booking/create/${scheduleId}`;
            } else {
                // Simpan URL tujuan dan redirect ke login
                sessionStorage.setItem('redirectAfterLogin', `/booking/create/${scheduleId}`);
                window.location.href = '/auth/login';
            }
        }
    });
}

// Panggil saat DOM ready
document.addEventListener('DOMContentLoaded', setupBookingButtons);
// Di halaman login (auth/login.php)
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a redirect URL
    const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
    if (redirectUrl) {
        // Simpan di form hidden
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'redirect';
        hiddenInput.value = redirectUrl;
        document.querySelector('form').appendChild(hiddenInput);
        
        // Bersihkan storage setelah digunakan
        sessionStorage.removeItem('redirectAfterLogin');
    }
});
    </script>
</body>
</html>