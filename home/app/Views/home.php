<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Jadwal Kapal - Raja Ampat Boat Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .search-section {
            background-color: #f8f9fa;
            padding: 2rem 0;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .search-card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }
        .search-btn {
            background-color: #0d6efd;
            border: none;
            padding: 10px 20px;
        }
        .search-btn:hover {
            background-color: #0b5ed7;
        }
        .result-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .boat-img {
            height: 180px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .price-tag {
            font-size: 1.5rem;
            font-weight: bold;
            color: #198754;
        }
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .feature-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
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
                            <h2 class="card-title text-center mb-4">Cari Jadwal Kapal</h2>
                            <form id="searchForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="routeSelect" class="form-label">Rute</label>
                                        <select class="form-select" id="routeSelect">
                                            <option value="" selected>Semua Rute</option>
                                            <?php foreach ($availableRoutes as $route): ?>
                                                <option value="<?= $route['route_id'] ?>">
                                                    <?= $route['departure_island'] ?> - <?= $route['arrival_island'] ?>
                                                    (<?= $route['estimated_duration'] ?> jam)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="dateInput" class="form-label">Tanggal Keberangkatan</label>
                                        <input type="date" class="form-control" id="dateInput">
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary w-100 search-btn">
                                            <i class="fas fa-search me-2"></i>Cari Jadwal
                                        </button>
                                    </div>
                                </div>
                            </form>
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
            <div id="loadingSpinner" class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Sedang mencari jadwal...</p>
            </div>
            
            <div id="resultsContainer" class="row">
                <!-- Results will be displayed here -->
                <?php if (empty($schedules)): ?>
                    <div class="no-results col-12">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <p>Silakan pilih rute dan/atau tanggal untuk melihat jadwal kapal</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($schedules as $schedule): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 result-card">
                                <?php if ($schedule['is_featured']): ?>
                                    <span class="feature-badge badge bg-warning">
                                        <i class="fas fa-star me-1"></i>Featured
                                    </span>
                                <?php endif; ?>
                                
                                <img src="<?= $schedule['image_url'] ?: 'https://images.unsplash.com/photo-1530533718754-001d2668365a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' ?>" 
                                     class="boat-img card-img-top" 
                                     alt="<?= $schedule['boat_name'] ?>">
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= $schedule['boat_name'] ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <?= $schedule['departure_island'] ?> - <?= $schedule['arrival_island'] ?>
                                    </h6>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i> 
                                            <?= date('d M Y', strtotime($schedule['departure_date'])) ?>
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1"></i> 
                                            <?= date('H:i', strtotime($schedule['departure_time'])) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">
                                            <i class="fas fa-users me-1"></i> 
                                            <?= $schedule['available_seats'] ?> kursi tersedia
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-hourglass-half me-1"></i> 
                                            <?= $schedule['estimated_duration'] ?> jam
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="price-tag">Rp <?= number_format($schedule['price_per_trip'], 0, ',', '.') ?></span>
                                        <button class="btn btn-outline-primary btn-sm">Pesan Sekarang</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateInput').min = today;
            
            // Form submission handler
            const searchForm = document.getElementById('searchForm');
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });
            
            // Perform search when route or date changes
            document.getElementById('routeSelect').addEventListener('change', performSearch);
            document.getElementById('dateInput').addEventListener('change', performSearch);
        });
        
        function performSearch() {
            const routeId = document.getElementById('routeSelect').value;
            const date = document.getElementById('dateInput').value;
            
            // Show loading spinner
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('resultsContainer').innerHTML = '';
            
            // Fetch data from server
            fetch(`/home/searchSchedules?route=${routeId}&date=${date}`)
                .then(response => response.json())
                .then(schedules => {
                    displayResults(schedules);
                    document.getElementById('loadingSpinner').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingSpinner').style.display = 'none';
                    document.getElementById('resultsContainer').innerHTML = `
                        <div class="no-results col-12">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-danger"></i>
                            <p>Terjadi kesalahan saat memuat data</p>
                        </div>
                    `;
                });
        }
        
        function displayResults(schedules) {
            const resultsContainer = document.getElementById('resultsContainer');
            
            if (schedules.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="no-results col-12">
                        <i class="fas fa-times-circle fa-3x mb-3 text-muted"></i>
                        <p>Tidak ada jadwal yang ditemukan</p>
                        <small class="text-muted">Coba ubah kriteria pencarian Anda</small>
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            schedules.forEach(schedule => {
                const departureDate = new Date(schedule.departure_date);
                const formattedDate = departureDate.toLocaleDateString('id-ID', {
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
                        <div class="card h-100 result-card">
                            ${schedule.is_featured ? '<span class="feature-badge badge bg-warning"><i class="fas fa-star me-1"></i>Featured</span>' : ''}
                            
                            <img src="${schedule.image_url || 'https://images.unsplash.com/photo-1530533718754-001d2668365a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'}" 
                                 class="boat-img card-img-top" 
                                 alt="${schedule.boat_name}">
                                 
                            <div class="card-body">
                                <h5 class="card-title">${schedule.boat_name}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">${schedule.departure_island} - ${schedule.arrival_island}</h6>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted"><i class="fas fa-calendar-alt me-1"></i> ${formattedDate}</span>
                                    <span class="text-muted"><i class="fas fa-clock me-1"></i> ${formattedTime}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted"><i class="fas fa-users me-1"></i> ${schedule.available_seats} kursi tersedia</span>
                                    <span class="text-muted"><i class="fas fa-hourglass-half me-1"></i> ${schedule.estimated_duration} jam</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="price-tag">Rp ${formatPrice(schedule.price_per_trip)}</span>
                                    <button class="btn btn-outline-primary btn-sm">Pesan Sekarang</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            resultsContainer.innerHTML = html;
        }
        
        function formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>
</html>