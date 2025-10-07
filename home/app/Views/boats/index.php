<!-- Main Content -->
<main class="container my-5">
    <!-- Image Slider -->
    <section class="mb-5">
        <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner rounded-3">
                <div class="carousel-item active">
                    <img src="<?= base_url('images/slider1.jpg') ?>" class="d-block w-100" alt="Raja Ampat 1">
                </div>
                <div class="carousel-item">
                    <img src="<?= base_url('images/slider2.jpg') ?>" class="d-block w-100" alt="Raja Ampat 2">
                </div>
                <div class="carousel-item">
                    <img src="<?= base_url('images/slider3.jpg') ?>" class="d-block w-100" alt="Raja Ampat 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <!-- Booking Form -->
    <section class="booking-form mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Book a Boat Now</h3>
            </div>
            <div class="card-body">
                <form id="boatBookingForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fromIsland" class="form-label">From Island</label>
                            <select class="form-select" id="fromIsland" required>
                                <option value="" selected disabled>Select Departure Island</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="toIsland" class="form-label">To Island</label>
                            <select class="form-select" id="toIsland" required>
                                <option value="" selected disabled>Select Destination Island</option>
                                <?php foreach ($islands as $island): ?>
                                    <option value="<?= $island['island_id'] ?>"><?= $island['island_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departureDate" class="form-label">Departure Date</label>
                            <input type="date" class="form-control" id="departureDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="passengers" class="form-label">Number of Passengers</label>
                            <input type="number" class="form-control" id="passengers" min="1" max="20" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="boatType" class="form-label">Boat Type</label>
                        <select class="form-select" id="boatType">
                            <option value="" selected disabled>Select Boat Type</option>
                            <option value="speedboat">Speedboat</option>
                            <option value="traditional">Traditional Boat</option>
                            <option value="luxury">Luxury Boat</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="roundTrip">
                            <label class="form-check-label" for="roundTrip">
                                Round Trip
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Check Schedule & Price</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section mb-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-ship fa-3x text-primary"></i>
                </div>
                <h3>Comfortable Boats</h3>
                <p>Our boats are equipped with safety equipment and passenger comfort features.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-clock fa-3x text-primary"></i>
                </div>
                <h3>On Time</h3>
                <p>Regular and punctual departure schedules for your travel comfort.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <h3>Safe & Trusted</h3>
                <p>Served by professional crew with years of experience.</p>
            </div>
        </div>
    </section>
</main>