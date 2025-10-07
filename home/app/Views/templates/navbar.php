<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <img src="<?= base_url('images/logo.png') ?>" alt="Raja Ampat Boats" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= (current_url() == base_url()) ? 'active' : '' ?>" href="<?= base_url() ?>">Home</a>
                </li>
                
                <!-- Tours Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (strpos(current_url(), 'tour') !== false) ? 'active' : '' ?>" href="#" id="toursDropdown" role="button" data-bs-toggle="dropdown">
                        Tours List
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?= (current_url() == base_url('tour/waigeo')) ? 'active' : '' ?>" href="<?= base_url('tour/waigeo') ?>">Waigeo Island Tour</a></li>
                        <li><a class="dropdown-item <?= (current_url() == base_url('tour/misool')) ? 'active' : '' ?>" href="<?= base_url('tour/misool') ?>">Misool Island Tour</a></li>
                        <li><a class="dropdown-item <?= (current_url() == base_url('tour/salawati')) ? 'active' : '' ?>" href="<?= base_url('tour/salawati') ?>">Salawati Island Tour</a></li>
                        <li><a class="dropdown-item <?= (current_url() == base_url('tour/batanta')) ? 'active' : '' ?>" href="<?= base_url('tour/batanta') ?>">Batanta Island Tour</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?= (current_url() == base_url('gallery')) ? 'active' : '' ?>" href="<?= base_url('gallery') ?>">View Gallery</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= (current_url() == base_url('blog')) ? 'active' : '' ?>" href="<?= base_url('blog') ?>">Blog</a>
                </li>
                
                <!-- About Us Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (strpos(current_url(), 'about') !== false) ? 'active' : '' ?>" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown">
                        About Us
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?= (current_url() == base_url('about')) ? 'active' : '' ?>" href="<?= base_url('about') ?>">Company Profile</a></li>
                        <li><a class="dropdown-item <?= (current_url() == base_url('about/team')) ? 'active' : '' ?>" href="<?= base_url('about/team') ?>">Our Team</a></li>
                        <li><a class="dropdown-item <?= (current_url() == base_url('about/testimonials')) ? 'active' : '' ?>" href="<?= base_url('about/testimonials') ?>">Testimonials</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= (current_url() == base_url('contact')) ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= (current_url() == base_url('faq')) ? 'active' : '' ?>" href="<?= base_url('faq') ?>">FAQ</a>
                </li>
                
                <!-- Booking CTA Button -->
                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('boats/open-trip') ?>">
                            <i class="fas fa-users me-1"></i> Open Trip
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-primary" href="<?= base_url('auth/login') ?>">Login to Book</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>