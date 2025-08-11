<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth Routes
// Admin Routes
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin::dashboard');
    
    // Boats
    $routes->group('boats', function($routes) {
        $routes->get('/', 'Admin::boats');
        $routes->get('create', 'Admin::createBoat');
        $routes->post('store', 'Admin::storeBoat');
        $routes->get('edit/(:num)', 'Admin::editBoat/$1');
        $routes->post('update/(:num)', 'Admin::updateBoat/$1');
        $routes->get('delete/(:num)', 'Admin::deleteBoat/$1');
    });
    
    // Blogs
    $routes->group('blogs', function($routes) {
        $routes->get('/', 'BlogController::index');
        $routes->get('create', 'BlogController::create');
        $routes->post('store', 'BlogController::store');
        $routes->get('edit/(:num)', 'BlogController::edit/$1');
        $routes->post('update/(:num)', 'BlogController::update/$1');
        $routes->get('delete/(:num)', 'BlogController::delete/$1');
    });
    
    // Bookings
    $routes->group('bookings', function($routes) {
        $routes->get('/', 'BookingController::index');
        $routes->get('(:num)', 'BookingController::show/$1');
        $routes->post('(:num)/status', 'BookingController::updateStatus/$1');
        $routes->post('(:num)/payment-status', 'BookingController::updatePaymentStatus/$1');
    });
    
    // Contacts
    $routes->group('contacts', function($routes) {
        $routes->get('/', 'ContactController::index');
        $routes->get('(:num)', 'ContactController::show/$1');
        $routes->post('(:num)/status', 'ContactController::updateStatus/$1');
        $routes->get('delete/(:num)', 'ContactController::delete/$1');
    });
    
    // FAQs
    $routes->group('faqs', function($routes) {
        $routes->get('/', 'FaqController::index');
        $routes->get('create', 'FaqController::create');
        $routes->post('store', 'FaqController::store');
        $routes->get('edit/(:num)', 'FaqController::edit/$1');
        $routes->post('update/(:num)', 'FaqController::update/$1');
        $routes->get('delete/(:num)', 'FaqController::delete/$1');
    });
    
    // Gallery
    $routes->group('gallery', function($routes) {
        $routes->get('/', 'GalleryController::index');
        $routes->get('create', 'GalleryController::create');
        $routes->post('store', 'GalleryController::store');
        $routes->get('delete/(:num)', 'GalleryController::delete/$1');
    });
    
    // Islands
    $routes->group('islands', function($routes) {
        $routes->get('/', 'IslandController::index');
        $routes->get('create', 'IslandController::create');
        $routes->post('store', 'IslandController::store');
        $routes->get('edit/(:num)', 'IslandController::edit/$1');
        $routes->post('update/(:num)', 'IslandController::update/$1');
        $routes->get('delete/(:num)', 'IslandController::delete/$1');
    });
    
    // Open Trips
    $routes->group('open-trips', function($routes) {
        $routes->get('/', 'OpenTripController::index');
        $routes->get('create', 'OpenTripController::create');
        $routes->post('store', 'OpenTripController::store');
        $routes->get('(:num)', 'OpenTripController::show/$1');
        $routes->post('(:num)/status/(:any)', 'OpenTripController::updateStatus/$1/$2');
        $routes->get('(:num)/status/(:any)', 'OpenTripController::updateStatus/$1/$2');
    });
    
    
    // Routes
    $routes->group('routes', function($routes) {
        $routes->get('/', 'RouteController::index');
        $routes->get('create', 'RouteController::create');
        $routes->post('store', 'RouteController::store');
        $routes->get('edit/(:num)', 'RouteController::edit/$1');
        $routes->post('update/(:num)', 'RouteController::update/$1');
        $routes->get('delete/(:num)', 'RouteController::delete/$1');
    });
    
    // Schedules
    $routes->group('schedules', function($routes) {
        $routes->get('/', 'ScheduleController::index');
        $routes->get('create', 'ScheduleController::create');
        $routes->post('store', 'ScheduleController::store');
        $routes->get('edit/(:num)', 'ScheduleController::edit/$1');
        $routes->post('update/(:num)', 'ScheduleController::update/$1');
        $routes->get('delete/(:num)', 'ScheduleController::delete/$1');
    });
    
    // Settings
    $routes->group('settings', function($routes) {
        $routes->get('/', 'SettingController::index');
        $routes->post('update', 'SettingController::update');
    });
    
    // Testimonials
    $routes->group('testimonials', function($routes) {
        $routes->get('/', 'TestimonialController::index');
        $routes->post('(:num)/status', 'TestimonialController::updateStatus/$1');
        $routes->get('delete/(:num)', 'TestimonialController::delete/$1');
    });
    
    // Users
    $routes->group('users', function($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('create', 'UserController::create');
        $routes->post('store', 'UserController::store');
        $routes->get('edit/(:num)', 'UserController::edit/$1');
        $routes->post('update/(:num)', 'UserController::update/$1');
        $routes->get('delete/(:num)', 'UserController::delete/$1');
    });
});

// Auth Routes
$routes->group('', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::register');
});
// Request Open Trips
$routes->group('admin/request-open-trips', function($routes) {
    $routes->get('/', 'RequestOpenTripController::index');
    $routes->get('(:num)', 'RequestOpenTripController::show/$1');
    $routes->post('(:num)/status/(:any)', 'RequestOpenTripController::updateStatus/$1/$2');
});
