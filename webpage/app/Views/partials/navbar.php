<!-- app/Views/partials/navbar.php -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Raja Ampat Boats" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url() ? 'active' : '' ?>" href="<?= base_url() ?>">Home</a>
                </li>
                
                <!-- Daftar Wisata Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array(uri_string(), ['tour-waigeo', 'tour-misool', 'tour-salawati', 'tour-batanta']) ? 'active' : '' ?>" 
                       href="#" id="toursDropdown" role="button" data-bs-toggle="dropdown">
                        Daftar Wisata
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?= uri_string() == 'tour-waigeo' ? 'active' : '' ?>" 
                              href="<?= base_url('tour-waigeo') ?>">Wisata Pulau Waigeo</a></li>
                        <li><a class="dropdown-item <?= uri_string() == 'tour-misool' ? 'active' : '' ?>" 
                              href="<?= base_url('tour-misool') ?>">Wisata Pulau Misool</a></li>
                        <li><a class="dropdown-item <?= uri_string() == 'tour-salawati' ? 'active' : '' ?>" 
                              href="<?= base_url('tour-salawati') ?>">Wisata Pulau Salawati</a></li>
                        <li><a class="dropdown-item <?= uri_string() == 'tour-batanta' ? 'active' : '' ?>" 
                              href="<?= base_url('tour-batanta') ?>">Wisata Pulau Batanta</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?= uri_string() == 'tour-packages' ? 'active' : '' ?>" 
                              href="<?= base_url('tour-packages') ?>">Paket Wisata Lengkap</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'blog' ? 'active' : '' ?>" 
                       href="<?= base_url('blog') ?>">Blog</a>
                </li>
                
                <!-- Tentang Kami Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array(uri_string(), ['about', 'team', 'testimonials']) ? 'active' : '' ?>" 
                       href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown">
                        Tentang Kami
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?= uri_string() == 'about' ? 'active' : '' ?>" 
                              href="<?= base_url('about') ?>">Profil Perusahaan</a></li>
                        <li><a class="dropdown-item <?= uri_string() == 'team' ? 'active' : '' ?>" 
                              href="<?= base_url('team') ?>">Tim Kami</a></li>
                        <li><a class="dropdown-item <?= uri_string() == 'testimonials' ? 'active' : '' ?>" 
                              href="<?= base_url('testimonials') ?>">Testimonial</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" 
                       href="<?= base_url('contact') ?>">Kontak</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'faq' ? 'active' : '' ?>" 
                       href="<?= base_url('faq') ?>">FAQ</a>
                </li>
                
                <!-- Auth Links -->
                <?php if (session()->get('isLoggedIn')) : ?>
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= session()->get('full_name') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user me-2"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('booking/history') ?>"><i class="fas fa-history me-2"></i> Riwayat Booking</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else : ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-primary" href="<?= base_url('login') ?>">Login</a>
                    </li>
                    <li class="nav-item ms-lg-1">
                        <a class="btn btn-primary" href="<?= base_url('register') ?>">Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
</script>