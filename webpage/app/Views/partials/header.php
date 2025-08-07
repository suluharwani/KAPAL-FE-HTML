<!-- app/Views/partials/header.php -->
<header>
    <!-- Top Bar -->
    <div class="top-bar bg-dark text-white py-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <i class="fas fa-phone-alt me-2"></i> +62 812-3456-7890
                    <i class="fas fa-envelope ms-3 me-2"></i> info@rajaampatboats.com
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Navbar -->
    <?= $this->include('partials/navbar') ?>
</header>