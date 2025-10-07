<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                <p class="lead mb-4">We're ready to help you plan the best trip in Raja Ampat</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-telephone-fill me-2"></i>
                        <span>+62 812-3456-7890</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-envelope-fill me-2"></i>
                        <span>info@rajaampatboats.com</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-fill me-2"></i>
                        <span>24/7 Customer Service</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white py-4">
                        <h2 class="h3 mb-0 text-center text-primary">
                            <i class="bi bi-chat-dots-fill me-2"></i>Send Message
                        </h2>
                    </div>
                    <div class="card-body p-5">
                        <?php if (session()->has('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?= session('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?= session('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Error occurred:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('contact/submit') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="bi bi-person-fill me-1 text-primary"></i>Full Name *
                                    </label>
                                    <input type="text" class="form-control form-control-lg <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                           id="name" name="name" value="<?= old('name') ?>" 
                                           placeholder="Enter your full name" required>
                                    <?php if (session('errors.name')): ?>
                                        <div class="invalid-feedback"><?= session('errors.name') ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="bi bi-envelope-fill me-1 text-primary"></i>Email *
                                    </label>
                                    <input type="email" class="form-control form-control-lg <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" value="<?= old('email') ?>" 
                                           placeholder="name@email.com" required>
                                    <?php if (session('errors.email')): ?>
                                        <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="bi bi-telephone-fill me-1 text-primary"></i>Phone Number
                                </label>
                                <input type="tel" class="form-control form-control-lg <?= session('errors.phone') ? 'is-invalid' : '' ?>" 
                                       id="phone" name="phone" value="<?= old('phone') ?>" 
                                       placeholder="+62 812-3456-7890">
                                <?php if (session('errors.phone')): ?>
                                    <div class="invalid-feedback"><?= session('errors.phone') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="subject" class="form-label fw-semibold">
                                    <i class="bi bi-chat-square-text-fill me-1 text-primary"></i>Subject *
                                </label>
                                <input type="text" class="form-control form-control-lg <?= session('errors.subject') ? 'is-invalid' : '' ?>" 
                                       id="subject" name="subject" value="<?= old('subject') ?>" 
                                       placeholder="Your message subject" required>
                                <?php if (session('errors.subject')): ?>
                                    <div class="invalid-feedback"><?= session('errors.subject') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text-fill me-1 text-primary"></i>Message *
                                </label>
                                <textarea class="form-control <?= session('errors.message') ? 'is-invalid' : '' ?>" 
                                          id="message" name="message" rows="6" 
                                          placeholder="Write your message here..." required><?= old('message') ?></textarea>
                                <?php if (session('errors.message')): ?>
                                    <div class="invalid-feedback"><?= session('errors.message') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-semibold">
                                    <i class="bi bi-send-fill me-2"></i>Send Message
                                </button>
                            </div>
                            
                            <div class="text-center text-muted">
                                <small>* Required fields</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-wrapper bg-primary text-white rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; line-height: 70px;">
                            <i class="bi bi-geo-alt-fill fs-4"></i>
                        </div>
                        <h4 class="h5 mb-3">Our Address</h4>
                        <p class="text-muted mb-0">
                            Jl. Raja Ampat No. 123<br>
                            Waigeo, Raja Ampat<br>
                            West Papua, Indonesia
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-wrapper bg-primary text-white rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; line-height: 70px;">
                            <i class="bi bi-telephone-fill fs-4"></i>
                        </div>
                        <h4 class="h5 mb-3">Phone</h4>
                        <p class="text-muted mb-2">
                            <strong>Customer Service:</strong><br>
                            +62 812-3456-7890
                        </p>
                        <p class="text-muted mb-0">
                            <strong>WhatsApp:</strong><br>
                            +62 812-3456-7890
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-wrapper bg-primary text-white rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; line-height: 70px;">
                            <i class="bi bi-envelope-fill fs-4"></i>
                        </div>
                        <h4 class="h5 mb-3">Email</h4>
                        <p class="text-muted mb-2">
                            <strong>Information:</strong><br>
                            info@rajaampatboats.com
                        </p>
                        <p class="text-muted mb-0">
                            <strong>Booking:</strong><br>
                            booking@rajaampatboats.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h2 class="h1 fw-bold text-primary">Our Location</h2>
                    <p class="lead text-muted">Visit our office in Raja Ampat</p>
                </div>
                
                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        <div class="ratio ratio-16x9">
                            <!-- Google Maps Embed -->
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127672.47510729705!2d130.591313!3d-0.455999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d5f3b3b3b3b3b3b%3A0x3b3b3b3b3b3b3b3b!2sRaja%Ampat!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="h1 fw-bold mb-3">Frequently Asked Questions</h2>
                <p class="lead mb-4">Find answers to commonly asked questions</p>
                <div class="row text-start">
                    <div class="col-md-6 mb-3">
                        <h5 class="mb-2">✓ How to book a boat?</h5>
                        <p class="small opacity-75">You can book through our website or contact our customer service.</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5 class="mb-2">✓ What payment methods are available?</h5>
                        <p class="small opacity-75">Bank transfer, cash, and digital payments.</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5 class="mb-2">✓ How long is the response time?</h5>
                        <p class="small opacity-75">We will respond within 1-2 working hours.</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5 class="mb-2">✓ Is there a guarantee?</h5>
                        <p class="small opacity-75">Yes, we provide customer satisfaction guarantee.</p>
                    </div>
                </div>
                <a href="<?= base_url('faq') ?>" class="btn btn-light btn-lg mt-4">
                    <i class="bi bi-question-circle-fill me-2"></i>View Complete FAQ
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CSS Custom -->
<style>
.hero-section {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}

.icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.card:hover .icon-wrapper {
    transform: scale(1.1);
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}
</style>