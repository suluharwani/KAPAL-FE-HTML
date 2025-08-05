<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Raja Ampat Boats</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 56px;
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 1000;
        }
        
        #main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: calc(100vh - var(--topbar-height));
            margin-top: var(--topbar-height);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            position: relative;
        }
        
        .sidebar-menu li a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: rgba(13, 110, 253, 0.1);
            border-left: 3px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-brand {
            padding: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            display: block;
            text-align: center;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }
        
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 999;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .user-dropdown img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
        
        .submenu {
            list-style: none;
            padding-left: 30px;
            display: none;
        }
        
        .submenu.show {
            display: block;
        }
        
        .has-submenu::after {
            content: "\f282";
            font-family: bootstrap-icons;
            position: absolute;
            right: 15px;
            top: 12px;
            transition: transform 0.3s;
        }
        
        .has-submenu.collapsed::after {
            transform: rotate(-90deg);
        }
        
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
            
            #main-content {
                margin-left: 0;
            }
            
            .topbar {
                left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar">
        <div class="sidebar-brand text-primary">
            Raja Ampat Boats
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?= base_url('dashboard') ?>" class="<?= current_url() == base_url('dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <!-- Boats Management -->
            <li class="has-submenu <?= strpos(current_url(), 'boats') !== false ? 'collapsed' : '' ?>">
                <a href="#boats-submenu" data-bs-toggle="collapse">
                    <i class="bi bi-boat"></i> Kapal
                </a>
                <ul id="boats-submenu" class="submenu <?= strpos(current_url(), 'boats') !== false ? 'show' : '' ?>">
                    <li><a href="<?=base_url('boats')?>">Daftar Kapal</a></li>
                    <li><a href="<?=base_url('boats/add') ?>">Tambah Kapal</a></li>
                </ul>
            </li>
            
            <!-- Bookings -->
            <li class="has-submenu <?= strpos(current_url(), 'bookings') !== false ? 'collapsed' : '' ?>">
                <a href="#bookings-submenu" data-bs-toggle="collapse">
                    <i class="bi bi-journal-bookmark"></i> Pemesanan
                </a>
                <ul id="bookings-submenu" class="submenu <?= strpos(current_url(), 'bookings') !== false ? 'show' : '' ?>">
                    <li><a href="<?= base_url('bookings') ?>">Semua Pemesanan</a></li>
                    <li><a href="<?= base_url('bookings/new') ?>">Pemesanan Baru</a></li>
                </ul>
            </li>
            
            <!-- Payments -->
            <li>
                <a href="<?= base_url('payments') ?>" class="<?= current_url() == base_url('payments') ? 'active' : '' ?>">
                    <i class="bi bi-credit-card"></i> Pembayaran
                </a>
            </li>
            
            <!-- Routes -->
            <li class="has-submenu <?= strpos(current_url(), 'routes') !== false ? 'collapsed' : '' ?>">
                <a href="#routes-submenu" data-bs-toggle="collapse">
                    <i class="bi bi-signpost-split"></i> Rute
                </a>
                <ul id="routes-submenu" class="submenu <?= strpos(current_url(), 'routes') !== false ? 'show' : '' ?>">
                    <li><a href="<?= base_url('routes') ?>">Daftar Rute</a></li>
                    <li><a href="<?= base_url('routes/add') ?>">Tambah Rute</a></li>
                </ul>
            </li>
            
            <!-- Schedules -->
            <li class="has-submenu <?= strpos(current_url(), 'schedules') !== false ? 'collapsed' : '' ?>">
                <a href="#schedules-submenu" data-bs-toggle="collapse">
                    <i class="bi bi-calendar-event"></i> Jadwal
                </a>
                <ul id="schedules-submenu" class="submenu <?= strpos(current_url(), 'schedules') !== false ? 'show' : '' ?>">
                    <li><a href="<?= base_url('schedules') ?>">Daftar Jadwal</a></li>
                    <li><a href="<?= base_url('schedules/add') ?>">Buat Jadwal</a></li>
                </ul>
            </li>
            
            <!-- Islands -->
            <li class="has-submenu <?= strpos(current_url(), 'islands') !== false ? 'collapsed' : '' ?>">
                <a href="#islands-submenu" data-bs-toggle="collapse">
                    <i class="bi bi-map"></i> Pulau
                </a>
                <ul id="islands-submenu" class="submenu <?= strpos(current_url(), 'islands') !== false ? 'show' : '' ?>">
                    <li><a href="<?= base_url('islands') ?>">Daftar Pulau</a></li>
                    <li><a href="<?= base_url('islands/add') ?>">Tambah Pulau</a></li>
                </ul>
            </li>
            
            <!-- Open Trips -->
            <li class="has-submenu <?= strpos(current_url(), 'open-trips') !== false ? 'collapsed' : '' ?>">
                <a href="#opentrips-submenu" data-bs-toggle="collapse">
                    <i class="bi bi-people"></i> Open Trip
                </a>
                <ul id="opentrips-submenu" class="submenu <?= strpos(current_url(), 'open-trips') !== false ? 'show' : '' ?>">
                    <li><a href="<?= base_url('open-trip') ?>">Daftar Open Trip</a></li>
                    <li><a href="<?= base_url('open-trip/request') ?>">Request Open Trip</a></li>
                </ul>
            </li>
            
            <!-- Gallery -->
            <li>
                <a href="<?= base_url('gallery') ?>" class="<?= current_url() == base_url('gallery') ? 'active' : '' ?>">
                    <i class="bi bi-images"></i> Galeri
                </a>
            </li>
            
            <!-- FAQs -->
            <li>
                <a href="<?= base_url('faqs') ?>" class="<?= current_url() == base_url('faqs') ? 'active' : '' ?>">
                    <i class="bi bi-question-circle"></i> FAQ
                </a>
            </li>
            
            <!-- Testimonials -->
            <li>
                <a href="<?= base_url('testimonials') ?>" class="<?= current_url() == base_url('testimonials') ? 'active' : '' ?>">
                    <i class="bi bi-chat-square-quote"></i> Testimoni
                </a>
            </li>
            
           <!-- Admin Only Sections -->
<?php if (session()->get('role') == 'admin'): ?>
    <li class="mt-3 border-top pt-2">
        <small class="text-muted px-3">Admin Menu</small>
    </li>
    
    <li>
        <a href="<?= base_url('reports') ?>" class="<?= current_url() == base_url('reports') ? 'active' : '' ?>">
            <i class="bi bi-bar-chart"></i> Laporan
        </a>
    </li>
<?php endif; ?>
        </ul>
    </div>

    <!-- Topbar -->
    <nav class="topbar navbar navbar-expand navbar-light px-3">
        <div class="container-fluid">
            <button class="btn btn-sm d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= session()->get('username')?? 'User' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="main-content">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Activate current menu item
        document.querySelectorAll('.sidebar-menu a').forEach(item => {
            if (item.href === window.location.href) {
                item.classList.add('active');
                
                // Expand parent menu if exists
                let parentMenu = item.closest('.submenu');
                if (parentMenu) {
                    parentMenu.classList.add('show');
                    parentMenu.previousElementSibling.classList.remove('collapsed');
                }
            }
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Terjadi Kesalahan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="errorModalBody">
                <!-- Error message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk menampilkan error modal
function showErrorModal(message) {
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    document.getElementById('errorModalBody').innerHTML = message;
    errorModal.show();
}

// Cek jika ada flash error dan tampilkan modal
<?php if (session()->getFlashdata('error')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showErrorModal(`<?= session()->getFlashdata('error') ?>`);
    });
<?php endif; ?>

// Tangkap error dari AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Intercept form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            if (this.dataset.ajaxSubmit !== 'true') return;
            
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            try {
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    // Success handling (redirect or other action)
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    // Show error message
                    showErrorModal(result.message || 'Terjadi kesalahan saat memproses permintaan');
                }
            } catch (error) {
                showErrorModal('Koneksi error: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    });
});
</script>
</body>
</html>