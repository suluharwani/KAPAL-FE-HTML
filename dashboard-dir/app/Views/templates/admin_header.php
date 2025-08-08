<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Raja Ampat Boats Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover {
            color: rgba(255, 255, 255, 1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <a href="<?= base_url('admin/dashboard') ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-3">
                        <span class="fs-4">Raja Ampat Boats</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/boats') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/boats') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-boat me-2"></i>Boats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/blogs') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/blogs') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-journal-text me-2"></i>Blogs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/bookings') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/bookings') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-calendar-check me-2"></i>Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/contacts') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/contacts') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-envelope me-2"></i>Contacts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/faqs') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/faqs') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-question-circle me-2"></i>FAQs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/gallery') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/gallery') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-images me-2"></i>Gallery
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/islands') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/islands') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-map me-2"></i>Islands
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/open-trips') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/open-trips') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-people me-2"></i>Open Trips
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/routes') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/routes') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-signpost-split me-2"></i>Routes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/schedules') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/schedules') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-clock me-2"></i>Schedules
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/settings') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/settings') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/testimonials') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/testimonials') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-chat-square-quote me-2"></i>Testimonials
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/users') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/users') !== false) ? 'active' : '' ?>">
                                <i class="bi bi-people me-2"></i>Users
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown px-3 pb-3">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong><?= $user['name'] ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $title ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <span data-feather="calendar"></span> This week
                        </button>
                    </div>
                </div>