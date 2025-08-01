<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('api-docs', 'Api\Docs::index');
$routes->get('test', 'Api\Auth::testBcrypt');
$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'cors'], function($routes) {
    // Authentication routes
    $routes->post('register', 'Auth::register');
    $routes->post('login', 'Auth::login');
    $routes->get('profile', 'Auth::profile', ['filter' => 'auth']);
    $routes->put('profile', 'Auth::updateProfile', ['filter' => 'auth']);
    $routes->post('change-password', 'Auth::changePassword', ['filter' => 'auth']);

    // Boats routes
    $routes->group('boats', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'Boats::index');
        $routes->get('(:num)', 'Boats::show/$1');
        $routes->post('/', 'Boats::create', ['filter' => 'auth:admin']);
        $routes->put('(:num)', 'Boats::update/$1', ['filter' => 'auth:admin']);
        $routes->delete('(:num)', 'Boats::delete/$1', ['filter' => 'auth:admin']);
    });

    // Blogs routes
    $routes->group('blogs', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'Blogs::index');
        $routes->get('(:num)', 'Blogs::show/$1');
        $routes->post('/', 'Blogs::create', ['filter' => 'auth:admin']);
        $routes->put('(:num)', 'Blogs::update/$1', ['filter' => 'auth:admin']);
        $routes->delete('(:num)', 'Blogs::delete/$1', ['filter' => 'auth:admin']);
        $routes->get('categories', 'Blogs::categories');
    });

    // Bookings routes
    $routes->group('bookings', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'Bookings::index');
        $routes->get('(:num)', 'Bookings::show/$1');
        $routes->post('/', 'Bookings::create');
        $routes->put('(:num)/status', 'Bookings::updateStatus/$1', ['filter' => 'auth:admin']);
        $routes->post('(:num)/cancel', 'Bookings::cancel/$1');
    });

    // Contacts routes
    $routes->group('contacts', function($routes) {
        $routes->post('/', 'Contacts::create');
        $routes->get('/', 'Contacts::index', ['filter' => 'auth:admin']);
        $routes->get('(:num)', 'Contacts::show/$1', ['filter' => 'auth:admin']);
        $routes->put('(:num)/status', 'Contacts::updateStatus/$1', ['filter' => 'auth:admin']);
    });

    // FAQs routes
    $routes->group('faqs', function($routes) {
        $routes->get('/', 'Faqs::index');
        $routes->get('featured', 'Faqs::featured');
        $routes->post('/', 'Faqs::create', ['filter' => 'auth:admin']);
        $routes->put('(:num)', 'Faqs::update/$1', ['filter' => 'auth:admin']);
        $routes->delete('(:num)', 'Faqs::delete/$1', ['filter' => 'auth:admin']);
    });

    // Gallery routes
    $routes->group('gallery', function($routes) {
        $routes->get('/', 'Gallery::index');
        $routes->get('featured', 'Gallery::featured');
        $routes->get('categories', 'Gallery::categories');
        $routes->post('/', 'Gallery::create', ['filter' => 'auth:admin']);
        $routes->delete('(:num)', 'Gallery::delete/$1', ['filter' => 'auth:admin']);
    });

    // Islands routes
    $routes->group('islands', function($routes) {
        $routes->get('/', 'Islands::index');
        $routes->get('(:num)', 'Islands::show/$1');
        $routes->post('/', 'Islands::create', ['filter' => 'auth:admin']);
        $routes->put('(:num)', 'Islands::update/$1', ['filter' => 'auth:admin']);
        $routes->delete('(:num)', 'Islands::delete/$1', ['filter' => 'auth:admin']);
    });

    // Payments routes
    $routes->group('payments', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'Payments::index');
        $routes->get('(:num)', 'Payments::show/$1');
        $routes->post('/', 'Payments::create');
        $routes->put('(:num)/status', 'Payments::updateStatus/$1', ['filter' => 'auth:admin']);
    });

    // Routes routes
    $routes->group('routes', function($routes) {
        $routes->get('/', 'Routes::index');
        $routes->get('(:num)', 'Routes::show/$1');
        $routes->post('/', 'Routes::create', ['filter' => 'auth:admin']);
        $routes->put('(:num)', 'Routes::update/$1', ['filter' => 'auth:admin']);
        $routes->delete('(:num)', 'Routes::delete/$1', ['filter' => 'auth:admin']);
    });

    // Schedules routes
    $routes->group('schedules', function($routes) {
        $routes->get('/', 'Schedules::index');
        $routes->get('(:num)', 'Schedules::show/$1');
        $routes->post('/', 'Schedules::create', ['filter' => 'auth:admin']);
        $routes->put('(:num)', 'Schedules::update/$1', ['filter' => 'auth:admin']);
        $routes->delete('(:num)', 'Schedules::delete/$1', ['filter' => 'auth:admin']);
    });

    // Settings routes
    $routes->group('settings', ['filter' => 'auth:admin'], function($routes) {
        $routes->get('/', 'Settings::index');
        $routes->get('(:any)', 'Settings::show/$1');
        $routes->post('(:any)', 'Settings::update/$1');
    });

    // Testimonials routes
    $routes->group('testimonials', function($routes) {
        $routes->get('/', 'Testimonials::index');
        $routes->get('approved', 'Testimonials::approved');
        $routes->post('/', 'Testimonials::create', ['filter' => 'auth']);
        $routes->put('(:num)/status', 'Testimonials::updateStatus/$1', ['filter' => 'auth:admin']);
    });
    $routes->group('open-trips', ['filter' => 'auth'], function($routes) {
        $routes->post('request', 'OpenTrips::createRequest');
        $routes->put('request/(:num)/approve', 'OpenTrips::approveRequest/$1', ['filter' => 'auth:admin']);
        $routes->get('available', 'OpenTrips::listAvailable');
        $routes->post('(:num)/reserve', 'OpenTrips::bookReservedSeat/$1');
        $routes->post('(:num)/join', 'OpenTrips::joinOpenTrip/$1');
    });
});