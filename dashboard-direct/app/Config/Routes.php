<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default Controller
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    // Frontend Auth
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::register');
    $routes->get('logout', 'Auth::logout');
    
    // Admin Auth
    $routes->get('admin/login', 'Admin\Auth::login');
    $routes->post('admin/login', 'Admin\Auth::login');
    $routes->get('admin/logout', 'Admin\Auth::logout');
});

// Frontend Routes
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    // Home & Static Pages
    $routes->get('about', 'Home::about');
    $routes->get('contact', 'Home::contact');
    $routes->post('contact', 'Home::contact');
    $routes->get('faqs', 'Home::faqs');
    
    // Boat Routes
    $routes->get('boats', 'Home::boats');
    $routes->get('boats/(:num)', 'Home::boatDetail/$1');
    
    // Schedule Routes
    $routes->get('schedules', 'Home::schedules');
    
    // Blog Routes
    $routes->get('blogs', 'Home::blogs');
    $routes->get('blog/(:any)', 'Home::blogDetail/$1');
    
    // Gallery Routes
    $routes->get('gallery', 'Home::gallery');
    
    // Booking Routes
    $routes->group('booking', function($routes) {
        $routes->get('check-availability', 'Booking::checkAvailability');
        $routes->get('create', 'Booking::create');
        $routes->post('store', 'Booking::store');
        $routes->get('(:num)', 'Booking::show/$1');
        $routes->get('(:num)/payment', 'Booking::payment/$1');
        $routes->post('(:num)/payment', 'Booking::payment/$1');
        $routes->get('(:num)/cancel', 'Booking::cancel/$1');
    });
    
    // Open Trip Routes
    $routes->group('open-trip', function($routes) {
        $routes->get('/', 'OpenTrip::index');
        $routes->get('(:num)', 'OpenTrip::show/$1');
        $routes->get('join', 'OpenTrip::join');
        $routes->post('store', 'OpenTrip::store');
        $routes->get('request', 'OpenTrip::request');
        $routes->post('request', 'OpenTrip::storeRequest');
    });
    
    // User Profile Routes
    $routes->group('', ['filter' => 'auth'], function($routes) {
        $routes->get('profile', 'Auth::profile');
        $routes->post('profile', 'Auth::profile');
        $routes->get('my-bookings', 'Auth::bookings');
    });
});

// Admin Routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'adminAuth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    
    // Boat Management
    $routes->group('boats', function($routes) {
        $routes->get('/', 'Boats::index');
        $routes->get('add', 'Boats::add');
        $routes->post('add', 'Boats::add');
        $routes->get('edit/(:num)', 'Boats::edit/$1');
        $routes->post('edit/(:num)', 'Boats::edit/$1');
        $routes->get('delete/(:num)', 'Boats::delete/$1');
    });
    
    // Schedule Management
    $routes->group('schedules', function($routes) {
        $routes->get('/', 'Schedules::index');
        $routes->get('add', 'Schedules::add');
        $routes->post('add', 'Schedules::add');
        $routes->get('edit/(:num)', 'Schedules::edit/$1');
        $routes->post('edit/(:num)', 'Schedules::edit/$1');
        $routes->get('delete/(:num)', 'Schedules::delete/$1');
    });
    
    // Booking Management
    $routes->group('bookings', function($routes) {
        $routes->get('/', 'Booking::index');
        $routes->get('view/(:num)', 'Booking::view/$1');
        $routes->get('update-status/(:num)', 'Booking::updateStatus/$1');
        $routes->post('update-status/(:num)', 'Booking::updateStatus/$1');
        $routes->get('cancel/(:num)', 'Booking::cancel/$1');
    });
    
    // Payment Management
    $routes->group('payments', function($routes) {
        $routes->get('/', 'Payments::index');
        $routes->get('verify/(:num)', 'Payments::verify/$1');
        $routes->get('reject/(:num)', 'Payments::reject/$1');
    });
    
    // Open Trip Management
    $routes->group('open-trips', function($routes) {
        $routes->get('/', 'OpenTrip::index');
        $routes->get('approve/(:num)', 'OpenTrip::approve/$1');
        $routes->post('approve/(:num)', 'OpenTrip::approve/$1');
        $routes->get('reject/(:num)', 'OpenTrip::reject/$1');
        $routes->post('reject/(:num)', 'OpenTrip::reject/$1');
        $routes->get('view/(:num)', 'OpenTrip::viewSchedule/$1');
    });
    
    // Island Management
    $routes->group('islands', function($routes) {
        $routes->get('/', 'Island::index');
        $routes->get('add', 'Island::add');
        $routes->post('add', 'Island::add');
        $routes->get('edit/(:num)', 'Island::edit/$1');
        $routes->post('edit/(:num)', 'Island::edit/$1');
        $routes->get('delete/(:num)', 'Island::delete/$1');
    });
    
    // Blog Management
    $routes->group('blogs', function($routes) {
        $routes->get('/', 'Blog::index');
        $routes->get('add', 'Blog::add');
        $routes->post('add', 'Blog::add');
        $routes->get('edit/(:num)', 'Blog::edit/$1');
        $routes->post('edit/(:num)', 'Blog::edit/$1');
        $routes->get('delete/(:num)', 'Blog::delete/$1');
    });
    
    // Blog Category Management
    $routes->group('blog-categories', function($routes) {
        $routes->get('/', 'BlogCategory::index');
        $routes->get('add', 'BlogCategory::add');
        $routes->post('add', 'BlogCategory::add');
        $routes->get('edit/(:num)', 'BlogCategory::edit/$1');
        $routes->post('edit/(:num)', 'BlogCategory::edit/$1');
        $routes->get('delete/(:num)', 'BlogCategory::delete/$1');
    });
    
    // Gallery Management
    $routes->group('gallery', function($routes) {
        $routes->get('/', 'Gallery::index');
        $routes->get('add', 'Gallery::add');
        $routes->post('add', 'Gallery::add');
        $routes->get('toggle-featured/(:num)', 'Gallery::toggleFeatured/$1');
        $routes->get('delete/(:num)', 'Gallery::delete/$1');
    });
    
    // Testimonial Management
    $routes->group('testimonials', function($routes) {
        $routes->get('/', 'Testimonial::index');
        $routes->get('approve/(:num)', 'Testimonial::approve/$1');
        $routes->get('reject/(:num)', 'Testimonial::reject/$1');
        $routes->get('delete/(:num)', 'Testimonial::delete/$1');
    });
    
    // FAQ Management
    $routes->group('faqs', function($routes) {
        $routes->get('/', 'Faq::index');
        $routes->get('add', 'Faq::add');
        $routes->post('add', 'Faq::add');
        $routes->get('edit/(:num)', 'Faq::edit/$1');
        $routes->post('edit/(:num)', 'Faq::edit/$1');
        $routes->get('delete/(:num)', 'Faq::delete/$1');
        $routes->get('toggle-featured/(:num)', 'Faq::toggleFeatured/$1');
        $routes->post('update-order', 'Faq::updateOrder');
    });
    
    // Contact Management
    $routes->group('contacts', function($routes) {
        $routes->get('/', 'Contact::index');
        $routes->get('view/(:num)', 'Contact::view/$1');
        $routes->get('replied/(:num)', 'Contact::markAsReplied/$1');
        $routes->get('spam/(:num)', 'Contact::markAsSpam/$1');
        $routes->get('delete/(:num)', 'Contact::delete/$1');
    });
    
    // User Management
    $routes->group('users', function($routes) {
        $routes->get('/', 'User::index');
        $routes->get('add', 'User::add');
        $routes->post('add', 'User::add');
        $routes->get('edit/(:num)', 'User::edit/$1');
        $routes->post('edit/(:num)', 'User::edit/$1');
        $routes->get('delete/(:num)', 'User::delete/$1');
    });
    
    // Settings
    $routes->group('settings', function($routes) {
        $routes->get('/', 'Setting::index');
        $routes->post('/', 'Setting::index');
    });
    
    // Reports
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Reports::index');
        $routes->get('export/(:any)', 'Reports::export/$1');
        $routes->get('export-excel', 'Reports::exportExcel');
    });
    
    // Backup
    $routes->group('backup', function($routes) {
        $routes->get('/', 'Backup::index');
        $routes->get('create', 'Backup::createBackup');
        $routes->get('list', 'Backup::listBackups');
        $routes->get('download/(:any)', 'Backup::downloadBackup/$1');
        $routes->get('delete/(:any)', 'Backup::deleteBackup/$1');
    });
});

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('boats', 'Api::boats');
    $routes->get('schedules', 'Api::schedules');
    $routes->get('schedules/(:num)/(:num)', 'Api::schedules/$1/$2');
    $routes->get('blogs', 'Api::blogs');
    $routes->get('blogs/(:num)', 'Api::blogs/$1');
    $routes->get('blog/(:any)', 'Api::blogDetail/$1');
    $routes->get('testimonials', 'Api::testimonials');
    $routes->get('gallery', 'Api::gallery');
    $routes->get('gallery/(:any)', 'Api::gallery/$1');
    $routes->get('faqs', 'Api::faqs');
    $routes->get('faqs/(:any)', 'Api::faqs/$1');
    $routes->get('islands', 'Api::islands');
});

// 404 Override
$routes->set404Override(function() {
    return view('errors/html/error_404');
});

// Maintenance Mode
// $routes->setDefaultNamespace('App\Controllers');
// $routes->setDefaultController('Maintenance');
// $routes->setDefaultMethod('index');
// $routes->setTranslateURIDashes(false);
// $routes->set404Override();
// $routes->setAutoRoute(false);