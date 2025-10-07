<!-- About Hero Section -->
<section class="about-hero py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">About Raja Ampat Boat Services</h1>
                <p class="lead">Serving Your Journey in Papua's Underwater Paradise Since 2010</p>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <!-- Company Overview -->
        <div class="row mb-5">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Who We Are?</h2>
                <p class="lead">Raja Ampat Boat Services is a trusted boat transportation service provider in the Raja Ampat Islands, West Papua.</p>
                
                <p>Since our establishment in 2010, we have served thousands of domestic and international tourists who want to explore the stunning underwater and land beauty of Raja Ampat.</p>
                
                <p>We provide various types of boats from speedboats to traditional boats that are comfortable and safe for inter-island travel in Raja Ampat.</p>
                
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary rounded-circle p-3 me-3">
                            <i class="fas fa-ship fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">20+ Boats</h5>
                            <p class="text-muted mb-0">Modern and traditional boat fleet</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">10,000+ Passengers</h5>
                            <p class="text-muted mb-0">Served since 2010</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle p-3 me-3">
                            <i class="fas fa-map-marked-alt fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">15+ Routes</h5>
                            <p class="text-muted mb-0">Covering all Raja Ampat Islands</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?= base_url('images/about-1.jpg') ?>" class="img-fluid rounded shadow" alt="Raja Ampat Boat Services">
                    <div class="position-absolute top-0 start-0 mt-3 ms-3">
                        <span class="badge bg-primary bg-opacity-75 text-white fs-6">Since 2010</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-bullseye fa-3x text-primary"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Our Mission</h3>
                        <p class="mb-0">To provide safe, comfortable, and affordable boat transportation services for all tourists who want to enjoy the beauty of Raja Ampat Islands, while preserving the natural environment and local culture.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-eye fa-3x text-primary"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Our Vision</h3>
                        <p class="mb-0">To become the leading boat service provider in Raja Ampat known for excellent service, high safety standards, and commitment to sustainable tourism.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">Our Values</h2>
                <p class="lead">Principles we uphold in every service</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-shield-alt fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold">Safety</h4>
                    <p>Passenger safety is our top priority. All boats are equipped with international standard safety equipment.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-smile fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold">Comfort</h4>
                    <p>We ensure every journey is comfortable and enjoyable with professional service and well-maintained boats.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-leaf fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold">Sustainability</h4>
                    <p>We are committed to preserving Raja Ampat's natural environment through responsible tourism practices.</p>
                </div>
            </div>
        </div>

        <!-- Testimonials Section -->
        <?php if (!empty($testimonials)): ?>
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">What Our Customers Say?</h2>
                <p class="lead">Testimonials from those who have used our services</p>
            </div>
            
            <div class="col-12">
                <div class="row">
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="text-warning mb-3">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $testimonial['rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                
                                <p class="card-text fst-italic">"<?= $testimonial['content'] ?>"</p>
                                
                                <div class="d-flex align-items-center mt-3">
                                    <?php if (!empty($testimonial['image'])): ?>
                                    <img src="<?= base_url('uploads/testimonials/' . $testimonial['image']) ?>" class="rounded-circle me-3" width="50" height="50" alt="<?= $testimonial['guest_name'] ?>">
                                    <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <?= substr($testimonial['guest_name'], 0, 1) ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <h6 class="mb-0"><?= $testimonial['guest_name'] ?></h6>
                                        <?php if (!empty($testimonial['user_name'])): ?>
                                        <small class="text-muted"><?= $testimonial['user_name'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?= base_url('about/testimonials') ?>" class="btn btn-outline-primary">View All Testimonials</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="fw-bold mb-3">Ready to Start Your Adventure?</h2>
                <p class="lead mb-4">Book a boat now and explore the beauty of Raja Ampat with our best services</p>
                <a href="<?= base_url('boats') ?>" class="btn btn-primary btn-lg">Book Now</a>
            </div>
        </div>
    </div>
</section>