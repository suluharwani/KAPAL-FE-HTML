<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raja Ampat Boats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar {
            padding: 0.5rem 0;
        }
        .navbar-brand {
            padding: 0;
        }
        .nav-item {
            margin: 0 0.2rem;
        }
        .nav-link {
            font-weight: 500;
            padding: 0.8rem 1rem !important;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd !important;
        }
        .dropdown-item {
            padding: 0.6rem 1.2rem;
            transition: all 0.2s ease;
        }
        .dropdown-item:hover, .dropdown-item.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .btn-login {
            padding: 0.5rem 1.2rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        .user-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        @media (max-width: 991.98px) {
            .user-actions {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem 0;
                gap: 0.5rem;
            }
            .nav-item {
                margin: 0.1rem 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/images/logo.png" alt="Raja Ampat Boats" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == base_url()) ? 'active' : '' ?>" href="/">Home</a>
                    </li>
                    
                    <!-- Daftar Wisata Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (strpos(current_url(), 'tour') !== false) ? 'active' : '' ?>" href="#" id="toursDropdown" role="button" data-bs-toggle="dropdown">
                            Daftar Wisata
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?= (current_url() == base_url('tour/waigeo')) ? 'active' : '' ?>" href="/tour/waigeo">Wisata Pulau Waigeo</a></li>
                            <li><a class="dropdown-item <?= (current_url() == base_url('tour/misool')) ? 'active' : '' ?>" href="/tour/misool">Wisata Pulau Misool</a></li>
                            <li><a class="dropdown-item <?= (current_url() == base_url('tour/salawati')) ? 'active' : '' ?>" href="/tour/salawati">Wisata Pulau Salawati</a></li>
                            <li><a class="dropdown-item <?= (current_url() == base_url('tour/batanta')) ? 'active' : '' ?>" href="/tour/batanta">Wisata Pulau Batanta</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item <?= (current_url() == base_url('gallery')) ? 'active' : '' ?>" href="/gallery">Lihat Galeri</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == base_url('blog')) ? 'active' : '' ?>" href="/blog">Blog</a>
                    </li>
                    
                    <!-- Tentang Kami Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (strpos(current_url(), 'about') !== false) ? 'active' : '' ?>" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown">
                            Tentang Kami
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?= (current_url() == base_url('about')) ? 'active' : '' ?>" href="/about">Profil Perusahaan</a></li>
                            <li><a class="dropdown-item <?= (current_url() == base_url('about/team')) ? 'active' : '' ?>" href="/about/team">Tim Kami</a></li>
                            <li><a class="dropdown-item <?= (current_url() == base_url('about/testimonials')) ? 'active' : '' ?>" href="/about/testimonials">Testimonial</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == base_url('contact')) ? 'active' : '' ?>" href="/contact">Kontak</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == base_url('faq')) ? 'active' : '' ?>" href="/faq">FAQ</a>
                    </li>
                </ul>
                
                <!-- User Actions -->
                <div class="user-actions">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a class="btn btn-outline-primary btn-sm" href="/profile">
                            <i class="fas fa-user me-1"></i> Profil
                        </a>
                        <a class="btn btn-outline-primary btn-sm" href="/boats/open-trip">
                            <i class="fas fa-users me-1"></i> Open Trip
                        </a>
                        <a class="btn btn-outline-secondary btn-sm" href="/auth/logout">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a class="btn btn-primary btn-login" href="/auth/login">
                            <i class="fas fa-sign-in-alt me-1"></i> Login untuk Pesan
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>