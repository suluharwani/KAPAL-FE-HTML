<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sewa Kapal di Raja Ampat - Nikmati perjalanan tak terlupakan dengan kapal terbaik kami">
    <meta name="keywords" content="sewa kapal, raja ampat, boat rental, trip raja ampat">
    <meta name="author" content="Raja Ampat Boats">
    
    <title><?= $title ?? 'Sewa Kapal Raja Ampat' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Slick Slider CSS -->
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
    
    <!-- AOS Animation CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
                <i class="fas fa-ship me-2"></i>Raja Ampat Boats
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">
                            <i class="fas fa-home me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('boats') ?>">
                            <i class="fas fa-ship me-1"></i>Kapal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('schedules') ?>">
                            <i class="fas fa-calendar-alt me-1"></i>Jadwal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('open-trip') ?>">
                            <i class="fas fa-users me-1"></i>Open Trip
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-info-circle me-1"></i>Info
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('about') ?>">Tentang Kami</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('blogs') ?>">Blog</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('gallery') ?>">Galeri</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('faqs') ?>">FAQ</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('contact') ?>">
                            <i class="fas fa-phone me-1"></i>Kontak
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (session()->get('logged_in')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i><?= session()->get('full_name') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= base_url('profile') ?>">
                                    <i class="fas fa-user me-2"></i>Profil
                                </a></li>
                                <li><a class="dropdown-item" href="<?= base_url('bookings') ?>">
                                    <i class="fas fa-ticket-alt me-2"></i>Pemesanan Saya
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light btn-sm ms-2 px-3" href="<?= base_url('register') ?>">
                                <i class="fas fa-user-plus me-1"></i>Daftar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 70px;">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 70px;">
            <div class="container">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="margin-top: 70px;">
            <div class="container">
                <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('warning') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert" style="margin-top: 70px;">
            <div class="container">
                <i class="fas fa-info-circle me-2"></i><?= session()->getFlashdata('info') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-ship me-2"></i>Raja Ampat Boats
                    </h5>
                    <p class="mb-3">
                        Layanan sewa kapal terpercaya di Raja Ampat. Nikmati keindahan alam Papua Barat dengan kapal-kapal berkualitas dan pelayanan terbaik.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-youtube fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Navigasi</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('/') ?>" class="text-light text-decoration-none">Beranda</a></li>
                        <li><a href="<?= base_url('boats') ?>" class="text-light text-decoration-none">Kapal</a></li>
                        <li><a href="<?= base_url('schedules') ?>" class="text-light text-decoration-none">Jadwal</a></li>
                        <li><a href="<?= base_url('open-trip') ?>" class="text-light text-decoration-none">Open Trip</a></li>
                        <li><a href="<?= base_url('about') ?>" class="text-light text-decoration-none">Tentang Kami</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Informasi</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('blogs') ?>" class="text-light text-decoration-none">Blog</a></li>
                        <li><a href="<?= base_url('gallery') ?>" class="text-light text-decoration-none">Galeri</a></li>
                        <li><a href="<?= base_url('faqs') ?>" class="text-light text-decoration-none">FAQ</a></li>
                        <li><a href="<?= base_url('contact') ?>" class="text-light text-decoration-none">Kontak</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Kontak Kami</h6>
                    <div class="contact-info">
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span>Waisai, Raja Ampat, Papua Barat</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <span>+62 812-3456-7890</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <span>info@rajaampat-boats.com</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-clock me-2"></i>
                            <span>Senin - Minggu: 08:00 - 20:00</span>
                        </div>
                    </div>
                    
                    <div class="newsletter">
                        <h6 class="fw-bold mb-2">Newsletter</h6>
                        <form class="d-flex">
                            <input type="email" class="form-control me-2" placeholder="Email Anda">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> Raja Ampat Boats. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="small text-muted">
                        Powered by <i class="fas fa-heart text-danger"></i> CodeIgniter 4
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="btn btn-primary btn-floating" id="backToTop" title="Kembali ke atas">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/6281234567890?text=Halo%2C%20saya%20ingin%20bertanya%20tentang%20sewa%20kapal" 
       class="whatsapp-float" target="_blank" title="Chat WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Slick Slider JS -->
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    
    <!-- AOS Animation JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    
    <!-- Global Scripts -->
    <script>
        $(document).ready(function() {
            // Initialize AOS
            AOS.init({
                duration: 800,
                once: true
            });
            
            // Back to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#backToTop').fadeIn();
                } else {
                    $('#backToTop').fadeOut();
                }
            });
            
            $('#backToTop').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
            
            // Auto hide flash messages
            $('.alert').delay(5000).slideUp();
            
            // Form validation enhancement
            $('form').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true)
                      .html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>