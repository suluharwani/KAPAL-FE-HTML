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
                                            <option value="1">Waigeo - Misool</option>
                                            <option value="2">Waigeo - Salawati</option>
                                            <option value="3">Waigeo - Batanta</option>
                                            <option value="4">Misool - Salawati</option>
                                            <option value="5">Misool - Batanta</option>
                                            <option value="6">Salawati - Batanta</option>
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
            <div id="resultsContainer" class="row">
                <!-- Results will be displayed here -->
                <div class="no-results col-12">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p>Silakan pilih rute dan/atau tanggal untuk melihat jadwal kapal</p>
                </div>
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
            
            // Initial search to show all available schedules
            performSearch();
        });
        
        function performSearch() {
            const routeId = document.getElementById('routeSelect').value;
            const date = document.getElementById('dateInput').value;
            
            // In a real application, you would fetch data from the server
            // For this example, we'll use mock data
            const mockData = getMockScheduleData();
            
            // Filter results based on selection
            let filteredResults = mockData;
            
            if (routeId) {
                filteredResults = filteredResults.filter(schedule => schedule.routeId == routeId);
            }
            
            if (date) {
                filteredResults = filteredResults.filter(schedule => schedule.departureDate === date);
            }
            
            displayResults(filteredResults);
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
                html += `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 result-card">
                            ${schedule.isFeatured ? '<span class="feature-badge badge bg-warning"><i class="fas fa-star me-1"></i>Featured</span>' : ''}
                            <img src="${schedule.image}" class="boat-img card-img-top" alt="${schedule.boatName}">
                            <div class="card-body">
                                <h5 class="card-title">${schedule.boatName}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">${schedule.routeName}</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted"><i class="fas fa-calendar-alt me-1"></i> ${formatDate(schedule.departureDate)}</span>
                                    <span class="text-muted"><i class="fas fa-clock me-1"></i> ${schedule.departureTime}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted"><i class="fas fa-users me-1"></i> ${schedule.availableSeats} kursi tersedia</span>
                                    <span class="text-muted"><i class="fas fa-hourglass-half me-1"></i> ${schedule.duration}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="price-tag">Rp ${formatPrice(schedule.price)}</span>
                                    <button class="btn btn-outline-primary btn-sm">Pesan Sekarang</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            resultsContainer.innerHTML = html;
        }
        
        function formatDate(dateString) {
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }
        
        function formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Mock data function - in a real application, this would be an API call
        function getMockScheduleData() {
            return [
                {
                    id: 1,
                    boatName: "Speedboat Merah",
                    routeId: 1,
                    routeName: "Waigeo - Misool",
                    departureDate: "2025-09-10",
                    departureTime: "08:00",
                    duration: "2 jam",
                    availableSeats: 12,
                    price: 350000,
                    image: "https://images.unsplash.com/photo-1530533718754-001d2668365a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
                    isFeatured: true
                },
                {
                    id: 2,
                    boatName: "Kapal Biru",
                    routeId: 2,
                    routeName: "Waigeo - Salawati",
                    departureDate: "2025-09-10",
                    departureTime: "09:30",
                    duration: "1.5 jam",
                    availableSeats: 8,
                    price: 250000,
                    image: "https://images.unsplash.com/photo-1502134249126-9f3755a50d78?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
                    isFeatured: false
                },
                {
                    id: 3,
                    boatName: "Phinisi Tradisional",
                    routeId: 3,
                    routeName: "Waigeo - Batanta",
                    departureDate: "2025-09-11",
                    departureTime: "10:00",
                    duration: "3 jam",
                    availableSeats: 15,
                    price: 450000,
                    image: "https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
                    isFeatured: true
                },
                {
                    id: 4,
                    boatName: "Speedboat Express",
                    routeId: 1,
                    routeName: "Waigeo - Misool",
                    departureDate: "2025-09-11",
                    departureTime: "13:30",
                    duration: "1.75 jam",
                    availableSeats: 6,
                    price: 400000,
                    image: "https://images.unsplash.com/photo-1469796466635-455ede0284a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
                    isFeatured: false
                },
                {
                    id: 5,
                    boatName: "Kapal Wisata",
                    routeId: 4,
                    routeName: "Misool - Salawati",
                    departureDate: "2025-09-12",
                    departureTime: "14:00",
                    duration: "2.5 jam",
                    availableSeats: 20,
                    price: 300000,
                    image: "https://images.unsplash.com/photo-1501426027426-829a8163a072?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
                    isFeatured: false
                },
                {
                    id: 6,
                    boatName: "Speedboat Luxury",
                    routeId: 5,
                    routeName: "Misool - Batanta",
                    departureDate: "2025-09-12",
                    departureTime: "16:00",
                    duration: "2.25 jam",
                    availableSeats: 4,
                    price: 550000,
                    image: "https://images.unsplash.com/photo-1551632811-561732d1e306?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
                    isFeatured: true
                }
            ];
        }
    </script>
</body>
</html>