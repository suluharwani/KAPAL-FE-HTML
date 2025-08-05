<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/process', 'Login::process');
$routes->get('/logout', 'Login::logout');
$routes->get('/dashboard', 'Dashboard::index');
$routes->group('boats',function($routes) {
    $routes->get('/', 'Boats::index');
    $routes->get('add', 'Boats::add');
    $routes->post('store', 'Boats::store');
    $routes->get('edit/(:num)', 'Boats::edit/$1');
    $routes->post('update/(:num)', 'Boats::update/$1');
    $routes->get('delete/(:num)', 'Boats::delete/$1');
    $routes->get('view/(:num)', 'Boats::view/$1');
});
$routes->group('bookings', function($routes) {
    $routes->get('/', 'Bookings::index');
    $routes->get('new', 'Bookings::new');
    $routes->post('create', 'Bookings::create');
    $routes->get('view/(:num)', 'Bookings::view/$1');
    $routes->get('cancel/(:num)', 'Bookings::cancel/$1');
    $routes->get('invoice/(:num)', 'Bookings::invoice/$1');
});
$routes->group('payments',  function($routes) {
    $routes->get('/', 'Payments::index');
    $routes->get('new/(:num)', 'Payments::new/$1'); // payment for booking ID
    $routes->post('create', 'Payments::create');
    $routes->get('view/(:num)', 'Payments::view/$1');
    $routes->post('upload-proof/(:num)', 'Payments::uploadProof/$1');
});
$routes->group('routes', function($routes) {
    $routes->get('/', 'Routes::index');
    $routes->get('add', 'Routes::add');
    $routes->post('store', 'Routes::store');
    $routes->get('edit/(:num)', 'Routes::edit/$1');
    $routes->post('update/(:num)', 'Routes::update/$1');
    $routes->get('delete/(:num)', 'Routes::delete/$1');
    $routes->get('view/(:num)', 'Routes::view/$1');
});
$routes->group('schedules',  function($routes) {
    $routes->get('/', 'Schedules::index');
    $routes->get('add', 'Schedules::add');
    $routes->post('store', 'Schedules::store');
    $routes->get('edit/(:num)', 'Schedules::edit/$1');
    $routes->post('update/(:num)', 'Schedules::update/$1');
    $routes->get('delete/(:num)', 'Schedules::delete/$1');
    $routes->get('view/(:num)', 'Schedules::view/$1');
});
$routes->group('islands', function($routes) {
    $routes->get('/', 'Islands::index');
    $routes->get('add', 'Islands::add');
    $routes->post('store', 'Islands::store');
    $routes->get('edit/(:num)', 'Islands::edit/$1');
    $routes->post('update/(:num)', 'Islands::update/$1');
    $routes->get('delete/(:num)', 'Islands::delete/$1');
    $routes->get('view/(:num)', 'Islands::view/$1');
});